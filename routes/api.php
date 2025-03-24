<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->prefix('/auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/forgot', 'forgot');
    Route::post('/reset', 'reset');
    Route::post('/register', 'register');
    Route::get('/validate', 'validate')->middleware('auth:sanctum');
});