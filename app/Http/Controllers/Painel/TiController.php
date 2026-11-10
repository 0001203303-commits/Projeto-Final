<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Services\SaudeSistema;

class TiController extends Controller
{
    public function inicio(SaudeSistema $saude)
    {
        return view('painel.ti.index', ['s' => $saude->coletar()]);
    }

    /** Só o conteúdo, para a atualização automática. */
    public function parcial(SaudeSistema $saude)
    {
        return view('painel.ti._conteudo', ['s' => $saude->coletar()]);
    }
}