<?php

use App\Http\Controllers\Api\Simulation\SimulationApplicantController;
use Illuminate\Support\Facades\Route;

// ===== NUEVAS RUTAS CON UUID (RECOMENDADAS) =====
// Obtener aplicante por UUID
Route::get('/simulation-applicants/{uuid}', [SimulationApplicantController::class, 'show'])->name('api.simulation-applicants.show');

// Actualizar datos por UUID
Route::put('/simulation-applicants/{uuid}', [SimulationApplicantController::class, 'updateByUuid'])->name('api.simulation-applicants.update-by-uuid');

// Subir foto por UUID
Route::post('/simulation-applicants/{uuid}/upload-photo', [SimulationApplicantController::class, 'uploadPhotoByUuid'])->name('api.simulation-applicants.upload-photo-by-uuid');

// Confirmar datos por UUID
Route::post('/simulation-applicants/{uuid}/confirm', [SimulationApplicantController::class, 'confirmDataByUuid'])->name('api.simulation-applicants.confirm-by-uuid');

// Estado del proceso por UUID
Route::get('/simulation-applicants/{uuid}/status', [SimulationApplicantController::class, 'getStatusByUuid'])->name('api.simulation-applicants.status-by-uuid');

// Verificar si pagó por UUID
Route::get('/simulation-applicants/{uuid}/has-paid', [SimulationApplicantController::class, 'hasPaid'])->name('api.simulation-applicants.has-paid');

// Marcar pago por UUID
Route::post('/simulation-applicants/{uuid}/mark-payment', [SimulationApplicantController::class, 'markPaymentByUuid'])->name('api.simulation-applicants.mark-payment-by-uuid');

// Completar inscripción por UUID
Route::post('/simulation-applicants/{uuid}/complete', [SimulationApplicantController::class, 'completeByUuid'])->name('api.simulation-applicants.complete-by-uuid');

// Actualizar y confirmar por UUID
Route::post('/simulation-applicants/{uuid}/update-and-confirm', [SimulationApplicantController::class, 'updateAndConfirmByUuid'])->name('api.simulation-applicants.update-and-confirm-by-uuid');

// ===== RUTAS SIN UUID (Para registro inicial y busqueda) =====
// Registro nuevo (devuelve UUID)
Route::post('/simulation-applicants', [SimulationApplicantController::class, 'store'])->name('api.simulation-applicants.store');

// Buscar por DNI y email (para recuperar UUID)
Route::post('/simulation-applicants/search', [SimulationApplicantController::class, 'search'])->name('api.simulation-applicants.search');

// ===== RUTAS LEGACY (DEPRECATED - usar rutas con UUID) =====
Route::get('/simulation-applicants/status', [SimulationApplicantController::class, 'getStatus'])->name('api.simulation-applicants.status');
Route::put('/simulation-applicants/update', [SimulationApplicantController::class, 'update'])->name('api.simulation-applicants.update');
Route::post('/simulation-applicants/upload-photo', [SimulationApplicantController::class, 'uploadPhoto'])->name('api.simulation-applicants.upload-photo');
Route::post('/simulation-applicants/confirm', [SimulationApplicantController::class, 'confirmData'])->name('api.simulation-applicants.confirm');
Route::post('/simulation-applicants/mark-payment', [SimulationApplicantController::class, 'markPayment'])->name('api.simulation-applicants.mark-payment');
Route::post('/simulation-applicants/complete', [SimulationApplicantController::class, 'complete'])->name('api.simulation-applicants.complete');
Route::post('/simulation-applicants/update-and-confirm', [SimulationApplicantController::class, 'updateAndConfirm'])->name('api.simulation-applicants.update-and-confirm');
