<?php

namespace App\Jobs;

use App\Models\Triagem;
use App\Services\ResumoIa;
use Illuminate\Foundation\Bus\Dispatchable;

class GerarResumoIa
{
    use Dispatchable;

    public function __construct(public int $triagemId)
    {
    }

    public function handle(ResumoIa $servico): void
    {
        $triagem = Triagem::find($this->triagemId);

        if ($triagem) {
            $servico->gerarComGemini($triagem);
        }
    }
}