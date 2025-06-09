<?php

namespace App\UseCase;

use App\Models\UserToken;

class FetchAccessToken
{
    /**
     * @param string $username
     * @return UserToken|null
     */
    public function handle(string $username): ?UserToken
    {
        // 有効なトークンを検索
        return UserToken::where('username', $username)
            ->where('expires_at', '>', now())
            ->first();
    }
}
