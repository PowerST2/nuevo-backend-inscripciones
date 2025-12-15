<?php

use App\Http\Controllers\Api\ExamSimulationController;
use Illuminate\Support\Facades\Route;

// Public routes - Exam Simulations
Route::get('/exam-simulations', [ExamSimulationController::class, 'index'])->name('api.exam-simulations.index');
Route::post('/exam-simulations/check', [ExamSimulationController::class, 'checkByCode'])->name('api.exam-simulations.check');