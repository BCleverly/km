<?php

namespace App\Enums;

enum CoupleTaskStatus: int
{
    case Pending = 1;
    case Completed = 2;
    case Failed = 3;
    case Declined = 4;

    /**
     * Get the label for the couple task status
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Declined => 'Declined',
        };
    }

    /**
     * Get the color for the couple task status
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Completed => 'green',
            self::Failed => 'red',
            self::Declined => 'gray',
        };
    }

    /**
     * Check if the task is active (pending)
     */
    public function isActive(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if the task is finished (completed, failed, or declined)
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::Completed, self::Failed, self::Declined]);
    }
}
