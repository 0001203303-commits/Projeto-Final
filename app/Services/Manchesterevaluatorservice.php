<?php

namespace App\Services;

use App\DTOs\SinaisVitaisDTO;
use App\Enums\ClassificacaoRiscoEnum;

class ManchesterEvaluatorService
{
    /**
     * Avalia os sinais vitais e retornos do formulário para determinar a cor do Protocolo.
     * Avalia de cima para baixo (do mais crítico para o menos crítico).
     */
    public function avaliar(SinaisVitaisDTO $dto): ClassificacaoRiscoEnum
    {
        // 🔴 VERMELHO (Emergência)
        if ($dto->botaoPanico || $dto->spo2 < 90) {
            return ClassificacaoRiscoEnum::VERMELHO;
        }

        // 🟠 LARANJA (Muito Urgente)
        if (
            ($dto->spo2 >= 90 && $dto->spo2 <= 93) ||
            $dto->frequenciaCardiaca > 120 ||
            $dto->intensidadeDor >= 8
        ) {
            return ClassificacaoRiscoEnum::LARANJA;
        }

        // 🟡 AMARELO (Urgente)
        if (
            $dto->temperatura > 38.5 ||
            ($dto->frequenciaCardiaca >= 100 && $dto->frequenciaCardiaca <= 120) ||
            ($dto->intensidadeDor >= 5 && $dto->intensidadeDor <= 7)
        ) {
            return ClassificacaoRiscoEnum::AMARELO;
        }

        // 🟢 VERDE (Pouco Urgente)
        if ($dto->spo2 >= 94 && $dto->temperatura <= 37.8) {
            return ClassificacaoRiscoEnum::VERDE;
        }

        // 🔵 AZUL (Não Urgente - Padrão para queixas crônicas e estabilidade total)
        return ClassificacaoRiscoEnum::AZUL;
    }

    /**
     * Gera a senha de atendimento baseada no risco.
     * Exemplo: VERM-001, AMA-042
     */
    public function gerarSenha(ClassificacaoRiscoEnum $classificacao, int $sequencialDiario): string
    {
        $prefixo = match ($classificacao) {
            ClassificacaoRiscoEnum::VERMELHO => 'VERM',
            ClassificacaoRiscoEnum::LARANJA  => 'LAR',
            ClassificacaoRiscoEnum::AMARELO  => 'AMA',
            ClassificacaoRiscoEnum::VERDE    => 'VERD',
            ClassificacaoRiscoEnum::AZUL     => 'AZUL',
        };

        return sprintf('%s-%03d', $prefixo, $sequencialDiario);
    }
}

