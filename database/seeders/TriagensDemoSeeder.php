<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\Sintoma;
use App\Models\Triagem;
use App\Models\User;
use Illuminate\Database\Seeder;

class TriagensDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Remove a demonstração anterior (as triagens saem junto, por cascata)
        Paciente::where('nome', 'like', 'Paciente Teste %')->delete();

        $sintomas = Sintoma::pluck('id');
        $medico = User::where('perfil', 'medico')->first();
        $limites = config('triagem.cores');

        $pool = ['laranja', 'amarelo', 'amarelo', 'amarelo', 'verde', 'verde', 'verde', 'verde', 'verde', 'azul', 'azul'];

        for ($i = 1; $i <= 40; $i++) {
            $cor = $pool[array_rand($pool)];
            $limite = $limites[$cor]['limite_min'] ?? 120;

            $chegada = now()->subMinutes(random_int(3, 600));
            $decorrido = (int) abs($chegada->diffInMinutes(now()));
            $espera = random_int(2, (int) round($limite * 1.3));

            $status = 'aguardando';
            $chamado = null;
            $final = null;

            if ($decorrido > $espera && random_int(1, 100) <= 80) {
                $chamado = $chegada->copy()->addMinutes($espera);
                $status = 'em_atendimento';
                $duracao = random_int(10, 40);

                if ($decorrido > $espera + $duracao) {
                    $status = 'finalizada';
                    $final = $chamado->copy()->addMinutes($duracao);
                }
            }

            $paciente = Paciente::create([
                'nome'            => "Paciente Teste {$i}",
                'cpf'             => sprintf('%011d', random_int(10000000000, 99999999999)),
                'data_nascimento' => now()->subYears(random_int(1, 90))->toDateString(),
                'sexo'            => ['M', 'F'][array_rand(['M', 'F'])],
                'gestante'        => false,
            ]);

            $triagem = Triagem::unguarded(fn () => Triagem::create([
                'paciente_id'     => $paciente->id,
                'bpm'             => random_int(60, 120),
                'spo2'            => random_int(92, 99),
                'pa_sistolica'    => random_int(100, 150),
                'pa_diastolica'   => random_int(60, 95),
                'temperatura'     => random_int(360, 385) / 10,
                'score_sintomas'  => random_int(1, 10),
                'score_vitais'    => random_int(0, 4),
                'score_total'     => random_int(1, 18),
                'cor'             => $cor,
                'alerta_imediato' => $cor === 'laranja' && random_int(1, 4) === 1,
                'status'          => $status,
                'atendido_por'    => $status === 'aguardando' ? null : $medico?->id,
                'chamado_em'      => $chamado,
                'finalizado_em'   => $final,
                'motivos'         => ['Dados de demonstração'],
                'resumo_ia'       => [
                    'origem' => 'local', 'modelo' => null,
                    'resumo' => 'Ficha de demonstração gerada para teste do painel.',
                    'hipoteses' => [], 'inconsistencias' => [], 'pontos_atencao' => [],
                    'gerado_em' => now()->toIso8601String(),
                ],
                'created_at'      => $chegada,
                'updated_at'      => $chegada,
            ]));

            // Anexa os sintomas apenas se houver algum registro no banco
            if ($sintomas->isNotEmpty()) {
                $qtdPedida = random_int(1, 3);
                $qtdDisponivel = min($qtdPedida, $sintomas->count());
                $triagem->sintomas()->attach($sintomas->random($qtdDisponivel)->all());
            }
        }
    }
}