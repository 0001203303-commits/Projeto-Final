<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\TriagemController;
use App\Http\Controllers\PacientesController; 
use App\Http\Controllers\TotemController;

// 1. ROTAS PÚBLICAS (Abertas para qualquer um - Terminais e Totens de Triagem)
Route::get('/', [TriagemController::class, 'index'])->name('triagem.index');
Route::get('/triagem/{slug}', [TriagemController::class, 'showSubcategorias'])->name('triagem.subcategorias');
Route::get('/triagem/{slug}/quiz', [TriagemController::class, 'startQuiz'])->name('triagem.quiz');

// 🌟 CORREÇÃO: Movida para fora do admin e livre de autenticação para o Totem salvar com sucesso
Route::post('/pacientes/salvar', [PacientesController::class, 'salvar'])->name('pacientes.salvar');


// 2. ROTAS DE AUTENTICAÇÃO (Não podem ter o middleware 'auth')
Route::get('/login', [AdminController::class, 'login'])->name('login');
Route::post('/login', [AdminController::class, 'loginPost'])->name('login.post');
Route::get('/cadastro', [AdminController::class, 'cadastro'])->name('cadastro');
Route::post('/cadastro', [AdminController::class, 'cadastroPost'])->name('cadastro.post');


// 3. ROTAS PROTEGIDAS (Só entra quem passou pelo login - Painel Médico/Admin)
Route::prefix('admin')->middleware('auth')->group(function () {
    
    Route::get('/', [AdminController::class, 'home'])->name('admin.home');
    Route::redirect('/index', '/admin');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/listar', [AdminController::class, 'listar']);
    Route::get('/alertas', [AdminController::class, 'alertas']);
    Route::get('/configuracoes', [AdminController::class, 'configuracoes']);

    Route::get('/pacientes', [PacientesController::class, 'index']);
    Route::get('/status_totens', [TotemController::class, 'status_totens']); 

    // Removida daqui a rota pacientes.salvar

    Route::get('/pacientes/deletar/{id}', [PacientesController::class, 'deletar'])->name('pacientes.deletar');

    Route::post('/triagem/finalizar', [TriagemController::class, 'finalizar'])->name('triagem.finalizar');
    Route::get('/triagem/finalizar/{id}', [TriagemController::class, 'finalizar'])->name('triagem.finalizar.get');
    
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
});