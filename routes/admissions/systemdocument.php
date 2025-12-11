<?php

use App\Http\Controllers\Api\SystemDocumentController;
use Illuminate\Support\Facades\Route;

// Public routes - System Documents
Route::get('/system-documents/{name}', [SystemDocumentController::class, 'index'])->name('api.system-documents.index');
