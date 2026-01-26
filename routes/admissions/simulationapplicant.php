<?php

use App\Http\Controllers\Api\Simulation\SimulationApplicantController;
use Illuminate\Support\Facades\Route;

// ===== RUTAS PÚBLICAS (Sin autenticación requerida) =====

// Registro nuevo postulante (POST - sin UUID aún)
Route::post('/simulation-applicants', [SimulationApplicantController::class, 'store'])
    ->name('api.simulation-applicants.store');

// Buscar postulante por DNI y email (para obtener UUID)
Route::post('/simulation-applicants/search', [SimulationApplicantController::class, 'search'])
    ->name('api.simulation-applicants.search');

// ===== RUTAS CON UUID (Con autenticación del postulante) =====
// NOTA: Las rutas sin parámetros {uuid} deben ir ANTES de las que tienen {uuid}

// Confirmar datos (POST - UUID en body)
Route::post('/simulation-applicants/confirm', [SimulationApplicantController::class, 'confirmDataByUuid'])
    ->name('api.simulation-applicants.confirm-by-uuid');

// Completar inscripción (POST - UUID en body)
Route::post('/simulation-applicants/complete', [SimulationApplicantController::class, 'completeByUuid'])
    ->name('api.simulation-applicants.complete-by-uuid');

// Obtener información del postulante (GET - UUID en URL)
Route::get('/simulation-applicants/{uuid}', [SimulationApplicantController::class, 'show'])
    ->name('api.simulation-applicants.show');

// Actualizar datos del postulante (POST - UUID en URL)
Route::post('/simulation-applicants/{uuid}', [SimulationApplicantController::class, 'updateByUuid'])
    ->name('api.simulation-applicants.update-by-uuid');

// Subir foto del postulante (POST - UUID en URL)
Route::post('/simulation-applicants/{uuid}/upload-photo', [SimulationApplicantController::class, 'uploadPhotoByUuid'])
    ->name('api.simulation-applicants.upload-photo-by-uuid');

// Obtener estado del proceso de inscripción (GET - UUID en URL)
Route::get('/simulation-applicants/{uuid}/status', [SimulationApplicantController::class, 'getStatusByUuid'])
    ->name('api.simulation-applicants.status-by-uuid');

// Verificar si el postulante ha pagado (GET - UUID en URL)
Route::get('/simulation-applicants/{uuid}/has-paid', [SimulationApplicantController::class, 'hasPaid'])
    ->name('api.simulation-applicants.has-paid');

// Obtener estado de la foto (GET - UUID en URL)
Route::get('/simulation-applicants/{uuid}/photo-status', [SimulationApplicantController::class, 'getPhotoStatus'])
    ->name('api.simulation-applicants.photo-status');

// Marcar pago (POST - UUID en URL)
Route::post('/simulation-applicants/{uuid}/mark-payment', [SimulationApplicantController::class, 'markPaymentByUuid'])
    ->name('api.simulation-applicants.mark-payment-by-uuid');

// Actualizar datos y confirmar en un paso (POST - UUID en URL)
Route::post('/simulation-applicants/{uuid}/update-and-confirm', [SimulationApplicantController::class, 'updateAndConfirmByUuid'])
    ->name('api.simulation-applicants.update-and-confirm-by-uuid');

