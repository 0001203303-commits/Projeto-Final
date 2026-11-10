<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PerfilMiddleware
{
        public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        $usuario = $request->user();

        // Conta desativada com sessão ainda aberta: encerra na hora
        if ($usuario && ! $usuario->ativo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Sua conta está desativada.']);
        }

        if (! $usuario || ! in_array($usuario->perfil, $perfis, true)) {
            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
