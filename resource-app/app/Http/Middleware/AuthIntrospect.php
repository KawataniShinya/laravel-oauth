<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthIntrospect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $requiredPermission, $requiredScope)
    {
        $token = $request->bearerToken();

        $response = Http::withBasicAuth(
            config('auth.introspection.client_id'),
            config('auth.introspection.client_secret')
        )->post(config('auth.introspection.endpoint'), [
            'token' => $token,
        ]);

        if (!$response->ok() || !$response->json('active')) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $permissions = $response->json('permissions', []);
        $scopes = $response->json('scopes', []);

        // 認可チェック
        if (!in_array($requiredPermission, $permissions) || !in_array($requiredScope, $scopes)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
