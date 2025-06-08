<?php

namespace App\UseCase;

use App\Models\UserToken;
use Illuminate\Support\Facades\Http;

class FetchAccessToken
{
    /**
     * @param string $username
     * @param string $password
     * @return UserToken|null
     */
    public function handle(string $username, string $password): ?UserToken
    {
        // 有効なトークンを検索
        $token = UserToken::where('username', $username)
            ->where('expires_at', '>', now())
            ->first();

        if ($token) {
            return $token;
        }

        // トークン取得リクエスト
        $response = Http::post(config('services.auth.token_url'), [
            'grant_type' => 'password',
            'client_id' => config('services.auth.client_id'),
            'client_secret' => config('services.auth.client_secret'),
            'username' => $username,
            'password' => $password,
            'scope' => '*',
        ]);

        if (!$response->ok()) {
            return null;
        }

        $data = $response->json();

        // トークン保存
        return UserToken::updateOrCreate(
            ['username' => $username],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]
        );
    }
}
