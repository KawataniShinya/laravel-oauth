<?php

namespace App\Enums;

enum Scope: string
{
    case CONFIDENTIAL = 'confidential';
    case GENERAL = 'general';

    public function label(): string
    {
        return match($this) {
            self::CONFIDENTIAL => '機密情報',
            self::GENERAL      => '一般情報',
        };
    }

    public function id(): int
    {
        return match($this) {
            self::CONFIDENTIAL => 1,
            self::GENERAL      => 2,
        };
    }
}
