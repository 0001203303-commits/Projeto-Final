<?php

// app/Http/Controllers/TriagemController.php
namespace App\Http\Controllers;

use App\Models\TriagemCategoria;
use Illuminate\Http\Request;

class TriagemController extends Controller
{
    // Passo 1: Listar Categorias Principais
    public function index()
    {
        $categorias = TriagemCategoria::whereNull('parent_id')->get();
        return view('triagem.index', compact('categorias'));
    }

    // Passo 2: Mostrar Subcategorias baseadas na escolha
    public function showSubcategorias($slug)
    {
        $categoriaPai = TriagemCategoria::where('slug', $slug)->firstOrFail();
        $subcategorias = $categoriaPai->subcategorias;

        return view('triagem.subcategorias', compact('categoriaPai', 'subcategorias'));
    }

    // Passo 3: Carregar o Quiz de perguntas da subcategoria escolhida
    public function startQuiz($slug)
    {
        $subcategoria = TriagemCategoria::where('slug', $slug)->with('perguntas')->firstOrFail();
        return view('triagem.quiz', compact('subcategoria'));
    }

    // Passo 4: Processar as respostas e dar o veredito Manchester
    public function finalizar(Request $request)
    {
        // Recebe as respostas do formulário (Array de scores)
        $scores = $request->input('respostas', [0]);
        $maiorScore = max($scores);

        // Lógica do Protocolo de Manchester
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
    public function home()
    {
        return view('home');
    }
}

?>