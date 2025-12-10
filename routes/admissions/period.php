<?php

use App\Http\Controllers\Api\PeriodController;
use Illuminate\Support\Facades\Route;

// Public routes - Periods
Route::apiResource('period-active', PeriodController::class);
