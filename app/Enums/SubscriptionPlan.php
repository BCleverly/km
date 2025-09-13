<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionPlan: int
{
    case Free = 0;
    case Solo = 1;
    case Premium = 2;
    case Couple = 3;
    case Lifetime = 4;

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Solo => 'Solo',
            self::Premium => 'Premium',
            self::Couple => 'Couple',
            self::Lifetime => 'Lifetime',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Free => 'Limited access with 1 task per day',
            self::Solo => 'Individual access with unlimited tasks',
            self::Premium => 'Full access with premium features',
            self::Couple => 'Shared access for couples',
            self::Lifetime => 'One-time payment for lifetime access',
        };
    }

    public function price(): int
    {
        return match ($this) {
            self::Free => 0,
            self::Solo => (int) (config('subscription.prices.solo', 1.99) * 100), // Convert to pence
            self::Premium => (int) (config('subscription.prices.premium', 2.99) * 100), // Convert to pence
            self::Couple => (int) (config('subscription.prices.couple', 3.99) * 100), // Convert to pence
            self::Lifetime => (int) (config('subscription.prices.lifetime', 99.99) * 100), // Convert to pence
        };
    }

    public function priceFormatted(): string
    {
        $currency = config('cashier.currency', 'gbp');
        $symbol = match (strtolower($currency)) {
            'gbp' => '£',
            'usd' => '$',
            'eur' => '€',
            default => '£',
        };
        
        return $symbol . number_format($this->price() / 100, 2);
    }

    public function isRecurring(): bool
    {
        return match ($this) {
            self::Free, self::Lifetime => false,
            self::Solo, self::Premium, self::Couple => true,
        };
    }

    public function interval(): ?string
    {
        return match ($this) {
            self::Free, self::Lifetime => null,
            self::Solo, self::Premium, self::Couple => 'month',
        };
    }

    public function stripePriceId(): ?string
    {
        return match ($this) {
            self::Free => null,
            self::Solo => config('subscription.stripe_prices.solo'),
            self::Premium => config('subscription.stripe_prices.premium'),
            self::Couple => config('subscription.stripe_prices.couple'),
            self::Lifetime => config('subscription.stripe_prices.lifetime'),
        };
    }

    public function features(): array
    {
        return match ($this) {
            self::Free => [
                '1 task per day',
                'Basic rewards and punishments',
                'Community content viewing',
            ],
            self::Solo => [
                'Unlimited tasks',
                'All rewards and punishments',
                'Story creation and viewing',
                'Priority support',
            ],
            self::Premium => [
                'Everything in Solo',
                'Premium content access',
                'Advanced analytics',
                'Custom task creation',
                'Image uploads',
            ],
            self::Couple => [
                'Everything in Premium',
                'Shared task management',
                'Partner synchronization',
                'Couple-specific content',
            ],
            self::Lifetime => [
                'Everything in Couple',
                'One-time payment',
                'No recurring charges',
                'Lifetime updates',
            ],
        };
    }

    public function maxTasksPerDay(): ?int
    {
        return match ($this) {
            self::Free => 1,
            self::Solo, self::Premium, self::Couple, self::Lifetime => null, // Unlimited
        };
    }

    public function canCreateStories(): bool
    {
        return match ($this) {
            self::Free => false,
            self::Solo, self::Premium, self::Couple, self::Lifetime => true,
        };
    }

    public function canUploadImages(): bool
    {
        return match ($this) {
            self::Free, self::Solo => false,
            self::Premium, self::Couple, self::Lifetime => true,
        };
    }

    public function canAccessPremiumContent(): bool
    {
        return match ($this) {
            self::Free, self::Solo => false,
            self::Premium, self::Couple, self::Lifetime => true,
        };
    }

    public function canCreateCustomTasks(): bool
    {
        return match ($this) {
            self::Free, self::Solo => false,
            self::Premium, self::Couple, self::Lifetime => true,
        };
    }

    public function isCouplePlan(): bool
    {
        return $this === self::Couple;
    }

    public function isLifetime(): bool
    {
        return $this === self::Lifetime;
    }

    public function isPaid(): bool
    {
        return $this !== self::Free;
    }

    public static function fromStripePriceId(string $priceId): ?self
    {
        foreach (self::cases() as $plan) {
            if ($plan->stripePriceId() === $priceId) {
                return $plan;
            }
        }

        return null;
    }
}
