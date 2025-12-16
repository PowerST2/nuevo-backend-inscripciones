<?php

use App\Http\Controllers\Api\Simulation\SimulationApplicantController;
use Illuminate\Support\Facades\Route;

// Public routes - Simulation Applicants
Route::get('/simulation-applicants/search', [SimulationApplicantController::class, 'search'])->name('api.simulation-applicants.search');
Route::get('/simulation-applicants/status', [SimulationApplicantController::class, 'getStatus'])->name('api.simulation-applicants.status');
Route::post('/simulation-applicants', [SimulationApplicantController::class, 'store'])->name('api.simulation-applicants.store');
Route::put('/simulation-applicants/update', [SimulationApplicantController::class, 'update'])->name('api.simulation-applicants.update');
Route::post('/simulation-applicants/confirm', [SimulationApplicantController::class, 'confirmData'])->name('api.simulation-applicants.confirm');
Route::post('/simulation-applicants/mark-payment', [SimulationApplicantController::class, 'markPayment'])->name('api.simulation-applicants.mark-payment');
Route::post('/simulation-applicants/complete', [SimulationApplicantController::class, 'complete'])->name('api.simulation-applicants.complete');
