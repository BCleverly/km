<?php

declare(strict_types=1);

namespace App\Enums;

enum DesireItemType: int
{
    case Fetish = 1;
    case Fantasy = 2;
    case Kink = 3;
    case Toy = 4;
    case Activity = 5;
    case Roleplay = 6;

    public function label(): string
    {
        return match ($this) {
            self::Fetish => 'Fetish',
            self::Fantasy => 'Fantasy',
            self::Kink => 'Kink',
            self::Toy => 'Toy',
            self::Activity => 'Activity',
            self::Roleplay => 'Roleplay',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Fetish => '🔥',
            self::Fantasy => '💭',
            self::Kink => '⚡',
            self::Toy => '🎯',
            self::Activity => '🎭',
            self::Roleplay => '🎪',
        };
    }
}
