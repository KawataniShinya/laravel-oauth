<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case USER = 'user';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => '管理者',
            self::STAFF => 'スタッフ',
            self::USER  => '一般ユーザー',
        };
    }

    public function id(): int
    {
        return match($this) {
            self::ADMIN => 1,
            self::STAFF => 2,
            self::USER  => 3,
        };
    }
}
