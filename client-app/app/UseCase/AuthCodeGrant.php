<?php

namespace App\UseCase;

use App\Models\UserToken;
use Illuminate\Support\Facades\Http;

class AuthCodeGrant
{
    /**
     * @param string $username
     * @param string $code
     * @return array|null
     */
    public function handle(string $username, string $code): ?array
    {
        $response = Http::post(config('services.auth.token_url'), [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.auth.code_grant_client_id'),
            'client_secret' => config('services.auth.code_grant_client_secret'),
            'redirect_uri' => config('services.auth.code_redirect_uri'),
            'code' => $code,
        ]);

        if (!$response->ok()) {
            return null;
        }

        $data = $response->json();

        UserToken::updateOrCreate(
            ['username' => $username],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]
        );

        return $data;
    }
}
