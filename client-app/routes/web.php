<?php

use App\Http\Controllers\AuthCallbackController;
use App\Http\Controllers\AuthPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AccessTokenController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. ユーザー名入力画面
Route::get('/', function () {
    return Inertia::render('UsernameInput');
});
Route::get('/clear-token', [AccessTokenController::class, 'clear']);

// 2. 登録トークン取得
Route::post('/fetch-token', [AccessTokenController::class, 'fetch']);

Route::middleware(['username.session'])->group(function () {
    // 3. 認証情報入力画面(登録トークンなしの場合)
    Route::get('/credential-input', function () {
        return Inertia::render('CredentialInput', [
            'codeGrantUrl' => request('codeGrantUrl'),
            'clientId' => request('clientId'),
            'redirectUri' => request('redirectUri'),
        ]);
    })->name('credential.input');

    // 4-1. Authorization Code Grant
    Route::post('/auth/password', [AuthPasswordController::class, 'handle']);

    // 4-2. Authorization Code Grant (コールバック)
    Route::get('/auth/callback', [AuthCallbackController::class, 'handle']);

    // 5. リソース選択画面
    Route::get('/resource-selection', function () {
        return Inertia::render('ResourceSelection');
    });

    // 6. 情報取得
    Route::post('/products', [ProductController::class, 'fetch']);
    Route::post('/customers', [CustomerController::class, 'fetch']);
});
