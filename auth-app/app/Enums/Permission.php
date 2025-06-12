<?php

namespace App\Enums;

enum Permission: string
{
    case READ   = 'read';
    case WRITE  = 'write';
    case DELETE = 'delete';

    public function label(): string
    {
        return match($this) {
            self::READ   => '閲覧',
            self::WRITE  => '更新',
            self::DELETE => '削除',
        };
    }

    public function id(): int
    {
        return match($this) {
            self::READ   => 1,
            self::WRITE  => 2,
            self::DELETE => 3,
        };
    }
}
