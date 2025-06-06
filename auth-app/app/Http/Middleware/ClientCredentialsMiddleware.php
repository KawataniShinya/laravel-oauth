<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Symfony\Component\HttpFoundation\Response;

class ClientCredentialsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Basic認証ヘッダーを取得
        $authorizationHeader = $request->header('Authorization');

        // Authorizationヘッダーが存在しない、またはBasic認証でない場合は401エラーを返す
        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Basic ')) {
            return $this->getBasicAuthenticationFailedResponse($request);
        }

        // "Basic xxx==" の xxx== 部分をデコード
        $encodedCredentials = substr($authorizationHeader, 6);
        $decoded = base64_decode($encodedCredentials);

        // デコードに失敗した、または形式が不正な場合は401エラーを返す
        if (!$decoded || !str_contains($decoded, ':')) {
            return $this->getInvalidCredentialsResponse($request);
        }

        [$clientId, $clientSecret] = explode(':', $decoded, 2);

        // クライアントが存在するか確認（client_credentials用で revoked されてないか）
        $client = Client::where('id', $clientId)
            ->where('secret', $clientSecret)
            ->where('password_client', false)
            ->where('personal_access_client', false)
            ->where('revoked', false)
            ->first();

        if (!$client) {
            return $this->getInvalidCredentialsResponse($request);
        }

        // クライアント情報をリクエストにセット
        $request->attributes->set('client', $client);

        return $next($request);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|JsonResponse|\Illuminate\Http\Response|Application
     */
    private function getBasicAuthenticationFailedResponse(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\Foundation\Application
    {
        // JSONレスポンスを期待している場合(APIリクエスト想定)はJSON形式でエラーを返す
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // HTMLレスポンスを期待している場合(ブラウザリクエスト想定)は、Basic認証を要求するレスポンスを返す
        return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic realm="Client Credentials"']);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|JsonResponse|\Illuminate\Http\Response|Application
     */
    private function getInvalidCredentialsResponse(Request $request): \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\Foundation\Application
    {
        // JSONレスポンスを期待している場合(APIリクエスト想定)はJSON形式でエラーを返す
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // HTMLレスポンスを期待している場合(ブラウザリクエスト想定)は、Basic認証を要求するレスポンスを返す
        return response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic realm="Client Credentials"']);
    }
}
