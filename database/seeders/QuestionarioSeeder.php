<?php

namespace Database\Seeders;

use App\Models\CategoriaSintoma;
use App\Models\Pergunta;
use App\Models\Sintoma;
use Illuminate\Database\Seeder;

class QuestionarioSeeder extends Seeder
{
    public function run(): void
    {
        $dados = [
            'Respiratório' => ['🫁', [
                'Tosse' => [1, [
                    ['A tosse atrapalha seu sono?', 1],
                    ['Você sente chiado no peito junto com a tosse?', 2],
                    ['Você está com tosse há mais de 7 dias?', 1],
                ]],
                'Falta de ar' => [3, [
                    ['Sente falta de ar mesmo parado, sem fazer esforço?', 3],
                    ['Precisa parar para respirar quando fala?', 3],
                    ['A falta de ar começou de repente?', 2],
                ]],
                'Dor ao respirar' => [2, [
                    ['Qual a intensidade da dor?', 'i'],
                    ['A dor começou depois de uma pancada ou queda?', 1],
                    ['Você está com febre junto?', 1],
                ]],
                'Nariz entupido' => [1, [
                    ['A secreção é amarela ou esverdeada?', 1],
                    ['Sente dor ou pressão no rosto?', 1],
                ]],
            ]],

            'Cardiovascular' => ['❤️', [
                'Palpitações' => [2, [
                    ['O coração dispara mesmo quando você está em repouso?', 2],
                    ['Sente tontura junto com as palpitações?', 2],
                    ['As palpitações duram mais de alguns minutos?', 1],
                ]],
                'Desconforto no peito' => [4, [
                    ['O desconforto aparece quando você está parado?', 3],
                    ['A dor se espalha para o braço, as costas ou o queixo?', 3],
                    ['Sente suor frio ou enjoo junto?', 2],
                ]],
                'Inchaço nas pernas' => [1, [
                    ['O inchaço é em uma perna só?', 3],
                    ['A perna está vermelha ou quente?', 2],
                    ['Sente falta de ar junto com o inchaço?', 2],
                ]],
            ]],

            'Gastrointestinal' => ['🤢', [
                'Náusea' => [1, [
                    ['Você não consegue beber líquidos por causa do enjoo?', 2],
                    ['O enjoo dura mais de 2 dias?', 1],
                ]],
                'Vômito' => [2, [
                    ['Vomitou mais de 3 vezes hoje?', 2],
                    ['Não consegue manter líquidos no estômago?', 2],
                    ['O vômito tem sangue ou aspecto escuro?', 4],
                ]],
                'Dor de barriga' => [2, [
                    ['Qual a intensidade da dor?', 'i'],
                    ['A dor está concentrada embaixo, do lado direito?', 2],
                    ['A barriga está dura ou muito sensível ao toque?', 2],
                ]],
                'Diarreia' => [1, [
                    ['Evacuou mais de 5 vezes hoje?', 2],
                    ['Há sangue nas fezes?', 3],
                    ['Sente muita sede ou boca seca?', 1],
                ]],
            ]],

            'Neurológico' => ['🧠', [
                'Dor de cabeça' => [1, [
                    ['Qual a intensidade da dor?', 'i'],
                    ['É a pior dor de cabeça que você já sentiu?', 3],
                    ['Sente o pescoço rígido ou incômodo com a luz?', 3],
                ]],
                'Tontura' => [2, [
                    ['Sente que o ambiente está girando?', 1],
                    ['Chegou a desmaiar hoje?', 3],
                    ['Está com dificuldade para andar reto?', 2],
                ]],
                'Visão turva' => [2, [
                    ['A visão ficou turva de repente?', 3],
                    ['Está com dor de cabeça junto?', 1],
                ]],
            ]],

            'Musculoesquelético' => ['🦴', [
                'Dor nas costas' => [1, [
                    ['Qual a intensidade da dor?', 'i'],
                    ['A dor desce para a perna?', 1],
                    ['Sente formigamento ou perda de força na perna?', 2],
                ]],
                'Dor nas articulações' => [1, [
                    ['A articulação está inchada ou vermelha?', 2],
                    ['A dor dificulta muito movimentar a articulação?', 1],
                ]],
                'Lesão' => [2, [
                    ['A lesão foi causada por queda, batida ou acidente?', 1],
                    ['Você não consegue apoiar ou mexer a região machucada?', 3],
                    ['A região está muito inchada ou com aspecto torto?', 2],
                ]],
            ]],

            'Dermatológico' => ['🩹', [
                'Coceira' => [1, [
                    ['A coceira atinge o corpo todo?', 1],
                    ['Surgiu depois de comer algo ou tomar um remédio?', 2],
                    ['Sente os lábios ou a língua inchados?', 4],
                ]],
                'Vermelhidão' => [1, [
                    ['A área está quente ou dolorida?', 1],
                    ['A vermelhidão está aumentando?', 2],
                    ['Você está com febre junto?', 1],
                ]],
                'Ferida' => [1, [
                    ['A ferida tem pus ou mau cheiro?', 2],
                    ['A ferida sangra e não para?', 3],
                    ['A ferida existe há mais de 2 semanas sem cicatrizar?', 1],
                ]],
                'Mancha' => [1, [
                    ['A mancha apareceu ou cresceu rápido?', 1],
                    ['A mancha coça ou dói?', 1],
                    ['A mancha surgiu junto com febre?', 2],
                ]],
            ]],

            'Sintomas gerais' => ['🌡️', [
                'Febre' => [2, [
                    ['A febre dura mais de 3 dias?', 2],
                    ['Sente calafrios ou tremores?', 1],
                    ['Está com dificuldade para beber líquidos?', 2],
                ]],
                'Cansaço' => [1, [
                    ['O cansaço começou de repente?', 1],
                    ['Sente falta de ar ao fazer pouco esforço?', 2],
                    ['Está cansado há mais de uma semana?', 1],
                ]],
                'Mal-estar' => [1, [
                    ['Está com suor frio?', 2],
                    ['Sente que vai desmaiar?', 2],
                ]],
                'Perda de apetite' => [1, [
                    ['Está sem comer direito há mais de 2 dias?', 2],
                    ['Perdeu peso sem querer?', 1],
                ]],
            ]],
        ];

        $ordemCategoria = 0;

        foreach ($dados as $nomeCategoria => [$icone, $sintomas]) {
            $categoria = CategoriaSintoma::updateOrCreate(
                ['nome' => $nomeCategoria],
                ['icone' => $icone, 'ordem' => ++$ordemCategoria, 'ativo' => true]
            );

            foreach ($sintomas as $nomeSintoma => [$pontosBase, $perguntas]) {
                $sintoma = Sintoma::updateOrCreate(
                    ['categoria_id' => $categoria->id, 'nome' => $nomeSintoma],
                    ['pontos_base' => $pontosBase, 'ativo' => true]
                );

                foreach ($perguntas as $i => [$texto, $pontos]) {
                    $intensidade = $pontos === 'i';

                    Pergunta::updateOrCreate(
                        ['sintoma_id' => $sintoma->id, 'texto' => $texto],
                        [
                            'tipo'       => $intensidade ? 'intensidade' : 'sim_nao',
                            'pontos_sim' => $intensidade ? 0 : $pontos,
                            'ordem'      => $i + 1,
                            'ativo'      => true,
                        ]
                    );
                }
            }
        }
        $this->aplicarCoresMinimas(); 

        
    }
        private function aplicarCoresMinimas(): void
    {
        $sintomas = [
            'Desconforto no peito' => 'amarelo',
            'Falta de ar'          => 'amarelo',
        ];

        foreach ($sintomas as $nome => $cor) {
            Sintoma::where('nome', $nome)->update(['cor_minima' => $cor]);
        }

        $perguntas = [
            // Laranja
            'Sente falta de ar mesmo parado, sem fazer esforço?'     => 'laranja',
            'Precisa parar para respirar quando fala?'               => 'laranja',
            'O desconforto aparece quando você está parado?'         => 'laranja',
            'A dor se espalha para o braço, as costas ou o queixo?'  => 'laranja',
            'Sente os lábios ou a língua inchados?'                  => 'laranja',
            'O vômito tem sangue ou aspecto escuro?'                 => 'laranja',
            'É a pior dor de cabeça que você já sentiu?'             => 'laranja',
            'Sente o pescoço rígido ou incômodo com a luz?'          => 'laranja',
            // Amarelo
            'A falta de ar começou de repente?'                      => 'amarelo',
            'Sente suor frio ou enjoo junto?'                        => 'amarelo',
            'O inchaço é em uma perna só?'                           => 'amarelo',
            'Há sangue nas fezes?'                                   => 'amarelo',
            'Chegou a desmaiar hoje?'                                => 'amarelo',
            'A visão ficou turva de repente?'                        => 'amarelo',
            'Sente formigamento ou perda de força na perna?'         => 'amarelo',
            'Você não consegue apoiar ou mexer a região machucada?'  => 'amarelo',
            'A ferida sangra e não para?'                            => 'amarelo',
        ];

        foreach ($perguntas as $texto => $cor) {
            Pergunta::where('texto', $texto)->update(['cor_minima' => $cor]);
        }
    }
}