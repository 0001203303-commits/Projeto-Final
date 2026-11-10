<?php

namespace App\Services;

use App\Models\Triagem;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EstatisticasPainel
{
    private const ORDEM = ['laranja', 'amarelo', 'verde', 'azul'];

    public function calcular(string $periodo, CarbonInterface $inicio, Collection $aguardando, int $meuId): array
    {
        $cores = config('triagem.cores');

        $triagens = Triagem::with('paciente:id,data_nascimento')
            ->whereNotNull('cor')
            ->where('created_at', '>=', $inicio)
            ->get(['id', 'paciente_id', 'cor', 'status', 'atendido_por', 'created_at', 'chamado_em', 'finalizado_em']);

        // ---- Gravidade no período
        $porCor = [];
        foreach (self::ORDEM as $c) {
            $porCor[$c] = $triagens->where('cor', $c)->count();
        }

        // ---- Espera até ser chamado
        $esperas = $triagens->whereNotNull('chamado_em')->map(fn ($t) => [
            'cor' => $t->cor,
            'min' => abs($t->created_at->diffInMinutes($t->chamado_em)),
        ]);

        $esperaPorCor = [];
        foreach (self::ORDEM as $c) {
            $lista = $esperas->where('cor', $c);
            $esperaPorCor[$c] = [
                'media'  => $lista->isEmpty() ? null : (int) round($lista->avg('min')),
                'limite' => $cores[$c]['limite_min'],
            ];
        }

        $esperaMedia = $esperas->isEmpty() ? null : (int) round($esperas->avg('min'));

        // Meta: % de chamados dentro do limite da própria cor
        $comLimite = $esperas->filter(fn ($e) => $cores[$e['cor']]['limite_min']);
        $metaPct = $comLimite->isEmpty()
            ? null
            : (int) round(
                $comLimite->filter(fn ($e) => $e['min'] <= $cores[$e['cor']]['limite_min'])->count()
                / $comLimite->count() * 100
            );

        // ---- Duração dos atendimentos
        $duracoes = $triagens->where('status', 'finalizada')
            ->filter(fn ($t) => $t->chamado_em && $t->finalizado_em)
            ->map(fn ($t) => abs($t->chamado_em->diffInMinutes($t->finalizado_em)));
        $duracaoMedia = $duracoes->isEmpty() ? null : (int) round($duracoes->avg());

        // ---- Situação agora (independe do período)
        $emAtendimento = Triagem::where('status', 'em_atendimento')->get(['id', 'atendido_por']);

        $acimaLimite = $aguardando->filter(function ($t) use ($cores) {
            $limite = $cores[$t->cor]['limite_min'];

            return $limite && $t->espera_minutos > $limite;
        });

        $precisamAtencao = $aguardando
            ->filter(fn ($t) => $t->alerta_imediato || $acimaLimite->contains('id', $t->id))
            ->take(5)
            ->values();

        return [
            'total'             => $triagens->count(),
            'finalizadas'       => $triagens->where('status', 'finalizada')->count(),
            'meus_finalizados'  => $triagens->where('status', 'finalizada')->where('atendido_por', $meuId)->count(),
            'por_cor'           => $porCor,
            'espera_por_cor'    => $esperaPorCor,
            'espera_media'      => $esperaMedia,
            'meta_pct'          => $metaPct,
            'duracao_media'     => $duracaoMedia,

            'na_fila'           => $aguardando->count(),
            'maior_espera'      => (int) ($aguardando->max('espera_minutos') ?? 0),
            'acima_limite'      => $acimaLimite->count(),
            'alertas'           => $aguardando->where('alerta_imediato', true)->count(),
            'em_atendimento'    => $emAtendimento->count(),
            'meus_em_atendimento' => $emAtendimento->where('atendido_por', $meuId)->count(),
            'precisam_atencao'  => $precisamAtencao,

            'chegadas'          => $this->chegadas($periodo, $inicio, $triagens),
            'faixas'            => $this->faixasEtarias($triagens),
            'queixas'           => $this->queixas($inicio),
        ];
    }

    private function chegadas(string $periodo, CarbonInterface $inicio, Collection $triagens): array
    {
        if ($periodo === 'hoje') {
            $ini = now()->subHours(11)->startOfHour();
            $datas = Triagem::whereNotNull('cor')->where('created_at', '>=', $ini)->pluck('created_at');

            $serie = [];
            for ($i = 0; $i < 12; $i++) {
                $h = $ini->copy()->addHours($i);
                $fim = $h->copy()->endOfHour();

                $serie[] = [
                    'rotulo' => $h->format('H') . 'h',
                    'total'  => $datas->filter(fn ($d) => $d->between($h, $fim))->count(),
                ];
            }

            return ['titulo' => 'Chegadas nas últimas 12 horas', 'serie' => $serie];
        }

        $dias = $periodo === '7d' ? 7 : 30;
        $porDia = $triagens->groupBy(fn ($t) => $t->created_at->format('Y-m-d'))->map->count();

        $serie = [];
        for ($i = 0; $i < $dias; $i++) {
            $d = $inicio->copy()->addDays($i);
            $serie[] = [
                'rotulo' => $d->format('d/m'),
                'total'  => $porDia[$d->format('Y-m-d')] ?? 0,
            ];
        }

        return ['titulo' => "Chegadas por dia (últimos {$dias} dias)", 'serie' => $serie];
    }

    private function faixasEtarias(Collection $triagens): array
    {
        $faixas = ['0 a 12 anos' => 0, '13 a 29 anos' => 0, '30 a 59 anos' => 0, '60 anos ou mais' => 0];

        foreach ($triagens as $t) {
            $idade = $t->paciente->idade;

            $chave = match (true) {
                $idade <= 12 => '0 a 12 anos',
                $idade <= 29 => '13 a 29 anos',
                $idade <= 59 => '30 a 59 anos',
                default      => '60 anos ou mais',
            };

            $faixas[$chave]++;
        }

        return $faixas;
    }

    private function queixas(CarbonInterface $inicio): Collection
    {
        return DB::table('triagem_sintomas as ts')
            ->join('triagens as t', 't.id', '=', 'ts.triagem_id')
            ->join('sintomas as s', 's.id', '=', 'ts.sintoma_id')
            ->whereNotNull('t.cor')
            ->where('t.created_at', '>=', $inicio)
            ->selectRaw('s.nome, COUNT(*) as total')
            ->groupBy('s.nome')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
    }
}