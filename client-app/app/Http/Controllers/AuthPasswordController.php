<?php

namespace App\Http\Controllers;

use App\UseCase\AuthPasswordGrant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthPasswordController extends Controller
{
    protected AuthPasswordGrant $authPasswordGrant;

    public function __construct(AuthPasswordGrant $authPasswordGrant)
    {
        $this->authPasswordGrant = $authPasswordGrant;
    }

    public function handle(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $username = $request->session()->get('username');
        $password = $request->password;

        $response = $this->authPasswordGrant->handle($username, $password);

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
