<?php

use App\Http\Controllers\Api\Simulation\SimulationApplicantController;
use Illuminate\Support\Facades\Route;

// ===== RUTAS SIN UUID (Para registro inicial y busqueda) =====
// Registro nuevo (devuelve UUID)
Route::post('/simulation-applicants', [SimulationApplicantController::class, 'store'])->name('api.simulation-applicants.store');

// Buscar por DNI y email (para recuperar UUID)
Route::post('/simulation-applicants/search', [SimulationApplicantController::class, 'search'])->name('api.simulation-applicants.search');

// ===== NUEVAS RUTAS CON UUID (RECOMENDADAS) =====
// Confirmar datos por UUID (UUID en body) - DEBE IR ANTES DE LA RUTA CON {uuid}
Route::put('/simulation-applicants/confirm', [SimulationApplicantController::class, 'confirmDataByUuid'])->name('api.simulation-applicants.confirm-by-uuid');

// Completar inscripción por UUID (UUID en body)
Route::put('/simulation-applicants/complete', [SimulationApplicantController::class, 'completeByUuid'])->name('api.simulation-applicants.complete-by-uuid');

// Obtener estado del proceso por UUID
Route::get('/simulation-applicants/{uuid}/status', [SimulationApplicantController::class, 'getStatusByUuid'])->name('api.simulation-applicants.status-by-uuid');

// Actualizar y confirmar por UUID
Route::post('/simulation-applicants/{uuid}/update-and-confirm', [SimulationApplicantController::class, 'updateAndConfirmByUuid'])->name('api.simulation-applicants.update-and-confirm-by-uuid');

// ===== RUTAS LEGACY (DEPRECATED - usar rutas con UUID) =====
Route::get('/simulation-applicants/status', [SimulationApplicantController::class, 'getStatus'])->name('api.simulation-applicants.status');
Route::put('/simulation-applicants/update', [SimulationApplicantController::class, 'update'])->name('api.simulation-applicants.update');
Route::post('/simulation-applicants/upload-photo', [SimulationApplicantController::class, 'uploadPhoto'])->name('api.simulation-applicants.upload-photo');
Route::post('/simulation-applicants/confirm', [SimulationApplicantController::class, 'confirmData'])->name('api.simulation-applicants.confirm');
Route::post('/simulation-applicants/mark-payment', [SimulationApplicantController::class, 'markPayment'])->name('api.simulation-applicants.mark-payment');
Route::post('/simulation-applicants/complete', [SimulationApplicantController::class, 'complete'])->name('api.simulation-applicants.complete');
Route::post('/simulation-applicants/update-and-confirm', [SimulationApplicantController::class, 'updateAndConfirm'])->name('api.simulation-applicants.update-and-confirm');
