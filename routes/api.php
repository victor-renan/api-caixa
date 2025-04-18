<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
  ->prefix('/auth')
  ->group(function () {
    Route::post('/login', 'login');
    Route::post('/forgot', 'forgot');
    Route::post('/reset', 'reset');
    Route::post('/register', 'register');
    Route::get('/validate', 'validate')->middleware('auth:sanctum');
  });

Route::controller(ClientController::class)
  ->prefix('/clients')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', 'list');
    Route::put('/', 'create');
    Route::get('/{client}', 'details');
    Route::patch('/{client}', 'update');
    Route::delete('/{client}', 'delete');
  });

Route::controller(ProductController::class)
  ->prefix('/products')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', 'list');
    Route::put('/', 'create');
    Route::get('/{product}', 'details');
    Route::patch('/{product}', 'update');
    Route::delete('/{product}', 'delete');
  });


Route::controller(ServiceController::class)
  ->prefix('/services')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', 'list');
    Route::put('/', 'create');
    Route::get('/{service}', 'details');
    Route::patch('/{service}', 'update');
    Route::delete('/{service}', 'delete');
  });
