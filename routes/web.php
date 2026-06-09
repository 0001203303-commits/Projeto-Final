<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; // Se tiver controllers importados, ficam aqui
use App\Http\Controllers\TriagemController;

// Suas rotas começam aqui...
use App\Models\Categoria; // Garanta que o Model está importado no topo do arquivo

Route::get('/', function () {
    // 1. Busca todas as categorias do banco de dados
    $categorias = Categoria::all(); 

    // 2. Envia a variável $categorias para a view
    return view('triagem.index', compact('categorias')); 
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/', [AdminController::class, 'home']);
    Route::redirect('/index', '/admin');
    Route::get('/listar', [AdminController::class, 'listar']);
    Route::get('/status_totens', [AdminController::class, 'status_totens']);
    Route::get('/pacientes', [AdminController::class, 'pacientes']);
    Route::get('/alertas', [AdminController::class, 'alertas']);
    Route::get('/configuracoes', [AdminController::class, 'configuracoes']);

});

//Rotas para o usuário

Route::prefix('totem/triagem')->group(function () {
    Route::get('/', [TriagemController::class, 'index'])->name('triagem.index');
    Route::get('/categoria/{slug}', [TriagemController::class, 'showSubcategorias'])->name('triagem.subs');
    Route::get('/sintoma/{slug}', [TriagemController::class, 'startQuiz'])->name('triagem.quiz');
    Route::post('/finalizar', [TriagemController::class, 'finalizar'])->name('triagem.finalizar');
});