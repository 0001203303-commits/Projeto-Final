<?php
namespace App\Enums;

enum ClassificacaoRiscoEnum: string
{
    case VERMELHO = 'VERMELHO'; // Emergência
    case LARANJA  = 'LARANJA';  // Muito Urgente
    case AMARELO  = 'AMARELO';  // Urgente
    case VERDE    = 'VERDE';    // Pouco Urgente
    case AZUL     = 'AZUL';     // Não Urgente

    public function tempoLimiteMinutos(): int
    {
        return match ($this) {
            self::VERMELHO => 0,
            self::LARANJA  => 10,
            self::AMARELO  => 60,
            self::VERDE    => 120,
            self::AZUL     => 240,
        };
    }
}
?>