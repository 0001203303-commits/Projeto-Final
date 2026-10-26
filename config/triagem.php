<?php

return [
    // Pontuação total mínima para cada cor
    'limiares' => ['laranja' => 14, 'amarelo' => 8, 'verde' => 3],

    // Sinais vitais são medidas objetivas, por isso pesam mais
    'peso_vitais' => 2,

    // Equilíbrio do score de sintomas
    'limite_por_sintoma'  => 10,
    'peso_secundarios'    => 0.5,
    'limite_sintomas'     => 20,

    'idade_pediatrica' => 14,

    // Valores que disparam aviso imediato à enfermagem
    'alerta_imediato' => ['spo2' => 85, 'pas' => 80],

    'cores' => [
        'laranja' => ['nome' => 'Laranja', 'descricao' => 'Muito urgente', 'tempo' => 'até 10 minutos','limite_min' => 10],
        'amarelo' => ['nome' => 'Amarelo', 'descricao' => 'Urgente',       'tempo' => 'até 60 minutos','limite_min' => 60],
        'verde'   => ['nome' => 'Verde',   'descricao' => 'Pouco urgente', 'tempo' => 'até 120 minutos','limite_min' => 120],
        'azul'    => ['nome' => 'Azul',    'descricao' => 'Não urgente',   'tempo' => 'atendimento ambulatorial','limite_min' => null],
    ],
];