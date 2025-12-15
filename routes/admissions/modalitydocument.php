<?php

use App\Http\Controllers\Api\ModalityDocumentController;
use Illuminate\Support\Facades\Route;

// Public routes - Modality Documents
Route::get('/modality-documents', [ModalityDocumentController::class, 'index'])->name('api.modality-documents.index');
