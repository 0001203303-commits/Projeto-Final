<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Services\EstatisticasAdmin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function inicio(Request $request)
    {
        return view('painel.admin.index', $this->dados($request));
    }

    /** Só o conteúdo, para a atualização automática. */
    public function parcial(Request $request)
    {
        return view('painel.admin._conteudo', $this->dados($request));
    }

    private function dados(Request $request): array
    {
        $periodo = in_array($request->query('periodo'), ['hoje', '7d', '30d'], true)
            ? $request->query('periodo')
            : 'hoje';

        $inicio = match ($periodo) {
            'hoje' => today(),
            '7d'   => today()->subDays(6),
            '30d'  => today()->subDays(29),
        };

        $e = app(EstatisticasAdmin::class)->calcular($periodo, $inicio);

        return compact('periodo', 'e');
    }
}