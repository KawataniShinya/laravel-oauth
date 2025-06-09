<?php

namespace App\UseCase;

use App\Models\UserToken;

class ClearAccessToken
{
    public function handle(): void
    {
        // ユーザートークンを削除
        UserToken::query()->delete();
    }
}
