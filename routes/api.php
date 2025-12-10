<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes
require __DIR__.'/auth.php';

// Schedule Activity routes
require __DIR__.'/admissions/scheduleactivity.php';

// Period routes
require __DIR__.'/admissions/period.php';
