<?php

use App\Http\Controllers\Api\PeriodController;
use Illuminate\Support\Facades\Route;

// Protected routes - Periods
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('periods', PeriodController::class);
});
