<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class IntrospectionController extends Controller
{
    protected Configuration $jwtConfig;

    public function __construct()
    {
        // 秘密鍵の設定
        $privateKeyPath = base_path('storage/oauth-private.key');
        // 公開鍵の設定
        $publicKeyPath = base_path('storage/oauth-public.key');

        if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath)) {
            Log::error('OAuth keys not found: ' . $privateKeyPath . ' or ' . $publicKeyPath);
            throw new \RuntimeException('OAuth keys not found');
        }

        // JWTの設定
        $this->jwtConfig = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file($privateKeyPath),
            InMemory::file($publicKeyPath)
        );
    }

    public function introspect(Request $request): JsonResponse
    {
        $tokenString = $request->input('token');

        if (!$tokenString) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        try {
            // トークン取得
            $token = $this->getToken($tokenString);
            if (!$token || $token->revoked || $token->expires_at->isPast()) {
                return response()->json(['active' => false]);
            }

            // ユーザー情報の取得
            $user = User::with([
                'role.permissions',
                'role.scopes',
            ])->find($token->user_id);
            if (!$user) {
                return response()->json(['active' => false]);
            }

            // パーミッションとスコープの取得
            $permissionNames = $user->role->permissions->pluck('name')->unique()->values();
            $scopeNames = $user->role->scopes->pluck('name')->unique()->values();

            return response()->json([
                'active' => true,
                'client_id' => $token->client_id,
                'username' => $user->email,
                'permissions' => $permissionNames,
                'scopes' => $scopeNames,
                'exp' => Carbon::parse($token->expires_at)->timestamp,
                'sub' => $user->id,
                'iss' => config('app.url'),
                'token_type' => 'access_token',
            ]);
        } catch (\Throwable $e) {
            Log::error('Introspection failed: ' . $e->getMessage());
            return response()->json(['active' => false]);
        }
    }

    /**
     * @param string $tokenString
     * @return Token|null
     */
    private function getToken(string $tokenString): ?Token
    {
        // jti（JWT ID）を取得
        $parsedToken = $this->jwtConfig->parser()->parse($tokenString);
        $claims = $parsedToken->claims();
        $jti = $claims->get('jti');

        $token = Token::find($jti);
        if (!$token) {
            Log::error('Token not found: ' . $jti);
            return null;
        }

        return $token;
    }
}
