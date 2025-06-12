<?php

namespace App\UseCase;

use App\Models\UserToken;
use Illuminate\Support\Facades\Http;

class AuthPasswordGrant
{
    /**
     * @param string $username
     * @param string $password
     * @return array|null
     */
    public function handle(string $username, string $password): ?array
    {
        $response = Http::post(config('services.auth.token_url'), [
            'grant_type' => 'password',
            'client_id' => config('services.auth.password_grant_client_id'),
            'client_secret' => config('services.auth.password_grant_client_secret'),
            'username' => $username,
            'password' => $password,
            'scope' => '*',
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
