<?php

use App\Http\Controllers\Api\ScheduleActivityController;
use Illuminate\Support\Facades\Route;

// Public routes - Schedule Activities (solo calendarios activos)
Route::post('/schedule-activities', [ScheduleActivityController::class, 'index']);