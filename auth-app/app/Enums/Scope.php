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
}
