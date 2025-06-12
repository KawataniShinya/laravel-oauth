<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth.introspect:read,general')->get('/products', [ProductController::class, 'index']);

Route::middleware('auth.introspect:read,confidential')->get('/customers', [CustomerController::class, 'index']);
