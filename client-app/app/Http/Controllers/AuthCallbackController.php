<?php

namespace App\Http\Controllers;

use App\UseCase\AuthCodeGrant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthCallbackController extends Controller
{
    protected AuthCodeGrant $authCodeGrant;

    public function __construct(AuthCodeGrant $authCodeGrant)
    {
        $this->authCodeGrant = $authCodeGrant;
    }

    public function handle(Request $request)
    {
        $username = $request->session()->get('username');
        $code = $request->input('code');

        if (!$code) {
            return Inertia::render('CredentialInput', [
                'errorMessage' => '認可要求が許可されませんでした',
                'codeGrantUrl' => config('services.auth.code_grant_url'),
                'clientId' => config('services.auth.code_grant_client_id'),
                'redirectUri' => config('services.auth.code_redirect_uri'),
            ]);
        }

        $response = $this->authCodeGrant->handle($username, $code);

        if (!$response) {
            return Inertia::render('CredentialInput', [
                'errorMessage' => 'トークン取得失敗',
                'codeGrantUrl' => config('services.auth.code_grant_url'),
                'clientId' => config('services.auth.code_grant_client_id'),
                'redirectUri' => config('services.auth.code_redirect_uri'),
            ]);
        }

        return redirect('/resource-selection');
    }
}
