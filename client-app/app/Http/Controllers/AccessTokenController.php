<?php

namespace App\Http\Controllers;

use App\UseCase\ClearAccessToken;
use App\UseCase\FetchAccessToken;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccessTokenController extends Controller
{
    protected FetchAccessToken $fetchAccessToken;
    protected ClearAccessToken $clearAccessToken;

    public function __construct(FetchAccessToken $fetchAccessToken, ClearAccessToken $clearAccessToken)
    {
        $this->clearAccessToken = $clearAccessToken;
        $this->fetchAccessToken = $fetchAccessToken;
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);

        $username = $request->username;

        // ユーザー名をセッションに保存
        $request->session()->put('username', $username);

        $token = $this->fetchAccessToken->handle($username);
        $sub = $request->session()->get('sub');

        // CredentialInputにリダイレクト
        // トークンが取得できた場合は認可スキップフラグを設定
        // OIDCはさらにsubがセッションに保存されている場合にOIDC認証スキップフラグを設定
        return redirect()->route('credential.input')->with([
            'codeGrantUrl' => config('services.auth.code_grant_url'),
            'clientId' => config('services.auth.code_grant_client_id'),
            'redirectUri' => config('services.auth.code_redirect_uri'),
            'OIDCClientId' => config('services.auth.oidc_code_grant_client_id'),
            'redirectUriOIDC' => config('services.auth.oidc_redirect_uri'),
            'skipAuth' => isset($token),
            'skipOIDC' => isset($token) && isset($sub),
        ]);
    }

    public function clear(Request $request)
    {
        $this->clearAccessToken->handle();

        return Inertia::render('UsernameInput', [
            'message' => 'トークンがクリアされました。再度トークンを取得してください。',
        ]);
    }
}
