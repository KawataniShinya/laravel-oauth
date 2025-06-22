<?php

namespace App\Http\Controllers\OIDC;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Builder;
use Psr\Http\Message\ServerRequestInterface;

class PassportAccessTokenController extends AccessTokenController
{
    public function issueToken(ServerRequestInterface $request): Response|JsonResponse
    {
        $response = parent::issueToken($request);

        $data = json_decode($response->getContent(), true);

        // access_tokenからDBを照会してスコープを取得
        $accessTokenString = $data['access_token'] ?? null;
        $jti = $this->getJti($accessTokenString);

        /** @var Token|null $tokenModel */
        $tokenModel = null;
        $scopes = [];
        if ($accessTokenString) {
            $tokenModel = Token::where('id', $jti)->first();
            if ($tokenModel) {
                $scopes = $tokenModel->scopes ?? [];
            }
        }

        if (in_array('openid', $scopes)) {
            $user = User::find($tokenModel?->user_id);

            if ($user) {
                $idToken = $this->generateIdToken($user, $scopes);
                $data['id_token'] = $idToken;
            }
        }

        return response()->json($data);
    }

    protected function generateIdToken($user, array $scopes): string
    {
        $privateKey = InMemory::file(storage_path('oauth-private.key'));

        $now = new \DateTimeImmutable();
        $encoder = new JoseEncoder();
        $claimFormatter = ChainedFormatter::default();
        $builder = Builder::new($encoder, $claimFormatter);

        $builder = $builder
            ->issuedBy(config('app.url')) // iss
            ->identifiedBy(bin2hex(random_bytes(16))) // jti
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->relatedTo((string) $user->id); // sub（常に必要）

        // profile スコープで付加するクレーム
        if (in_array('profile', $scopes)) {
            $builder = $builder->withClaim('name', $user->name);
        }

        // email スコープで付加するクレーム
        if (in_array('email', $scopes)) {
            $builder = $builder->withClaim('email', $user->email);
            // 任意で email_verified も追加可能
            $builder = $builder->withClaim('email_verified', true); // 仮に常に true とする
        }

        return $builder->getToken(new Sha256(), $privateKey)->toString();
    }

    // todo : IntrospectionController.phpにある同様の処理と合わせて共通化
    /**
     * @param mixed $accessTokenString
     * @return mixed
     */
    private function getJti(mixed $accessTokenString): mixed
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
        $parsedToken = $jwtConfig->parser()->parse($accessTokenString);
        $claims = $parsedToken->claims();
        $jti = $claims->get('jti');

        return $jti;
    }
}
