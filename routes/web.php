<?php

use App\Http\Controllers\TriagemController;
use Illuminate\Support\Facades\Route;

Route::get('', [TriagemController::class, 'index'])->name('triagem.index');
