<?php

declare(strict_types=1);

namespace App\Enums;

enum DesireResponseType: int
{
    case No = 1;
    case Maybe = 2;
    case Yes = 3;

    public function label(): string
    {
        return match ($this) {
            self::No => 'Not for me',
            self::Maybe => 'I\'m curious',
            self::Yes => 'I\'m interested',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::No => '❌',
            self::Maybe => '🤔',
            self::Yes => '✅',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::No => 'red',
            self::Maybe => 'yellow',
            self::Yes => 'green',
        };
    }
}
