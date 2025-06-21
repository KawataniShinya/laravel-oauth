<?php

namespace App\Providers;

use App\Http\Controllers\PassportApproveAuthorizationController;
use App\Http\Controllers\PassportAuthorizationController;
use App\Http\Controllers\PassportDenyAuthorizationController;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // StatefulGuard をバインドする
        $this->app->when(PassportAuthorizationController::class)
            ->needs(StatefulGuard::class)
            ->give(fn () => Auth::guard());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // パスワードグラントなどを有効にする
        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addDays(config('passport.token_expires_days')));
        Passport::refreshTokensExpireIn(now()->addDays(config('passport.refresh_token_expires_days')));

        // スコープ定義を追加(OIDC対応)
        Passport::tokensCan([
            'openid' => 'OpenID Connect scope',
            'profile' => 'Access basic profile info',
            'email' => 'Access email address',
        ]);

        // カスタムのAuthorizationControllerを使用する
        Route::middleware(['web', 'auth'])
        ->prefix('oauth')
            ->group(function () {
                Route::get('/authorize', [PassportAuthorizationController::class, 'authorize'])
                    ->name('passport.authorizations.authorize');
            });
    }
}
