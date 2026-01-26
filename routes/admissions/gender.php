<?php

use App\Http\Controllers\Api\GenderController;
use Illuminate\Support\Facades\Route;

// Public routes - Genders
Route::get('/genders', [GenderController::class, 'index'])->name('api.genders.index');