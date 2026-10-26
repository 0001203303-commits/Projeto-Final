<?php

namespace App\Services;

use App\Models\Triagem;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResumoIa
{
    private const INSTRUCAO = <<<'TXT'
Você é um assistente de apoio à decisão clínica em um pronto-socorro brasileiro.
Receberá os dados de uma pré-triagem feita em um totem de autoatendimento: respostas simples do paciente (botões) e sinais vitais medidos por sensores.
Escreva para um MÉDICO, em português do Brasil, de forma objetiva e técnica.

Regras:
1. Não dê diagnóstico definitivo e não sugira tratamento, medicação ou conduta.
2. Liste apenas hipóteses a investigar, coerentes com os dados, da mais para a menos relevante (no máximo 4).
3. Não invente dados que não foram informados. Se a informação for insuficiente, diga isso.
4. Aponte inconsistências entre o relato do paciente e os sinais vitais, quando existirem.
5. Destaque sinais de alerta presentes nos dados.
6. A classificação de risco (cor) já foi calculada pelo sistema: não a altere nem a questione.
7. Responda somente no formato JSON solicitado.
TXT;

    // ------------------------------------------------------------------
    // Resumo local: sempre disponível, não depende de internet
    // ------------------------------------------------------------------
    public function gerarLocal(Triagem $triagem): void
    {
        $this->carregar($triagem);

        $triagem->update(['resumo_ia' => $this->resumoLocal($triagem)]);
    }

    // ------------------------------------------------------------------
    // Resumo com Gemini: substitui o local se tudo der certo
    // ------------------------------------------------------------------
    public function gerarComGemini(Triagem $triagem): bool
    {
        $chave = config('services.gemini.key');

        if (! $chave) {
            return false;
        }

        $this->carregar($triagem);
        set_time_limit(60);

        $modelo = config('services.gemini.model');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent";

        try {
            $resposta = Http::withHeaders(['x-goog-api-key' => $chave])
                ->timeout((int) config('services.gemini.timeout', 15))
                ->retry(
                    1,
                    500,
                    fn ($e) => $e instanceof ConnectionException
                        || ($e instanceof RequestException && in_array($e->response->status(), [429, 500, 502, 503, 504])),
                    throw: false
                )
                ->post($url, $this->payload($triagem));
        } catch (\Throwable $e) {
            Log::warning('Gemini indisponível', ['triagem' => $triagem->id, 'erro' => $e->getMessage()]);

            return false;
        }

        if ($resposta->failed()) {
            Log::warning('Gemini retornou erro', [
                'triagem' => $triagem->id,
                'status'  => $resposta->status(),
                'erro'    => Str::limit($resposta->body(), 300),
            ]);

            return false;
        }

        $texto = collect($resposta->json('candidates.0.content.parts', []))
            ->reject(fn ($parte) => ! empty($parte['thought']))
            ->pluck('text')
            ->join('');

        $dados = json_decode($texto, true);

        if (! is_array($dados) || empty($dados['resumo'])) {
            Log::warning('Gemini retornou formato inesperado', ['triagem' => $triagem->id]);

            return false;
        }

        $inconsistencias = array_merge(
            $this->inconsistenciasLocais($triagem),
            $this->textos($dados['inconsistencias'] ?? [], 5, 250)
        );

        $triagem->update(['resumo_ia' => [
            'origem'          => 'gemini',
            'modelo'          => $modelo,
            'resumo'          => Str::limit(trim(strip_tags((string) $dados['resumo'])), 900),
            'hipoteses'       => $this->textos($dados['hipoteses'] ?? [], 4, 200),
            'inconsistencias' => array_values(array_unique($inconsistencias)),
            'pontos_atencao'  => $this->textos($dados['pontos_atencao'] ?? [], 6, 250),
            'gerado_em'       => now()->toIso8601String(),
        ]]);

        return true;
    }

    // ------------------------------------------------------------------
    // Montagem dos dados e da requisição
    // ------------------------------------------------------------------
    private function payload(Triagem $triagem): array
    {
        $dados = json_encode(
            $this->dadosAnonimos($triagem),
            JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        return [
            'systemInstruction' => ['parts' => [['text' => self::INSTRUCAO]]],
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => "Dados da pré-triagem (JSON):\n" . $dados]],
            ]],
            'generationConfig' => [
                'temperature'      => 0.2,
                'maxOutputTokens'  => 4096,
                'responseMimeType' => 'application/json',
                'responseSchema'   => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'resumo' => [
                            'type' => 'STRING',
                            'description' => 'Resumo clínico objetivo em 3 a 4 frases.',
                        ],
                        'hipoteses' => [
                            'type' => 'ARRAY',
                            'description' => 'Hipóteses a investigar (não são diagnóstico).',
                            'items' => ['type' => 'STRING'],
                        ],
                        'inconsistencias' => [
                            'type' => 'ARRAY',
                            'description' => 'Divergências entre relato e sinais vitais.',
                            'items' => ['type' => 'STRING'],
                        ],
                        'pontos_atencao' => [
                            'type' => 'ARRAY',
                            'description' => 'Sinais de alerta presentes nos dados.',
                            'items' => ['type' => 'STRING'],
                        ],
                    ],
                    'required' => ['resumo', 'hipoteses', 'inconsistencias', 'pontos_atencao'],
                ],
            ],
        ];
    }

    /** Somente dados anonimizados: sem nome, CPF, telefone ou data de nascimento. */
    private function dadosAnonimos(Triagem $t): array
    {
        $p = $t->paciente;
        $respostasPorSintoma = $t->respostas->groupBy(fn ($r) => $r->pergunta->sintoma_id);

        return [
            'paciente' => [
                'codigo'     => $p->codigo_anonimo,
                'idade_anos' => $p->idade,
                'sexo'       => ['M' => 'masculino', 'F' => 'feminino', 'O' => 'outro'][$p->sexo] ?? null,
                'gestante'   => (bool) $p->gestante,
            ],
            'sinais_vitais' => [
                'frequencia_cardiaca_bpm'    => $t->bpm,
                'saturacao_oxigenio_percent' => $t->spo2,
                'pressao_arterial_mmHg'      => "{$t->pa_sistolica}/{$t->pa_diastolica}",
                'temperatura_celsius'        => (float) $t->temperatura,
            ],
            'sintomas_relatados' => $t->sintomas->map(fn ($s) => [
                'sintoma'   => $s->nome,
                'categoria' => $s->categoria->nome,
                'respostas' => ($respostasPorSintoma[$s->id] ?? collect())->map(fn ($r) => [
                    'pergunta' => $r->pergunta->texto,
                    'resposta' => $this->rotulo($r->pergunta->tipo, (int) $r->valor),
                ])->values()->all(),
            ])->values()->all(),
            'classificacao_do_sistema' => [
                'cor'                  => $t->cor,
                'pontuacao_total'      => $t->score_total,
                'motivos'              => $t->motivos ?? [],
                'alerta_imediato'      => (bool) $t->alerta_imediato,
            ],
        ];
    }

    // ------------------------------------------------------------------
    // Resumo local e regras de inconsistência
    // ------------------------------------------------------------------
    private function resumoLocal(Triagem $t): array
    {
        $p = $t->paciente;
        $sexo = ['M' => 'masculino', 'F' => 'feminino', 'O' => 'outro'][$p->sexo] ?? 'não informado';
        $sintomas = $t->sintomas->pluck('nome')->implode(', ') ?: 'nenhum informado';
        $temperatura = number_format((float) $t->temperatura, 1, ',', '.');

        $resumo = "Paciente de {$p->idade} anos, sexo {$sexo}" . ($p->gestante ? ', gestante' : '') . ". "
            . "Relata: {$sintomas}. "
            . "Sinais vitais: FC {$t->bpm} bpm, SpO₂ {$t->spo2}%, PA {$t->pa_sistolica}/{$t->pa_diastolica} mmHg, temperatura {$temperatura} °C. "
            . 'Classificação do sistema: ' . strtoupper($t->cor ?? 'não calculada') . '.';

        $positivas = $t->respostas
            ->filter(fn ($r) => $r->pergunta)
            ->map(function ($r) {
                $q = $r->pergunta;

                if ($q->tipo === 'intensidade') {
                    return "{$q->sintoma->nome}: " . rtrim($q->texto, '?') . ' → ' . strtolower($this->rotulo('intensidade', (int) $r->valor));
                }

                return (int) $r->valor === 1 ? "{$q->sintoma->nome}: " . rtrim($q->texto, '?') . ' → Sim' : null;
            })
            ->filter()
            ->values()
            ->all();

        return [
            'origem'          => 'local',
            'modelo'          => null,
            'resumo'          => $resumo,
            'hipoteses'       => [],
            'inconsistencias' => $this->inconsistenciasLocais($t),
            'pontos_atencao'  => array_merge($positivas, array_slice($t->motivos ?? [], 1)),
            'gerado_em'       => now()->toIso8601String(),
        ];
    }

    private function inconsistenciasLocais(Triagem $t): array
    {
        $nomes = $t->sintomas->pluck('nome')->map(fn ($n) => Str::lower($n));
        $tem = fn (string $nome) => $nomes->contains($nome);

        $spo2 = (int) $t->spo2;
        $fc = (int) $t->bpm;
        $temp = (float) $t->temperatura;
        $tempTxt = number_format($temp, 1, ',', '.');
        $adulto = $t->paciente->idade >= config('triagem.idade_pediatrica');

        $itens = [];

        if ($tem('falta de ar') && $spo2 >= 96) {
            $itens[] = "Refere falta de ar, mas a SpO₂ medida está normal ({$spo2}%).";
        }
        if (! $tem('falta de ar') && $spo2 <= 94) {
            $itens[] = "SpO₂ baixa ({$spo2}%) sem relato de falta de ar.";
        }
        if ($tem('febre') && $temp < 37.8) {
            $itens[] = "Refere febre, mas a temperatura medida foi {$tempTxt} °C.";
        }
        if (! $tem('febre') && $temp >= 38.0) {
            $itens[] = "Temperatura elevada ({$tempTxt} °C) sem relato de febre.";
        }
        if ($adulto && $tem('palpitações') && $fc >= 51 && $fc <= 100) {
            $itens[] = "Refere palpitações, mas a frequência cardíaca medida está normal ({$fc} bpm); considerar sintomas intermitentes.";
        }
        if ($adulto && ! $tem('palpitações') && ($fc > 110 || $fc < 50)) {
            $itens[] = "Frequência cardíaca alterada ({$fc} bpm) sem relato de palpitações.";
        }

        return $itens;
    }

    // ------------------------------------------------------------------
    // Auxiliares
    // ------------------------------------------------------------------
    private function carregar(Triagem $triagem): void
    {
        $triagem->load(['paciente', 'sintomas.categoria', 'respostas.pergunta.sintoma']);
    }

    private function rotulo(string $tipo, int $valor): string
    {
        if ($tipo === 'intensidade') {
            return [1 => 'Leve', 2 => 'Moderada', 3 => 'Forte'][$valor] ?? '';
        }

        return $valor === 1 ? 'Sim' : 'Não';
    }

    /** Garante lista de textos curtos e sem HTML, venha o que vier da IA. */
    private function textos(mixed $valor, int $max, int $tamanho): array
    {
        return collect(is_array($valor) ? $valor : [])
            ->filter('is_string')
            ->map(fn ($s) => Str::limit(trim(strip_tags($s)), $tamanho))
            ->filter()
            ->take($max)
            ->values()
            ->all();
    }
}