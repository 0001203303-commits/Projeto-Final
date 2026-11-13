<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RelatorioController extends Controller
{
    private const ORDEM = ['laranja', 'amarelo', 'verde', 'azul'];

    public function index(Request $request)
    {
        $f = $this->filtros($request);
        $r = $this->calcular($f);

        return view('painel.relatorios.index', compact('f', 'r'));
    }

    public function exportar(Request $request, string $tipo): StreamedResponse
    {
        $f = $this->filtros($request);
        $r = $this->calcular($f);
        $cores = config('triagem.cores');

        Log::info('Relatório exportado', ['usuario' => auth()->id(), 'tipo' => $tipo]);

        return response()->streamDownload(function () use ($tipo, $r, $cores) {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF"); // BOM para o Excel abrir os acentos

            if ($tipo === 'volume') {
                fputcsv($saida, ['Período', 'Laranja', 'Amarelo', 'Verde', 'Azul', 'Total'], ';', '"', '\\');

                foreach ($r['volume'] as $linha) {
                    fputcsv($saida, [
                        $linha['rotulo'],
                        $linha['cores']['laranja'], $linha['cores']['amarelo'],
                        $linha['cores']['verde'], $linha['cores']['azul'],
                        $linha['total'],
                    ], ';', '"', '\\');
                }
            } else {
                fputcsv($saida, [
                    'Cor', 'Triagens chamadas', 'Espera média (min)', 'Espera máxima (min)',
                    'Limite (min)', 'Dentro do limite (%)', 'Duração média do atendimento (min)', 'Ainda aguardando',
                ], ';', '"', '\\');

                foreach ($r['espera'] as $cor => $e) {
                    fputcsv($saida, [
                        $cores[$cor]['nome'], $e['n'], $e['media'], $e['maximo'],
                        $e['limite'], $e['dentro_pct'], $e['duracao'], $e['aguardando'],
                    ], ';', '"', '\\');
                }
            }

            fclose($saida);
        }, 'relatorio-' . $tipo . '-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ------------------------------------------------------------------

    private function filtros(Request $request): array
    {
        $dados = $request->validate([
            'periodo' => ['nullable', 'in:7d,30d,90d,tudo,personalizado'],
            'de'      => ['nullable', 'date_format:Y-m-d'],
            'ate'     => ['nullable', 'date_format:Y-m-d'],
        ], [
            '*.date_format' => 'Informe uma data válida.',
            '*.in'          => 'Opção de filtro inválida.',
        ]);

        $periodo = $dados['periodo'] ?? '30d';
        $de = $dados['de'] ?? null;
        $ate = $dados['ate'] ?? null;

        if ($de && $ate && $de > $ate) {
            [$de, $ate] = [$ate, $de];
        }

        [$inicio, $fim] = match ($periodo) {
            '7d'            => [today()->subDays(6), null],
            '30d'           => [today()->subDays(29), null],
            '90d'           => [today()->subDays(89), null],
            'tudo'          => [null, null],
            'personalizado' => [
                $de ? Carbon::parse($de)->startOfDay() : null,
                $ate ? Carbon::parse($ate)->endOfDay() : null,
            ],
        };

        return compact('periodo', 'de', 'ate', 'inicio', 'fim');
    }

    private function consulta(array $f): Builder
    {
        $q = Triagem::query()->whereNotNull('cor');

        if ($f['inicio']) {
            $q->where('created_at', '>=', $f['inicio']);
        }
        if ($f['fim']) {
            $q->where('created_at', '<=', $f['fim']);
        }

        return $q;
    }

    private function calcular(array $f): array
    {
        $cores = config('triagem.cores');
        $base = fn () => $this->consulta($f);

        // ---- Totais
        $total = $base()->count();
        $porCor = $base()->selectRaw('cor, COUNT(*) as total')->groupBy('cor')->pluck('total', 'cor');
        $alertas = $base()->where('alerta_imediato', true)->count();

        // ---- Intervalo efetivo (para montar as linhas do volume)
        $primeira = $f['inicio'] ?? $base()->min('created_at');
        $inicio = $primeira ? Carbon::parse($primeira)->startOfDay() : today();
        $fim = ($f['fim'] ?? now())->copy();
        if ($fim->lt($inicio)) {
            $fim = $inicio->copy()->endOfDay();
        }

        $dias = (int) abs($inicio->copy()->startOfDay()->diffInDays($fim->copy()->startOfDay())) + 1;
        $porMes = $dias > 92;

        // ---- Volume por dia (ou mês) e cor
        $formato = $porMes ? '%Y-%m' : '%Y-%m-%d';
        $linhas = $base()
            ->selectRaw("DATE_FORMAT(created_at, '{$formato}') as periodo, cor, COUNT(*) as total")
            ->groupBy('periodo', 'cor')
            ->get();

        $volume = [];
        $cursor = $porMes ? $inicio->copy()->startOfMonth() : $inicio->copy();
        while ($cursor->lte($fim)) {
            $volume[$cursor->format($porMes ? 'Y-m' : 'Y-m-d')] = [
                'rotulo' => $cursor->format($porMes ? 'm/Y' : 'd/m/Y'),
                'cores'  => array_fill_keys(self::ORDEM, 0),
                'total'  => 0,
            ];
            $porMes ? $cursor->addMonth() : $cursor->addDay();
        }

        foreach ($linhas as $l) {
            if (isset($volume[$l->periodo]) && isset($volume[$l->periodo]['cores'][$l->cor])) {
                $volume[$l->periodo]['cores'][$l->cor] += (int) $l->total;
                $volume[$l->periodo]['total'] += (int) $l->total;
            }
        }

        // Rótulo curto para o gráfico
        $serieGrafico = collect($volume)->map(fn ($v) => [
            'rotulo' => $porMes ? $v['rotulo'] : substr($v['rotulo'], 0, 5),
            'total'  => $v['total'],
        ])->values()->all();

        // ---- Dia da semana
        $semana = $base()->selectRaw('DAYOFWEEK(created_at) as d, COUNT(*) as total')->groupBy('d')->pluck('total', 'd');
        $nomesSemana = [2 => 'Seg', 3 => 'Ter', 4 => 'Qua', 5 => 'Qui', 6 => 'Sex', 7 => 'Sáb', 1 => 'Dom'];
        $porSemana = [];
        foreach ($nomesSemana as $d => $nome) {
            $porSemana[] = ['rotulo' => $nome, 'total' => (int) ($semana[$d] ?? 0)];
        }

        // ---- Espera por cor
        $espera = [];
        $somaN = $somaMedia = $somaDentro = $somaComLimite = 0;

        foreach (self::ORDEM as $c) {
            $limite = $cores[$c]['limite_min'];
            $chamados = fn () => $base()->where('cor', $c)->whereNotNull('chamado_em');

            $s = $chamados()->selectRaw(
                'COUNT(*) as n, AVG(TIMESTAMPDIFF(MINUTE, created_at, chamado_em)) as media, MAX(TIMESTAMPDIFF(MINUTE, created_at, chamado_em)) as maximo'
            )->first();

            $n = (int) $s->n;
            $dentro = ($limite && $n)
                ? $chamados()->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, chamado_em) <= ?', [$limite])->count()
                : null;

            $duracao = $base()->where('cor', $c)->whereNotNull('chamado_em')->whereNotNull('finalizado_em')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, chamado_em, finalizado_em)) as m')->value('m');

            $espera[$c] = [
                'n'          => $n,
                'media'      => $n ? (int) round($s->media) : null,
                'maximo'     => $n ? (int) $s->maximo : null,
                'limite'     => $limite,
                'dentro_pct' => ($limite && $n) ? (int) round($dentro / $n * 100) : null,
                'duracao'    => $duracao === null ? null : (int) round($duracao),
                'aguardando' => $base()->where('cor', $c)->where('status', 'aguardando')->count(),
            ];

            $somaN += $n;
            $somaMedia += $n ? $s->media * $n : 0;

            if ($limite && $n) {
                $somaComLimite += $n;
                $somaDentro += $dentro;
            }
        }

        // ---- Queixas
        $queixas = DB::table('triagem_sintomas as ts')
            ->join('triagens as t', 't.id', '=', 'ts.triagem_id')
            ->join('sintomas as s', 's.id', '=', 'ts.sintoma_id')
            ->whereNotNull('t.cor')
            ->when($f['inicio'], fn ($q, $v) => $q->where('t.created_at', '>=', $v))
            ->when($f['fim'], fn ($q, $v) => $q->where('t.created_at', '<=', $v))
            ->selectRaw('s.nome, COUNT(*) as total')
            ->groupBy('s.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'total'        => $total,
            'por_cor'      => $porCor,
            'alertas'      => $alertas,
            'espera_media' => $somaN ? (int) round($somaMedia / $somaN) : null,
            'meta_pct'     => $somaComLimite ? (int) round($somaDentro / $somaComLimite * 100) : null,
            'volume'       => $volume,
            'serie'        => $serieGrafico,
            'por_mes'      => $porMes,
            'semana'       => $porSemana,
            'espera'       => $espera,
            'queixas'      => $queixas,
            'de'           => $inicio,
            'ate'          => $fim,
        ];
    }
}
