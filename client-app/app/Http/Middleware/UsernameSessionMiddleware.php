<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class UsernameSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): \Inertia\Response|Response
    {
        if (!$request->session()->has('username')) {
            return Inertia::render('UsernameInput', [
                'errorMessage' => 'ユーザーが設定されていません。ユーザー名を入力してください。',
            ]);
        }

        return $next($request);
    }
}
