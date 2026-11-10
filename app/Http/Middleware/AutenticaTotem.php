<?php

namespace App\Http\Middleware;

use App\Models\Totem;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutenticaTotem
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        $totem = $token
            ? Totem::where('token_hash', hash('sha256', $token))->where('ativo', true)->first()
            : null;

        if (! $totem) {
            return response()->json(['erro' => 'Token inválido ou totem desativado.'], 401);
        }

        $request->attributes->set('totem', $totem);

        return $next($request);
    }
}