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

        // トークンが見つかった場合はリソース選択へ遷移
        if ($token) {
            return redirect('/resource-selection');
        }

        // トークンが取得できなかった場合はCredentialInputにリダイレクト
        return Inertia::location(
            route('credential.input', [
                'codeGrantUrl' => config('services.auth.code_grant_url'),
                'clientId' => config('services.auth.code_grant_client_id'),
                'redirectUri' => config('services.auth.code_redirect_uri'),
            ])
        );
    }

    public function clear(Request $request)
    {
        $this->clearAccessToken->handle();

        return redirect('/');
    }
}
