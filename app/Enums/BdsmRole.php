<?php

namespace App\Enums;

enum BdsmRole: int
{
    case Dominant = 1;
    case Submissive = 2;
    case Switch = 3;

    /**
     * Get the label for the BDSM role
     */
    public function label(): string
    {
        return match ($this) {
            self::Dominant => 'Dominant',
            self::Submissive => 'Submissive',
            self::Switch => 'Switch',
        };
    }

    /**
     * Get the description for the BDSM role
     */
    public function description(): string
    {
        return match ($this) {
            self::Dominant => 'Takes control and leads in BDSM activities',
            self::Submissive => 'Surrenders control and follows in BDSM activities',
            self::Switch => 'Enjoys both dominant and submissive roles',
        };
    }

    /**
     * Get all available BDSM roles
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
