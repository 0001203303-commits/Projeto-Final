<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function inicio(Request $request)
    {
        return match ($request->user()->perfil) {
            'medico' => redirect()->route('painel.fila.index'),
            'ti' => redirect()->route('painel.ti'),
            'admin' => redirect()->route('painel.admin'),
            default => abort(403),
        };
    }
}
