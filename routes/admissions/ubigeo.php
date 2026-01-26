<?php

use App\Http\Controllers\Api\UbigeoController;
use Illuminate\Support\Facades\Route;

// Rutas de Ubigeo para selectores en cascada
// Rutas Públicas de Ubigeo
Route::prefix('ubigeos')->group(function () {
    Route::get('/departments', [UbigeoController::class, 'departments']);
    Route::get('/provinces', [UbigeoController::class, 'provinces']);
    Route::get('/districts', [UbigeoController::class, 'districts']);
    Route::get('/{id}', [UbigeoController::class, 'show']);
});