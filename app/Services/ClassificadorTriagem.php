<?php

namespace App\Services;

use App\Models\Triagem;

class ClassificadorTriagem
{
    private const ORDEM = ['azul' => 0, 'verde' => 1, 'amarelo' => 2, 'laranja' => 3];

    /** Faixas [limite_superior, pontos], adaptadas da escala NEWS2 (adultos). */
    private const FAIXAS = [
        'spo2' => [[91, 3], [93, 2], [95, 1], [INF, 0]],
        'pas'  => [[90, 3], [100, 2], [110, 1], [219, 0], [INF, 3]],
        'fc'   => [[40, 3], [50, 1], [90, 0], [110, 1], [130, 2], [INF, 3]],
        'temp' => [[35.0, 3], [36.0, 1], [38.0, 0], [39.0, 1], [INF, 2]],
    ];

    public function classificar(Triagem $triagem): array
    {
        $triagem->loadMissing('paciente');
        $paciente = $triagem->paciente;
        $idade = $paciente->idade;

        $piso = 'azul';   // cor mínima exigida pelas regras de segurança
        $motivos = [];
        $alerta = false;

        $elevar = function (string $nova, string $motivo) use (&$piso, &$motivos) {
            if (self::ORDEM[$nova] > self::ORDEM[$piso]) {
                $piso = $nova;
            }
            $motivos[] = $motivo;
        };

        // ---------- Sinais vitais ----------
        $spo2 = (int) $triagem->spo2;
        $fc   = (int) $triagem->bpm;
        $pas  = (int) $triagem->pa_sistolica;
        $pad  = (int) $triagem->pa_diastolica;
        $temp = (float) $triagem->temperatura;

        $pediatrico = $idade < config('triagem.idade_pediatrica');

        $itens = ['spo2' => $spo2, 'temp' => $temp];
        if (! $pediatrico) {
            $itens += ['fc' => $fc, 'pas' => $pas];
        }

        $nomes = ['temp' => 'Temperatura', 'fc' => 'Batimentos cardíacos', 'pas' => 'Pressão sistólica'];
        $scoreVitais = 0;

        foreach ($itens as $chave => $valor) {
            $pontos = $this->pontuar($valor, self::FAIXAS[$chave]);
            $scoreVitais += $pontos;

            // Nota 3 em um único parâmetro: urgência, mesmo com total baixo
            if ($pontos === 3 && $chave !== 'spo2') {
                $elevar('amarelo', "{$nomes[$chave]} em faixa crítica ({$valor})");
            }
        }

        if (! $pediatrico && $pas < 220) {
            if ($pas >= 180 || $pad >= 120) {
                $scoreVitais += 2;
                $elevar('amarelo', "Pressão arterial muito elevada ({$pas}/{$pad} mmHg)");
            } elseif ($pas >= 160 || $pad >= 100) {
                $scoreVitais += 1;
            }
        }

        if ($spo2 <= 91) {
            $elevar('laranja', "SpO₂ muito baixa ({$spo2}%)");
        } elseif ($spo2 <= 94) {
            $elevar('amarelo', "SpO₂ abaixo do esperado ({$spo2}%)");
        }

        if (! $pediatrico && $pas > 0 && ($fc / $pas) >= 1.0) {
            $elevar('laranja', "Índice de choque elevado (batimentos {$fc} ≥ pressão sistólica {$pas})");
        }

        if ($spo2 <= config('triagem.alerta_imediato.spo2')
            || (! $pediatrico && $pas <= config('triagem.alerta_imediato.pas'))) {
            $alerta = true;
            $elevar('laranja', 'Valor crítico: avaliação imediata da enfermagem');
        }

        if ($pediatrico) {
            $elevar('verde', 'Menor de 14 anos: batimentos e pressão não pontuados (faixas pediátricas diferentes). Exige avaliação da enfermagem.');
        }

        // ---------- Fatores de risco ----------
        $fatores = 0;
        if ($idade >= 80) {
            $fatores += 2;
            $motivos[] = 'Idade igual ou superior a 80 anos';
        } elseif ($idade >= 65) {
            $fatores += 1;
            $motivos[] = 'Idade igual ou superior a 65 anos';
        }
        if ($paciente->gestante) {
            $fatores += 1;
            $motivos[] = 'Gestante';
        }

        // ---------- Sintomas ----------
        $triagem->load(['sintomas', 'respostas.pergunta']);

        $porSintoma = [];
        foreach ($triagem->sintomas as $sintoma) {
            $porSintoma[$sintoma->id] = (int) $sintoma->pontos_base;

            if ($sintoma->cor_minima) {
                $elevar($sintoma->cor_minima, "Sintoma que exige avaliação: {$sintoma->nome}");
            }
        }

        foreach ($triagem->respostas as $resposta) {
            $pergunta = $resposta->pergunta;
            $sid = $pergunta->sintoma_id;

            if (! isset($porSintoma[$sid])) {
                continue;
            }

            $porSintoma[$sid] += (int) $resposta->pontos;

            if ((int) $resposta->valor === 1 && $pergunta->tipo === 'sim_nao' && $pergunta->cor_minima) {
                $elevar($pergunta->cor_minima, 'Resposta de alerta: ' . $pergunta->texto);
            }
        }

        // O sintoma mais pontuado conta 100%; os demais, uma fração
        $pontos = collect($porSintoma)
            ->map(fn ($p) => min($p, config('triagem.limite_por_sintoma')))
            ->sortDesc()
            ->values();

        $principal = (int) ($pontos->shift() ?? 0);
        $scoreSintomas = (int) round(min(
            $principal + config('triagem.peso_secundarios') * $pontos->sum(),
            config('triagem.limite_sintomas')
        ));

        // ---------- Total e cor ----------
        $total = $scoreSintomas
            + (int) round($scoreVitais * config('triagem.peso_vitais'))
            + $fatores;

        $l = config('triagem.limiares');
        $corCalculada = match (true) {
            $total >= $l['laranja'] => 'laranja',
            $total >= $l['amarelo'] => 'amarelo',
            $total >= $l['verde']   => 'verde',
            default                 => 'azul',
        };

        $corFinal = self::ORDEM[$piso] > self::ORDEM[$corCalculada] ? $piso : $corCalculada;

        array_unshift(
            $motivos,
            "Pontuação: sintomas {$scoreSintomas} + sinais vitais {$scoreVitais} (×" . config('triagem.peso_vitais') . ") + fatores {$fatores} = {$total}"
        );

        return [
            'score_sintomas'  => $scoreSintomas,
            'score_vitais'    => $scoreVitais,
            'score_total'     => $total,
            'cor'             => $corFinal,
            'alerta_imediato' => $alerta,
            'motivos'         => array_values(array_unique($motivos)),
        ];
    }

    private function pontuar(float|int $valor, array $faixas): int
    {
        foreach ($faixas as [$limite, $pontos]) {
            if ($valor <= $limite) {
                return $pontos;
            }
        }

        return 0;
    }
}