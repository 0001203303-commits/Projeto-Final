<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CriarTriagemRequest;
use App\Services\TriagemService;
use Illuminate\Http\JsonResponse;

class TriagemController extends Controller
{
    public function __construct(
        private TriagemService $triagemService
    ) {}

    public function store(CriarTriagemRequest $request): JsonResponse
    {
        $triagem = $this->triagemService->registrarTriagem($request->validated());

        return response()->json([
            'message' => 'Triagem registrada com sucesso. Análise de IA em processamento.',
            'data' => [
                'id' => $triagem->id,
                'senha' => $triagem->senha_atendimento,
                'classificacao_risco' => $triagem->classificacao_risco->value,
                'status' => $triagem->status->value,
                'tempo_limite_minutos' => $triagem->classificacao_risco->tempoLimiteMinutos(),
            ],
        ], 201);
    }
}
