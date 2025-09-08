<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case MONTHLY = 'monthly';
    case COUPLE = 'couple';
    case LIFETIME = 'lifetime';

    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::MONTHLY => 'Monthly',
            self::COUPLE => 'Couple',
            self::LIFETIME => 'Lifetime',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FREE => 'Basic features with limited access',
            self::MONTHLY => 'Full access to all features',
            self::COUPLE => 'Full access for couples with shared features',
            self::LIFETIME => 'One-time payment for lifetime access',
        };
    }

    public function price(): int
    {
        return match ($this) {
            self::FREE => 0,
            self::MONTHLY => 299, // £2.99 in pence
            self::COUPLE => 399,  // £3.99 in pence
            self::LIFETIME => 9900, // £99.00 in pence
        };
    }

    public function formattedPrice(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::MONTHLY => '£2.99/month',
            self::COUPLE => '£3.99/month',
            self::LIFETIME => '£99.00 (one-time)',
        };
    }

    public function stripePriceId(): ?string
    {
        return match ($this) {
            self::FREE => null,
            self::MONTHLY => config('subscription.stripe_prices.monthly'),
            self::COUPLE => config('subscription.stripe_prices.couple'),
            self::LIFETIME => config('subscription.stripe_prices.lifetime'),
        };
    }

    public function isRecurring(): bool
    {
        return match ($this) {
            self::FREE => false,
            self::MONTHLY => true,
            self::COUPLE => true,
            self::LIFETIME => false,
        };
    }

    public function trialDays(): int
    {
        return match ($this) {
            self::FREE => 0,
            self::MONTHLY => 14, // 2 weeks
            self::COUPLE => 14,  // 2 weeks
            self::LIFETIME => 0,
        };
    }

    public function features(): array
    {
        return match ($this) {
            self::FREE => [
                'Limited task assignments',
                'Basic rewards and punishments',
                'Community access',
            ],
            self::MONTHLY => [
                'Unlimited task assignments',
                'Full rewards and punishments library',
                'Priority support',
                'Advanced analytics',
                'Custom task creation',
            ],
            self::COUPLE => [
                'Everything in Monthly',
                'Shared couple dashboard',
                'Partner task assignments',
                'Couple-specific content',
                'Joint progress tracking',
            ],
            self::LIFETIME => [
                'Everything in Couple',
                'No recurring payments',
                'Lifetime updates',
                'Premium support',
                'Early access to new features',
            ],
        };
    }

    public static function fromStripePriceId(string $priceId): ?self
    {
        return match ($priceId) {
            config('subscription.stripe_prices.monthly') => self::MONTHLY,
            config('subscription.stripe_prices.couple') => self::COUPLE,
            config('subscription.stripe_prices.lifetime') => self::LIFETIME,
            default => null,
        };
    }
}