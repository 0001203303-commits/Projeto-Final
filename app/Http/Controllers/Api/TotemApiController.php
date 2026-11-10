<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TotemApiController extends Controller
{
    /** O totem chama este endereço a cada ~60 s para avisar que está funcionando. */
    public function ping(Request $request)
    {
        $totem = $request->attributes->get('totem');

        $versao = trim((string) $request->input('versao', ''));

        $totem->forceFill([
            'ultimo_contato_em' => now(),
            'ultimo_ip'         => $request->ip(),
            'versao'            => $versao !== '' ? Str::limit($versao, 30, '') : $totem->versao,
        ])->save();

        return response()->json([
            'ok'            => true,
            'totem'         => $totem->nome,
            'hora_servidor' => now()->toIso8601String(),
        ]);
    }
}