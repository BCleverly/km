<?php

declare(strict_types=1);

namespace App;

enum ContentStatus: int
{
    case Pending = 1;
    case Approved = 2;
    case InReview = 3;
    case Rejected = 4;

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pending Review',
            self::Approved => 'Approved',
            self::InReview => 'Under Review',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'yellow',
            self::Approved => 'green',
            self::InReview => 'orange',
            self::Rejected => 'red',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::Approved;
    }
}
