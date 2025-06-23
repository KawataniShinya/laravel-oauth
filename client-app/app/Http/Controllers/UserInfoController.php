<?php

namespace App\Http\Controllers;

use App\Models\OIDCUser;
use App\UseCase\FetchAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class UserInfoController extends Controller
{
    protected FetchAccessToken $fetchAccessToken;

    public function __construct(FetchAccessToken $fetchAccessToken)
    {
        $this->fetchAccessToken = $fetchAccessToken;
    }

    public function handle(Request $request)
    {
        $sub = $request->session()->get('sub');

        $user = OIDCUser::where('sub', $sub)->first();

        return Inertia::render('UserInfo', [
            'sub' => $sub,
            'name' => $user?->name ?? 'Unknown User',
            'email' => $user?->email ?? 'No Email Provided',
        ]);
    }

    public function detail(Request $request)
    {
        $username = $request->session()->get('username');
        $sub = $request->session()->get('sub');

        $token = $this->fetchAccessToken->handle($username);
        if (!$token) {
            return Inertia::render('ResourceSelection', [
                'errorMessage' => 'トークンが無効です。',
            ]);
        }

        // 認可サーバーの /userinfo API にリクエスト
        $response = Http::withToken($token->access_token)->get(config('services.auth.oidc_userinfo_url'));

        if (!$response->ok()) {
            $status = $response->status();
            $shortMessage = $status === 403
                ? 'Forbidden : アクセスが拒否されました。\'openid\'がスコープに含まれているアクセストークンを使用してください。'
                : 'ユーザー情報の取得に失敗しました';

            return Inertia::render('UserInfo', [
                'sub' => $sub,
                'name' => 'Unknown User',
                'email' => 'No Email Provided',
                'roleId' => null,
                'createdAt' => null,
                'updatedAt' => null,
                'errorMessage' => "{$shortMessage}（HTTP {$status}）",
            ]);
        }

        $data = $response->json();

        return Inertia::render('UserInfo', [
            'sub' => isset($data['sub']) ? (string) $data['sub'] : $sub,
            'name' => $data['name'] ?? 'Unknown User',
            'email' => $data['email'] ?? 'No Email Provided',
            'roleId' => isset($data['role_id']) ? (string) $data['role_id'] : null,
            'createdAt' => $data['created_at'] ?? null,
            'updatedAt' => $data['updated_at'] ?? null,
        ]);
    }
}
