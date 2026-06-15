<?php

// app/Http/Controllers/TriagemController.php
namespace App\Http\Controllers;

use App\Models\TriagemCategoria;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
   
    public function index()
    {
        
        return view('triagem.index');
    }

    
    public function showSubcategorias($slug)
    {
        $categoriaPai = TriagemCategoria::where('slug', $slug)->firstOrFail();
        $subcategorias = $categoriaPai->subcategorias;

        return view('triagem.subcategorias', compact('categoriaPai', 'subcategorias'));
    }
    public function startQuiz($slug)
    {
        $subcategoria = TriagemCategoria::where('slug', $slug)->with('perguntas')->firstOrFail();
        return view('triagem.quiz', compact('subcategoria'));
    }

       public function finalizar(Request $request)
    {
       
        $scores = $request->input('respostas', [0]);
        $maiorScore = max($scores);

    
        if ($maiorScore >= 11) {
            $resultado = ['cor' => 'Vermelho', 'urgencia' => 'EMERGÊNCIA (Imediato)', 'hex' => '#ef4444'];
        } elseif ($maiorScore >= 8) {
            $resultado = ['cor' => 'Laranja', 'urgencia' => 'MUITO URGENTE (10 min)', 'hex' => '#f97316'];
        } elseif ($maiorScore >= 5) {
            $resultado = ['cor' => 'Amarelo', 'urgencia' => 'URGENTE (60 min)', 'hex' => '#eab308'];
        } else {
            $resultado = ['cor' => 'Verde', 'urgencia' => 'POUCO URGENTE (120 min)', 'hex' => '#22c55e'];
        }

        return view('triagem.resultado', compact('resultado'));
    }
      

}
