<?php

use App\Http\Controllers\AuthCallbackController;
use App\Http\Controllers\AuthPasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InitController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OIDCCallbackController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\UserInfoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. ユーザー名入力画面
Route::get('/', [InitController::class, 'handle']);
Route::get('/clear-token', [AccessTokenController::class, 'clear']);

// 2. 登録トークン取得
Route::post('/fetch-token', [AccessTokenController::class, 'fetch']);

Route::middleware(['username.session'])->group(function () {
    // 3. 認証情報入力画面(登録トークンなしの場合)
    Route::get('/credential-input', function () {
        return Inertia::render('CredentialInput', [
            'codeGrantUrl' => session('codeGrantUrl'),
            'clientId' => session('clientId'),
            'redirectUri' => session('redirectUri'),
            'OIDCClientId' => session('OIDCClientId'),
            'redirectUriOIDC' => session('redirectUriOIDC'),
            'skipAuth' => session('skipAuth', false),
            'skipOIDC' => session('skipOIDC', false),
        ]);
    })->name('credential.input');

    // 4-1. Authorization Password Grant
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

    // 7. OpenID Connect (OIDC) 認証 (コールバック)
    Route::get('/oidc/callback', [OIDCCallbackController::class, 'handle']);

    // 8. ユーザー情報表示
    Route::get('/userinfo', [UserInfoController::class, 'handle'])->name('userinfo');

    // 9. ユーザー詳細情報表示
    Route::get('/userinfo-detail', [UserInfoController::class, 'detail'])->name('userinfo.detail');

    // 10. ログアウト
    Route::post('/logout', [LogoutController::class, 'handle'])->name('logout');
});
