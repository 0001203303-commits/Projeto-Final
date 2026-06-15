<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController; 
use App\Http\Controllers\TriagemController;
use App\Http\Controllers\PacientesController; 
use App\Http\Controllers\TotemController;

Route::get('/', [TriagemController::class, 'index']);


Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'home']);
    Route::redirect('/index', '/admin');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/listar', [AdminController::class, 'listar']);
    Route::get('/alertas', [AdminController::class, 'alertas']);
    Route::get('/configuracoes', [AdminController::class, 'configuracoes']);

    // Ambas as rotas abaixo agora chamam o PacientesController corretamente
    Route::get('/pacientes', [PacientesController::class, 'index']);
    Route::get('/status_totens', [TotemController::class, 'status_totens']); 
     
    Route::post('/pacientes/salvar', [PacientesController::class, 'salvar'])->name('pacientes.salvar');
    Route::get('/pacientes/deletar/{id}', [PacientesController::class, 'deletar'])->name('pacientes.deletar');
    Route::get('/pacientes/editar/{id}', [PacientesController::class, 'editar'])->name('pacientes.editar');
});