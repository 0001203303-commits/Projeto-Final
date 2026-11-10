<?php

use App\Http\Controllers\Api\TotemApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('totem')->middleware(['throttle:60,1', 'totem.token'])->group(function () {
    Route::post('/ping', [TotemApiController::class, 'ping']);
});