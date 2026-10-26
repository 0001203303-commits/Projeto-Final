<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use App\Models\User;

class AdminController extends Controller
{
    public function inicio()
    {
        $hoje = fn () => Triagem::whereNotNull('cor')->whereDate('created_at', today());

        return view('painel.admin', [
            'totalHoje'     => $hoje()->count(),
            'porCor'        => $hoje()->selectRaw('cor, COUNT(*) as total')->groupBy('cor')->pluck('total', 'cor'),
            'aguardando'    => Triagem::whereNotNull('cor')->where('status', 'aguardando')->count(),
            'emAtendimento' => Triagem::where('status', 'em_atendimento')->count(),
            'usuarios'      => User::where('ativo', true)->count(),
        ]);
    }
}