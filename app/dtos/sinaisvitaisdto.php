<?php

namespace App\DTOs;

readonly class SinaisVitaisDTO
{
    public function __construct(
        public int $spo2,
        public int $frequenciaCardiaca,
        public float $temperatura,
        public ?int $pressaoSistolica = null,
        public ?int $pressaoDiastolica = null,
        public ?float $peso = null,
        public ?float $altura = null,
        public int $intensidadeDor = 0,
        public bool $botaoPanico = false,
    ) {}

    public static function fromArray(array $data, int $intensidadeDor = 0, bool $botaoPanico = false): self
    {
        return new self(
            spo2: (int) $data['spo2'],
            frequenciaCardiaca: (int) $data['frequencia_cardiaca'],
            temperatura: (float) $data['temperatura'],
            pressaoSistolica: isset($data['pressao_sistolica']) ? (int) $data['pressao_sistolica'] : null,
            pressaoDiastolica: isset($data['pressao_diastolica']) ? (int) $data['pressao_diastolica'] : null,
            peso: isset($data['peso']) ? (float) $data['peso'] : null,
            altura: isset($data['altura']) ? (float) $data['altura'] : null,
            intensidadeDor: $intensidadeDor,
            botaoPanico: $botaoPanico,
        );
    }
}