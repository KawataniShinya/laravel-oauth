<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
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
    }
}
