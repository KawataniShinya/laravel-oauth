<?php

namespace App\UseCase;

use App\Models\OIDCUser;
use App\Models\UserToken;
use Illuminate\Support\Facades\Http;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class OIDCCodeGrant
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
            'client_id' => config('services.auth.oidc_code_grant_client_id'),
            'client_secret' => config('services.auth.oidc_code_grant_client_secret'),
            'redirect_uri' => config('services.auth.oidc_redirect_uri'),
            'code' => $code,
        ]);

        if (!$response->ok()) {
            return null;
        }

        $data = $response->json();

        $claims = $this->getClaims($data['id_token']);

        $data['sub'] = $claims->get('sub');

        // トークン情報の更新
        UserToken::updateOrCreate(
            ['username' => $username],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_at' => now()->addSeconds($data['expires_in']),
            ]
        );

        // OIDCUser テーブル更新
        OIDCUser::updateOrCreate(
            ['sub' => $claims->get('sub')],
            [
                'name' => $claims->get('name'),
                'email' => $claims->get('email'),
            ]
        );

        return $data;
    }

    /**
     * @param $id_token
     * @return mixed
     */
    public function getClaims($id_token)
    {
        // 秘密鍵の設定
        $privateKeyPath = base_path('storage/oauth-private.key');
        // 公開鍵の設定
        $publicKeyPath = base_path('storage/oauth-public.key');

        $jwtConfig = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($privateKeyPath),
            InMemory::file($publicKeyPath)
        );
        $parsedToken = $jwtConfig->parser()->parse($id_token);
        $claims = $parsedToken->claims();
        return $claims;
    }
}
