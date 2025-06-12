<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::post('/products', [ProductController::class, 'fetch']);
Route::post('/customers', [CustomerController::class, 'fetch']);
