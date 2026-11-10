<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use App\Services\EstatisticasPainel;
use Illuminate\Http\Request;

class PrincipalController extends Controller
{
    public function index(Request $request)
    {
        return view('painel.principal.index', $this->dados($request));
    }

    /** Só o conteúdo, para a atualização automática. */
    public function parcial(Request $request)
    {
        return view('painel.principal._conteudo', $this->dados($request));
    }

    private function dados(Request $request): array
    {
        $periodo = in_array($request->query('periodo'), ['hoje', '7d', '30d'], true)
            ? $request->query('periodo')
            : 'hoje';

        $inicio = match ($periodo) {
            'hoje'  => today(),
            '7d'    => today()->subDays(6),
            '30d'   => today()->subDays(29),
        };

        $aguardando = Triagem::with(['paciente', 'sintomas'])
            ->whereNotNull('cor')
            ->where('status', 'aguardando')
            ->orderByDesc('alerta_imediato')
            ->orderByRaw("FIELD(cor, 'laranja', 'amarelo', 'verde', 'azul')")
            ->orderBy('created_at')
            ->get();

        $e = app(EstatisticasPainel::class)->calcular($periodo, $inicio, $aguardando, $request->user()->id);

        return compact('periodo', 'aguardando', 'e');
    }
}