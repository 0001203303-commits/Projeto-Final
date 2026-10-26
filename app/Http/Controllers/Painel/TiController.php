<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Triagem;
use Illuminate\Support\Facades\DB;

class TiController extends Controller
{
    public function inicio()
    {
        try {
            DB::select('select 1');
            $banco = true;
        } catch (\Throwable) {
            $banco = false;
        }

        $ultimas24h = fn () => Triagem::whereNotNull('cor')->where('created_at', '>=', now()->subDay());

        return view('painel.ti', [
            'banco'         => $banco,
            'iaConfigurada' => filled(config('services.gemini.key')),
            'modelo'        => config('services.gemini.model'),
            'triagens24h'   => $ultimas24h()->count(),
            'comIa24h'      => $ultimas24h()->where('resumo_ia', 'like', '%"origem":"gemini"%')->count(),
            'laravel'       => app()->version(),
            'php'           => PHP_VERSION,
            'ambiente'      => app()->environment(),
        ]);
    }
}