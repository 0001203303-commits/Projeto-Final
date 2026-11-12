<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class GerenciadorBanco
{
    /** Tabelas com dados de pacientes (recebem etiqueta de alerta na tela). */
    private const SENSIVEIS = ['pacientes', 'triagens', 'triagem_respostas', 'triagem_sintomas', 'triagem_comentarios'];

    private const PADRAO_BACKUP = '/^smart_triage_\d{8}_\d{6}\.sql$/';

    public static function bytes(int|float $b): string
    {
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($b >= 1024 && $i < count($unidades) - 1) {
            $b /= 1024;
            $i++;
        }

        return ($i === 0 ? (int) $b : number_format($b, 1, ',', '.')) . ' ' . $unidades[$i];
    }

    // ------------------------------------------------------------------
    // Monitoramento
    // ------------------------------------------------------------------
    public function status(): array
    {
        $inicio = microtime(true);

        try {
            $conexao = DB::connection();
            $versao = DB::selectOne('select version() as v')->v;
            $ms = (int) round((microtime(true) - $inicio) * 1000);
            $banco = $conexao->getDatabaseName();

            $tamanho = DB::selectOne(
                'select coalesce(sum(data_length + index_length), 0) as t from information_schema.tables where table_schema = ?',
                [$banco]
            )->t;

            return [
                'ok'          => true,
                'versao'      => $versao,
                'latencia_ms' => $ms,
                'banco'       => $banco,
                'servidor'    => $conexao->getConfig('host') . ':' . $conexao->getConfig('port'),
                'usuario'     => $conexao->getConfig('username'),
                'charset'     => $conexao->getConfig('charset'),
                'tamanho'     => (int) $tamanho,
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'erro' => $e->getMessage()];
        }
    }

    /** Só nomes e totais: nunca o conteúdo das tabelas. */
    public function tabelas(): array
    {
        try {
            $banco = DB::connection()->getDatabaseName();

            $infos = DB::select(
                "select table_name as nome, (data_length + index_length) as tamanho
                 from information_schema.tables
                 where table_schema = ? and table_type = 'BASE TABLE'
                 order by table_name",
                [$banco]
            );
        } catch (\Throwable) {
            return [];
        }

        return collect($infos)->map(function ($t) {
            try {
                $linhas = DB::table($t->nome)->count();
            } catch (\Throwable) {
                $linhas = null;
            }

            return [
                'nome'     => $t->nome,
                'linhas'   => $linhas,
                'tamanho'  => (int) $t->tamanho,
                'sensivel' => in_array($t->nome, self::SENSIVEIS, true),
            ];
        })->all();
    }

    // ------------------------------------------------------------------
    // Migrations
    // ------------------------------------------------------------------
    public function migrations(): array
    {
        try {
            $arquivos = array_keys(app('migrator')->getMigrationFiles(database_path('migrations')));
            $aplicadas = DB::table('migrations')->pluck('batch', 'migration');
        } catch (\Throwable) {
            return ['aplicadas' => 0, 'pendentes' => 0, 'lista' => []];
        }

        $lista = collect($arquivos)
            ->map(fn ($nome) => ['nome' => $nome, 'batch' => $aplicadas->get($nome)])
            ->all();

        $pendentes = count(array_filter($lista, fn ($m) => $m['batch'] === null));

        return ['aplicadas' => count($lista) - $pendentes, 'pendentes' => $pendentes, 'lista' => $lista];
    }

    public function aplicarMigrations(): array
    {
        try {
            $codigo = Artisan::call('migrate', ['--force' => true]);

            return ['ok' => $codigo === 0, 'saida' => trim(Artisan::output())];
        } catch (\Throwable $e) {
            return ['ok' => false, 'saida' => $e->getMessage()];
        }
    }

    // ------------------------------------------------------------------
    // Backups
    // ------------------------------------------------------------------
    public function backups(): array
    {
        return collect(File::files($this->pasta()))
            ->filter(fn ($f) => preg_match(self::PADRAO_BACKUP, $f->getFilename()))
            ->map(fn ($f) => [
                'nome'    => $f->getFilename(),
                'tamanho' => $f->getSize(),
                'data'    => Carbon::createFromTimestamp($f->getMTime(), config('app.timezone')),
            ])
            ->sortByDesc('data')
            ->values()
            ->all();
    }

    public function criarBackup(): array
    {
        $conf = DB::connection()->getConfig();
        $nome = 'smart_triage_' . now()->format('Ymd_His') . '.sql';
        $caminho = $this->pasta() . DIRECTORY_SEPARATOR . $nome;

        // A senha vai por variável de ambiente, não na linha de comando
        $env = filled($conf['password'] ?? null) ? ['MYSQL_PWD' => (string) $conf['password']] : [];

        $processo = new Process([
            config('triagem.mysqldump', 'mysqldump'),
            '--host=' . $conf['host'],
            '--port=' . $conf['port'],
            '--user=' . $conf['username'],
            '--single-transaction',
            '--routines',
            '--no-tablespaces',
            '--default-character-set=utf8mb4',
            $conf['database'],
        ], null, $env);

        $processo->setTimeout(180);

        $arquivo = fopen($caminho, 'w');

        try {
            $processo->run(function ($tipo, $buffer) use ($arquivo) {
                if ($tipo === Process::OUT) {
                    fwrite($arquivo, $buffer);
                }
            });
        } catch (\Throwable $e) {
            fclose($arquivo);
            @unlink($caminho);

            return ['ok' => false, 'erro' => $e->getMessage()];
        }

        fclose($arquivo);

        if (! $processo->isSuccessful() || filesize($caminho) === 0) {
            @unlink($caminho);

            return ['ok' => false, 'erro' => trim($processo->getErrorOutput()) ?: 'O mysqldump não retornou dados.'];
        }

        return ['ok' => true, 'arquivo' => $nome];
    }

    public function caminhoDoBackup(string $arquivo): ?string
    {
        if (! preg_match(self::PADRAO_BACKUP, $arquivo)) {
            return null;
        }

        $caminho = $this->pasta() . DIRECTORY_SEPARATOR . $arquivo;

        return File::exists($caminho) ? $caminho : null;
    }

    public function excluirBackup(string $arquivo): bool
    {
        $caminho = $this->caminhoDoBackup($arquivo);

        return $caminho !== null && File::delete($caminho);
    }

    private function pasta(): string
    {
        $pasta = storage_path('app/backups');
        File::ensureDirectoryExists($pasta);

        return $pasta;
    }
}