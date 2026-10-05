<?php

namespace App\Services;

use App\DTOs\SinaisVitaisDTO;
use App\Enums\StatusTriagemEnum;
use App\Jobs\ProcessarAnaliseIaJob;
use App\Models\Paciente;
use App\Models\Totem;
use App\Models\Triagem;
use Illuminate\Support\Facades\DB;

class TriagemService
{
    public function __construct(
        private ManchesterEvaluatorService $manchesterEvaluator
    ) {}

    public function registrarTriagem(array $dados): Triagem
    {
        return DB::transaction(function () use ($dados) {
            // 1. Identifica Totem e Unidade
            $totem = Totem::where('codigo_totem', $dados['codigo_totem'])->firstOrFail();

            // 2. Trata Paciente com Hashing e Criptografia (LGPD)
            $cpfHash = hash('sha256', $dados['paciente']['cpf']);
            
            $paciente = Paciente::firstOrCreate(
                ['cpf_hash' => $cpfHash],
                [
                    'cpf_encrypted' => $dados['paciente']['cpf'],
                    'nome_encrypted' => $dados['paciente']['nome'],
                    'data_nascimento' => $dados['paciente']['data_nascimento'],
                    'sexo' => $dados['paciente']['sexo'],
                ]
            );

            // 3. Monta DTO e Avalia Risco de Manchester
            $intensidadeDor = $dados['questionario']['intensidade_dor'] ?? 0;
            $botaoPanico = $dados['questionario']['botao_panico'] ?? false;
            
            $sinaisDTO = SinaisVitaisDTO::fromArray($dados['sinais_vitais'], $intensidadeDor, $botaoPanico);
            $classificacao = $this->manchesterEvaluator->avaliar($sinaisDTO);

            // 4. Sequencial diário simples para a senha da unidade
            $hojeCount = Triagem::where('unidade_id', $totem->unidade_id)
                ->whereDate('created_at', now()->today())
                ->count() + 1;
                
            $senha = $this->manchesterEvaluator->gerarSenha($classificacao, $hojeCount);

            // 5. Salva Triagem no Banco
            $triagem = Triagem::create([
                'unidade_id' => $totem->unidade_id,
                'totem_id' => $totem->id,
                'paciente_id' => $paciente->id,
                'senha_atendimento' => $senha,
                'queixa_principal' => $dados['queixa_principal'],
                'questionario_respostas' => $dados['questionario'],
                'classificacao_risco' => $classificacao,
                'status' => StatusTriagemEnum::AGUARDANDO_IA,
                'data_atendimento' => now(),
            ]);

            // 6. Salva Sinais Vitais vinculados
            $triagem->sinaisVitais()->create([
                'spo2' => $sinaisDTO->spo2,
                'frequencia_cardiaca' => $sinaisDTO->frequenciaCardiaca,
                'temperatura' => $sinaisDTO->temperatura,
                'pressao_sistolica' => $sinaisDTO->pressaoSistolica,
                'pressao_diastolica' => $sinaisDTO->pressaoDiastolica,
                'peso' => $sinaisDTO->peso,
                'altura' => $sinaisDTO->altura,
            ]);

            // 7. Dispara Job Assíncrono para IA (Google Gemini)
            ProcessarAnaliseIaJob::dispatch($triagem->id);

            return $triagem->load(['sinaisVitais', 'paciente']);
        });
    }
}
?>