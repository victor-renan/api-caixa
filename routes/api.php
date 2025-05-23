<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
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
    Route::get('/{id}', 'details');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'delete');
  });

Route::controller(ProductController::class)
  ->prefix('/products')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', 'list');
    Route::put('/', 'create');
    Route::get('/{id}', 'details');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'delete');
  });


  Route::controller(TransactionController::class)
  ->prefix('/transactions')
  ->middleware('auth:sanctum')
  ->group(function () {
    Route::get('/', 'list');
    Route::put('/', 'create');
    Route::get('/{id}', 'details');
    Route::patch('/{id}', 'update');
    Route::delete('/{id}', 'delete');
  });

