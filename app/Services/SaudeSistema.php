<?php

namespace App\Services;

use App\Models\Totem;
use App\Models\Triagem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaudeSistema
{
    public function coletar(): array
    {
        $dados = [
            'banco'     => $this->banco(),
            'ia'        => $this->ia(),
            'totens'    => $this->totens(),
            'atividade' => $this->atividade(),
            'sistema'   => $this->sistema(),
            'erros'     => $this->errosRecentes(),
            'gerado_em' => now(),
        ];

        $dados['alertas'] = $this->alertas($dados);

        return $dados;
    }

    // ------------------------------------------------------------------

    private function banco(): array
    {
        $inicio = microtime(true);

        try {
            DB::select('select 1');
            $ms = (int) round((microtime(true) - $inicio) * 1000);
        } catch (\Throwable) {
            return ['ok' => false, 'ms' => null, 'versao' => null];
        }

        try {
            $versao = DB::selectOne('select version() as v')->v ?? null;
        } catch (\Throwable) {
            $versao = null;
        }

        return ['ok' => true, 'ms' => $ms, 'versao' => $versao];
    }

    private function ia(): array
    {
        $padrao = [
            'configurada' => filled(config('services.gemini.key')),
            'modelo'      => config('services.gemini.model'),
            'total'       => 0,
            'com_ia'      => 0,
            'locais'      => 0,
            'taxa'        => null,
            'ultima'      => null,
        ];

        try {
            $desde = now()->subDay();
            $limite = now()->subMinutes(2); // dá tempo da IA terminar

            $base = fn () => Triagem::whereNotNull('cor')
                ->where('created_at', '>=', $desde)
                ->where('created_at', '<=', $limite);

            $total = $base()->count();
            $comIa = $base()->where('resumo_ia', 'like', '%"origem":"gemini"%')->count();

            $padrao['total'] = $total;
            $padrao['com_ia'] = $comIa;
            $padrao['locais'] = $total - $comIa;
            $padrao['taxa'] = $total ? (int) round($comIa / $total * 100) : null;
        } catch (\Throwable) {
            return $padrao;
        }

        try {
            $texto = Triagem::where('resumo_ia', 'like', '%"origem":"gemini"%')
                ->selectRaw("MAX(JSON_UNQUOTE(JSON_EXTRACT(resumo_ia, '$.gerado_em'))) as ultima")
                ->value('ultima');

            $padrao['ultima'] = $texto ? Carbon::parse($texto)->timezone(config('app.timezone')) : null;
        } catch (\Throwable) {
            // a última geração é informação complementar
        }

        return $padrao;
    }

    private function totens(): array
    {
        $padrao = ['total' => 0, 'ativos' => 0, 'online' => 0, 'offline' => 0, 'nunca' => 0, 'inativos' => 0, 'offline_lista' => collect()];

        try {
            $todos = Totem::orderBy('nome')->get();
        } catch (\Throwable) {
            return $padrao;
        }

        $contagem = fn (string $st) => $todos->filter(fn ($t) => $t->status_conexao === $st)->count();

        return [
            'total'         => $todos->count(),
            'ativos'        => $todos->where('ativo', true)->count(),
            'online'        => $contagem('online'),
            'offline'       => $contagem('offline'),
            'nunca'         => $contagem('nunca'),
            'inativos'      => $contagem('inativo'),
            'offline_lista' => $todos->filter(fn ($t) => $t->status_conexao === 'offline')->values(),
        ];
    }

    private function atividade(): array
    {
        try {
            return [
                'triagens_24h' => Triagem::whereNotNull('cor')->where('created_at', '>=', now()->subDay())->count(),
                'ultima'       => Triagem::whereNotNull('cor')->max('created_at'),
            ];
        } catch (\Throwable) {
            return ['triagens_24h' => 0, 'ultima' => null];
        }
    }

    private function sistema(): array
    {
        $pasta = storage_path();
        $log = storage_path('logs/laravel.log');
        $livre = @disk_free_space($pasta);
        $total = @disk_total_space($pasta);

        return [
            'laravel'     => app()->version(),
            'php'         => PHP_VERSION,
            'ambiente'    => app()->environment(),
            'debug'       => (bool) config('app.debug'),
            'timezone'    => config('app.timezone'),
            'hora'        => now(),
            'disco_livre' => $livre ?: null,
            'disco_total' => $total ?: null,
            'log_bytes'   => is_file($log) ? filesize($log) : 0,
            'cache'       => config('cache.default'),
            'sessao'      => config('session.driver'),
        ];
    }

    /**
     * Lê só o final do log. Apenas os registros do Gemini mostram o texto: os demais
     * erros podem conter dados de pacientes (ex.: SQL com valores) e ficam resumidos.
     */
    private function errosRecentes(int $max = 8): array
    {
        $vazio = ['itens' => [], 'total_24h' => 0, 'gemini_24h' => 0];
        $arquivo = storage_path('logs/laravel.log');

        if (! is_file($arquivo) || ! is_readable($arquivo)) {
            return $vazio;
        }

        $leitura = min(filesize($arquivo), 262144);
        if ($leitura === 0) {
            return $vazio;
        }

        $h = fopen($arquivo, 'rb');
        fseek($h, -$leitura, SEEK_END);
        $texto = (string) stream_get_contents($h);
        fclose($h);

        preg_match_all(
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(WARNING|ERROR|CRITICAL|ALERT|EMERGENCY): (.*)$/m',
            $texto,
            $blocos,
            PREG_SET_ORDER
        );

        $desde = now()->subDay();
        $itens = [];
        $total = 0;
        $gemini = 0;

        foreach (array_reverse($blocos) as $b) {
            $quando = Carbon::createFromFormat('Y-m-d H:i:s', $b[1], config('app.timezone'));

            if ($quando->lt($desde)) {
                break;
            }

            $total++;
            $ehGemini = Str::startsWith($b[3], 'Gemini');
            if ($ehGemini) {
                $gemini++;
            }

            if (count($itens) < $max) {
                $itens[] = [
                    'quando' => $quando,
                    'nivel'  => $b[2],
                    'gemini' => $ehGemini,
                    'texto'  => $ehGemini
                        ? Str::limit(trim($b[3]), 220)
                        : 'Erro interno do sistema (detalhes no arquivo de log do servidor).',
                ];
            }
        }

        return ['itens' => $itens, 'total_24h' => $total, 'gemini_24h' => $gemini];
    }

    private function alertas(array $d): array
    {
        $a = [];

        if (! $d['banco']['ok']) {
            $a[] = ['nivel' => 'critico', 'texto' => 'Sem conexão com o banco de dados.', 'link' => null];
        }

        if ($d['totens']['offline'] > 0) {
            $n = $d['totens']['offline'];
            $a[] = [
                'nivel' => 'aviso',
                'texto' => $n . ($n === 1 ? ' totem está offline.' : ' totens estão offline.'),
                'link'  => ['rotulo' => 'Ver totens →', 'url' => route('painel.totens.index')],
            ];
        }

        if (! $d['ia']['configurada']) {
            $a[] = ['nivel' => 'aviso', 'texto' => 'IA não configurada (chave ausente). Os resumos locais continuam funcionando.', 'link' => null];
        } elseif ($d['ia']['taxa'] !== null && $d['ia']['total'] >= 3 && $d['ia']['taxa'] < 50) {
            $a[] = ['nivel' => 'aviso', 'texto' => 'Menos da metade dos resumos das últimas 24 h foi gerada pela IA. Verifique a chave, a cota e a conexão com a internet.', 'link' => null];
        }

        if ($d['sistema']['debug'] && $d['sistema']['ambiente'] !== 'local') {
            $a[] = ['nivel' => 'critico', 'texto' => "Modo debug ligado no ambiente \"{$d['sistema']['ambiente']}\". Defina APP_DEBUG=false antes de qualquer uso real.", 'link' => null];
        }

        $s = $d['sistema'];
        if ($s['disco_total'] && $s['disco_livre'] / $s['disco_total'] < 0.10) {
            $a[] = ['nivel' => 'aviso', 'texto' => 'Pouco espaço livre em disco (menos de 10%).', 'link' => null];
        }

        return $a;
    }
}