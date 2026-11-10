<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HistoricoController extends Controller
{
    public function index(Request $request)
    {
        $f = $this->filtros($request);
        $base = $this->consulta($f);

        $triagens = (clone $base)
            ->with(['paciente', 'sintomas', 'atendente'])
            ->withCount('comentarios')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        // Contagem por cor ignorando o filtro de cor (usada nos chips)
        $contagemCores = $this->consulta($f, ignorarCor: true)
            ->selectRaw('cor, COUNT(*) as total')
            ->groupBy('cor')
            ->pluck('total', 'cor');

        $espera = (clone $base)->whereNotNull('chamado_em')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, chamado_em)) as media')
            ->value('media');

        $duracao = (clone $base)->whereNotNull('chamado_em')->whereNotNull('finalizado_em')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, chamado_em, finalizado_em)) as media')
            ->value('media');

        $resumo = [
            'total'    => $triagens->total(),
            'espera'   => $espera === null ? null : (int) round($espera),
            'duracao'  => $duracao === null ? null : (int) round($duracao),
            'alertas'  => (clone $base)->where('alerta_imediato', true)->count(),
        ];

        return view('painel.historico.index', compact('f', 'triagens', 'contagemCores', 'resumo'));
    }

    public function exportar(Request $request): StreamedResponse
    {
        $f = $this->filtros($request);
        $nomeArquivo = 'historico-' . now()->format('Ymd-His') . '.csv';

        $sexos = ['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro'];
        $situacoes = ['aguardando' => 'Aguardando', 'em_atendimento' => 'Em atendimento', 'finalizada' => 'Atendido'];

        return response()->streamDownload(function () use ($f, $sexos, $situacoes) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF"); // BOM: o Excel abre os acentos corretamente

            fputcsv($saida, [
                'Ficha', 'Chegada', 'Paciente', 'Idade', 'Sexo', 'Gravidade', 'Situação',
                'Queixas', 'Espera até chamado (min)', 'Duração do atendimento (min)',
                'Atendido por', 'Pontuação',
            ], ';', '"', '\\');

            $this->consulta($f)
                ->with(['paciente', 'sintomas', 'atendente'])
                ->chunkById(200, function ($lote) use ($saida, $sexos, $situacoes) {
                    foreach ($lote as $t) {
                        fputcsv($saida, [
                            str_pad($t->id, 4, '0', STR_PAD_LEFT),
                            $t->created_at->format('d/m/Y H:i'),
                            $this->celula($t->paciente->nome),
                            $t->paciente->idade,
                            $sexos[$t->paciente->sexo] ?? '',
                            ucfirst($t->cor),
                            $situacoes[$t->status] ?? $t->status,
                            $this->celula($t->sintomas->pluck('nome')->implode(', ')),
                            $t->espera_ate_chamado,
                            $t->duracao_atendimento,
                            $this->celula($t->atendente->name ?? ''),
                            $t->score_total,
                        ], ';', '"', '\\');
                    }
                });

            fclose($saida);
        }, $nomeArquivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ------------------------------------------------------------------

    private function filtros(Request $request): array
    {
        $dados = $request->validate([
            'periodo' => ['nullable', 'in:hoje,7d,30d,tudo,personalizado'],
            'de'      => ['nullable', 'date_format:Y-m-d'],
            'ate'     => ['nullable', 'date_format:Y-m-d'],
            'cor'     => ['nullable', 'in:laranja,amarelo,verde,azul'],
            'status'  => ['nullable', 'in:todos,aguardando,em_atendimento,finalizada'],
            'busca'   => ['nullable', 'string', 'max:100'],
        ], [
            '*.date_format' => 'Informe uma data válida.',
            '*.in'          => 'Opção de filtro inválida.',
            'busca.max'     => 'A busca pode ter no máximo 100 caracteres.',
        ]);

        $periodo = $dados['periodo'] ?? '30d';
        $de  = $dados['de'] ?? null;
        $ate = $dados['ate'] ?? null;

        if ($de && $ate && $de > $ate) {
            [$de, $ate] = [$ate, $de];
        }

        [$inicio, $fim] = match ($periodo) {
            'hoje'          => [today(), null],
            '7d'            => [today()->subDays(6), null],
            '30d'           => [today()->subDays(29), null],
            'tudo'          => [null, null],
            'personalizado' => [
                $de ? Carbon::parse($de)->startOfDay() : null,
                $ate ? Carbon::parse($ate)->endOfDay() : null,
            ],
        };

        return [
            'periodo' => $periodo,
            'de'      => $de,
            'ate'     => $ate,
            'inicio'  => $inicio,
            'fim'     => $fim,
            'cor'     => $dados['cor'] ?? null,
            'status'  => $dados['status'] ?? 'todos',
            'busca'   => trim($dados['busca'] ?? ''),
            'meus'    => $request->boolean('meus'),
        ];
    }

    private function consulta(array $f, bool $ignorarCor = false): Builder
    {
        $q = Triagem::query()->whereNotNull('cor');

        if ($f['inicio']) {
            $q->where('created_at', '>=', $f['inicio']);
        }
        if ($f['fim']) {
            $q->where('created_at', '<=', $f['fim']);
        }
        if ($f['cor'] && ! $ignorarCor) {
            $q->where('cor', $f['cor']);
        }
        if ($f['status'] !== 'todos') {
            $q->where('status', $f['status']);
        }
        if ($f['meus']) {
            $q->where('atendido_por', auth()->id());
        }

        if ($f['busca'] !== '') {
            $b = $f['busca'];
            $like = '%' . addcslashes($b, '%_\\') . '%';

            $q->where(function (Builder $w) use ($like, $b) {
                $w->whereHas('paciente', fn ($p) => $p->where('nome', 'like', $like))
                  ->orWhereHas('sintomas', fn ($s) => $s->where('nome', 'like', $like));

                // "12" ou "0012" encontra a ficha nº 12
                if (ctype_digit($b)) {
                    $w->orWhere('id', (int) $b);
                }
            });
        }

        return $q;
    }

    /** Evita que o Excel execute como fórmula um texto digitado por terceiros. */
    private function celula(?string $valor): string
    {
        $valor = (string) $valor;

        return preg_match('/^[=+\-@\t\r]/', $valor) ? "'" . $valor : $valor;
    }
}