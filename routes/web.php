<?php

use App\Http\Controllers\Painel\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Painel\AdminController;
use App\Http\Controllers\Painel\FilaController;
use App\Http\Controllers\Painel\PainelController;
use App\Http\Controllers\Painel\PrincipalController;
use App\Http\Controllers\Painel\TiController;
use App\Http\Controllers\QuestionarioController;
use App\Http\Controllers\TriagemController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Painel\HistoricoController;
use App\Http\Controllers\Painel\TotemController;
use App\Http\Controllers\Painel\BancoApiController;
use App\Http\Controllers\Painel\QuestionarioAdminController;
use App\Http\Controllers\Painel\RelatorioController;    


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
// ----- Painel -----
Route::middleware('auth')->prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [PainelController::class, 'inicio'])->name('inicio');

    // Médico
    Route::middleware('perfil:medico')->group(function () {
        Route::get('/principal', [PrincipalController::class, 'index'])->name('principal');
        Route::get('/principal/parcial', [PrincipalController::class, 'parcial'])->name('principal.parcial');
    });

    Route::middleware('perfil:medico')->prefix('fila')->name('fila.')->group(function () {
        Route::get('/', [FilaController::class, 'index'])->name('index');
        Route::get('/parcial', [FilaController::class, 'parcial'])->name('parcial');
        Route::get('/{triagem}', [FilaController::class, 'mostrar'])->name('mostrar');
        Route::post('/{triagem}/chamar', [FilaController::class, 'chamar'])->name('chamar');
        Route::post('/{triagem}/finalizar', [FilaController::class, 'finalizar'])->name('finalizar');
        Route::post('/{triagem}/comentarios', [FilaController::class, 'comentar'])->name('comentar');
    });

    Route::middleware('perfil:medico')->prefix('historico')->name('historico.')->group(function () {
        Route::get('/', [HistoricoController::class, 'index'])->name('index');
        Route::get('/exportar', [HistoricoController::class, 'exportar'])->name('exportar');
    });

    // Administrador
    Route::middleware('perfil:admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'inicio'])->name('admin');
        Route::get('/admin/parcial', [AdminController::class, 'parcial'])->name('admin.parcial');
    });

        Route::middleware('perfil:admin')->prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/', [RelatorioController::class, 'index'])->name('index');
        Route::get('/exportar/{tipo}', [RelatorioController::class, 'exportar'])
            ->whereIn('tipo', ['volume', 'espera'])->name('exportar');
    });

    Route::middleware('perfil:admin')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/novo', [UsuarioController::class, 'create'])->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
        Route::post('/{usuario}/senha', [UsuarioController::class, 'redefinirSenha'])->name('senha');
        Route::post('/{usuario}/alternar', [UsuarioController::class, 'alternar'])->name('alternar');
        Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy');
    });

    // Técnico de TI
    Route::middleware('perfil:ti')->group(function () {
        Route::get('/ti', [TiController::class, 'inicio'])->name('ti');
        Route::get('/ti/parcial', [TiController::class, 'parcial'])->name('ti.parcial');
    });

    Route::middleware('perfil:ti')->prefix('totens')->name('totens.')->group(function () {
        Route::get('/', [TotemController::class, 'index'])->name('index');
        Route::get('/parcial', [TotemController::class, 'parcial'])->name('parcial');
        Route::get('/novo', [TotemController::class, 'create'])->name('create');
        Route::post('/', [TotemController::class, 'store'])->name('store');
        Route::get('/{totem}/editar', [TotemController::class, 'edit'])->name('edit');
        Route::put('/{totem}', [TotemController::class, 'update'])->name('update');
        Route::post('/{totem}/token', [TotemController::class, 'novoToken'])->name('token');
        Route::post('/{totem}/alternar', [TotemController::class, 'alternar'])->name('alternar');
        Route::delete('/{totem}', [TotemController::class, 'destroy'])->name('destroy');
    });
});

    Route::middleware('perfil:admin')->group(function () {
        Route::get('/admin', [PrincipalController::class, 'index'])->name('admin');
        Route::get('/admin/parcial', [PrincipalController::class, 'parcial'])->name('admin.parcial');
    });

    Route::middleware(['auth', 'perfil:admin'])->prefix('painel/questionario')->name('painel.questionario.')->group(function () {
        Route::get('/', [QuestionarioAdminController::class, 'index'])->name('index');

        Route::get('/categorias/nova', [QuestionarioAdminController::class, 'novaCategoria'])->name('categorias.create');
        Route::post('/categorias', [QuestionarioAdminController::class, 'criarCategoria'])->name('categorias.store');
        Route::get('/categorias/{categoria}/editar', [QuestionarioAdminController::class, 'editarCategoria'])->name('categorias.edit');
        Route::put('/categorias/{categoria}', [QuestionarioAdminController::class, 'atualizarCategoria'])->name('categorias.update');
        Route::post('/categorias/{categoria}/alternar', [QuestionarioAdminController::class, 'alternarCategoria'])->name('categorias.alternar');
        Route::delete('/categorias/{categoria}', [QuestionarioAdminController::class, 'excluirCategoria'])->name('categorias.destroy');

        Route::get('/sintomas/novo', [QuestionarioAdminController::class, 'novoSintoma'])->name('sintomas.create');
        Route::post('/sintomas', [QuestionarioAdminController::class, 'criarSintoma'])->name('sintomas.store');
        Route::get('/sintomas/{sintoma}/editar', [QuestionarioAdminController::class, 'editarSintoma'])->name('sintomas.edit');
        Route::put('/sintomas/{sintoma}', [QuestionarioAdminController::class, 'atualizarSintoma'])->name('sintomas.update');
        Route::post('/sintomas/{sintoma}/alternar', [QuestionarioAdminController::class, 'alternarSintoma'])->name('sintomas.alternar');
        Route::delete('/sintomas/{sintoma}', [QuestionarioAdminController::class, 'excluirSintoma'])->name('sintomas.destroy');

        Route::get('/perguntas/nova', [QuestionarioAdminController::class, 'novaPergunta'])->name('perguntas.create');
        Route::post('/perguntas', [QuestionarioAdminController::class, 'criarPergunta'])->name('perguntas.store');
        Route::get('/perguntas/{pergunta}/editar', [QuestionarioAdminController::class, 'editarPergunta'])->name('perguntas.edit');
        Route::put('/perguntas/{pergunta}', [QuestionarioAdminController::class, 'atualizarPergunta'])->name('perguntas.update');
        Route::post('/perguntas/{pergunta}/alternar', [QuestionarioAdminController::class, 'alternarPergunta'])->name('perguntas.alternar');
        Route::delete('/perguntas/{pergunta}', [QuestionarioAdminController::class, 'excluirPergunta'])->name('perguntas.destroy');
    });

// ----- Histórico -----
    Route::middleware('perfil:medico')->prefix('historico')->name('historico.')->group(function () {
    Route::get('/', [HistoricoController::class, 'index'])->name('index');
    Route::get('/exportar', [HistoricoController::class, 'exportar'])->name('exportar');
    });   

