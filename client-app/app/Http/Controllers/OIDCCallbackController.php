<?php

namespace App\Http\Controllers;

use App\UseCase\OIDCCodeGrant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OIDCCallbackController extends Controller
{
    protected OIDCCodeGrant $oidcCodeGrant;

    public function __construct(OIDCCodeGrant $oidcCodeGrant)
    {
        $this->oidcCodeGrant = $oidcCodeGrant;
    }

    public function handle(Request $request)
    {
        $username = $request->session()->get('username');
        $code = $request->input('code');

        if (!$code) {
            return Inertia::render('CredentialInput', [
                'errorMessage' => 'コードが提供されていません。認証されませんでした。',
                'codeGrantUrl' => config('services.auth.code_grant_url'),
                'clientId' => config('services.auth.code_grant_client_id'),
                'redirectUri' => config('services.auth.code_redirect_uri'),
                'OIDCClientId' => config('services.auth.oidc_code_grant_client_id'),
                'redirectUriOIDC' => config('services.auth.oidc_redirect_uri'),
                'skipAuth' => false,
                'skipOIDC' => false,
            ]);
        }

        $response = $this->oidcCodeGrant->handle($username, $code);

        if (!$response || !isset($response['sub'])) {
            return Inertia::render('CredentialInput', [
                'errorMessage' => 'トークン取得失敗',
                'codeGrantUrl' => config('services.auth.code_grant_url'),
                'clientId' => config('services.auth.code_grant_client_id'),
                'redirectUri' => config('services.auth.code_redirect_uri'),
                'OIDCClientId' => config('services.auth.oidc_code_grant_client_id'),
                'redirectUriOIDC' => config('services.auth.oidc_redirect_uri'),
                'skipAuth' => false,
                'skipOIDC' => false,
            ]);
        }

        // セッションにsubを保存
        $request->session()->put('sub', $response['sub']);

        return redirect()->route('userinfo');
    }
}
