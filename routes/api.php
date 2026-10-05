<?php

use App\Http\Controllers\Api\V1\TriagemController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/triagem', [TriagemController::class, 'store']);
});