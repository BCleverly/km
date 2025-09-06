<?php

declare(strict_types=1);

namespace App;

enum TaskStatus: int
{
    case Assigned = 1;
    case Completed = 2;
    case Failed = 3;

    public function label(): string
    {
        return match($this) {
            self::Assigned => 'Assigned',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Assigned => 'blue',
            self::Completed => 'green',
            self::Failed => 'red',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Assigned;
    }

    public function isFinished(): bool
    {
        return $this === self::Completed || $this === self::Failed;
    }
}
