<?php

use App\Http\Controllers\IntrospectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/oauth/introspect', [IntrospectionController::class, 'introspect'])
    ->middleware('client.credentials')
    ->name('introspection');
