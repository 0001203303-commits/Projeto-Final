<?php

use App\Http\Controllers\QuestionarioController;
use App\Http\Controllers\TriagemController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Painel\AdminController;
use App\Http\Controllers\Painel\FilaController;
use App\Http\Controllers\Painel\PainelController;
use App\Http\Controllers\Painel\TiController;

Route::get('/', [TriagemController::class, 'inicio'])->name('triagem.inicio');

Route::prefix('triagem')->name('triagem.')->group(function () {
    Route::get('/dados', [TriagemController::class, 'dados'])->name('dados');
    Route::post('/dados', [TriagemController::class, 'salvarDados'])->name('dados.salvar');
    Route::get('/vitais', [TriagemController::class, 'vitais'])->name('vitais');
    Route::post('/vitais', [TriagemController::class, 'salvarVitais'])->name('vitais.salvar');

    Route::get('/sintomas', [QuestionarioController::class, 'sintomas'])->name('sintomas');
    Route::post('/sintomas', [QuestionarioController::class, 'salvarSintomas'])->name('sintomas.salvar');
    Route::get('/perguntas', [QuestionarioController::class, 'perguntas'])->name('perguntas');
    Route::post('/perguntas', [QuestionarioController::class, 'salvarPerguntas'])->name('perguntas.salvar');
    Route::get('/resultado', [QuestionarioController::class, 'resultado'])->name('resultado');

    Route::post('/encerrar', [QuestionarioController::class, 'encerrar'])->name('encerrar');

    

});

// ----- Autenticação -----
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'form'])->name('login');
    Route::post('/login', [LoginController::class, 'entrar'])->middleware('throttle:5,1')->name('login.entrar');
});

Route::post('/logout', [LoginController::class, 'sair'])->middleware('auth')->name('logout');

// ----- Painel -----
Route::middleware('auth')->prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [PainelController::class, 'inicio'])->name('inicio');

    Route::middleware('perfil:medico')->prefix('fila')->name('fila.')->group(function () {
        Route::get('/', [FilaController::class, 'index'])->name('index');
        Route::get('/parcial', [FilaController::class, 'parcial'])->name('parcial');
        Route::get('/{triagem}', [FilaController::class, 'mostrar'])->name('mostrar');
        Route::post('/{triagem}/chamar', [FilaController::class, 'chamar'])->name('chamar');
        Route::post('/{triagem}/finalizar', [FilaController::class, 'finalizar'])->name('finalizar');
    });

    Route::get('/admin', [AdminController::class, 'inicio'])->middleware('perfil:admin')->name('admin');
    Route::get('/ti', [TiController::class, 'inicio'])->middleware('perfil:ti')->name('ti');
});
