<?php

declare(strict_types=1);

namespace App;

enum TargetUserType: int
{
    case Male = 1;
    case Female = 2;
    case Couple = 3;
    case Any = 4;

    public function label(): string
    {
        return match($this) {
            self::Male => 'Male',
            self::Female => 'Female',
            self::Couple => 'Couple',
            self::Any => 'Anyone',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::Male => 'For individual male users',
            self::Female => 'For individual female users',
            self::Couple => 'For couple accounts',
            self::Any => 'For all user types',
        };
    }
}
