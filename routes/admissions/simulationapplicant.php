<?php

use App\Http\Controllers\Api\Simulation\SimulationApplicantController;
use Illuminate\Support\Facades\Route;

// Public routes - Simulation Applicants
Route::get('/simulation-applicants/search', [SimulationApplicantController::class, 'search'])->name('api.simulation-applicants.search');
Route::post('/simulation-applicants', [SimulationApplicantController::class, 'store'])->name('api.simulation-applicants.store');
