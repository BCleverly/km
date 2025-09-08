<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan enum.
     */
    public function getPlanAttribute(): ?SubscriptionPlan
    {
        if (!$this->stripe_price) {
            return null;
        }

        return SubscriptionPlan::fromStripePriceId($this->stripe_price);
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->stripe_status === 'active' || $this->onTrial();
    }

    /**
     * Check if the subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the subscription is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->stripe_status === 'canceled' || $this->stripe_status === 'cancelled';
    }

    /**
     * Check if the subscription is past due.
     */
    public function isPastDue(): bool
    {
        return $this->stripe_status === 'past_due';
    }

    /**
     * Check if the subscription is incomplete.
     */
    public function isIncomplete(): bool
    {
        return $this->stripe_status === 'incomplete';
    }

    /**
     * Check if the subscription is incomplete and expired.
     */
    public function isIncompleteExpired(): bool
    {
        return $this->stripe_status === 'incomplete_expired';
    }

    /**
     * Check if the subscription is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->stripe_status === 'unpaid';
    }

    /**
     * Get the subscription plan name.
     */
    public function getPlanNameAttribute(): string
    {
        return $this->plan?->label() ?? 'Unknown';
    }

    /**
     * Get the subscription plan description.
     */
    public function getPlanDescriptionAttribute(): string
    {
        return $this->plan?->description() ?? '';
    }

    /**
     * Get the subscription plan features.
     */
    public function getPlanFeaturesAttribute(): array
    {
        return $this->plan?->features() ?? [];
    }

    /**
     * Get the subscription plan price.
     */
    public function getPlanPriceAttribute(): int
    {
        return $this->plan?->price() ?? 0;
    }

    /**
     * Get the formatted subscription plan price.
     */
    public function getFormattedPlanPriceAttribute(): string
    {
        return $this->plan?->formattedPrice() ?? 'Free';
    }

    /**
     * Check if the subscription has a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->plan_features);
    }

    /**
     * Get the subscription limits.
     */
    public function getLimitsAttribute(): array
    {
        if (!$this->plan) {
            return config('subscription.limits.free', []);
        }

        return config("subscription.limits.{$this->plan->value}", []);
    }

    /**
     * Check if the subscription allows a specific action.
     */
    public function can(string $action): bool
    {
        $limits = $this->limits;
        
        return $limits[$action] ?? false;
    }

    /**
     * Get the maximum number of active outcomes allowed.
     */
    public function getMaxActiveOutcomesAttribute(): ?int
    {
        return $this->limits['max_active_outcomes'] ?? null;
    }

    /**
     * Get the maximum number of tasks per day.
     */
    public function getMaxTasksPerDayAttribute(): ?int
    {
        return $this->limits['max_tasks_per_day'] ?? null;
    }

    /**
     * Check if the user can create content.
     */
    public function canCreateContent(): bool
    {
        return $this->can('can_create_content');
    }

    /**
     * Check if the user can access premium content.
     */
    public function canAccessPremiumContent(): bool
    {
        return $this->can('can_access_premium_content');
    }

    /**
     * Check if the user can assign partner tasks (couple subscription).
     */
    public function canAssignPartnerTasks(): bool
    {
        return $this->can('can_assign_partner_tasks');
    }

    /**
     * Check if the user has premium support.
     */
    public function hasPremiumSupport(): bool
    {
        return $this->can('has_premium_support');
    }

    /**
     * Scope to get active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('stripe_status', 'active');
    }

    /**
     * Scope to get trial subscriptions.
     */
    public function scopeOnTrial($query)
    {
        return $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope to get cancelled subscriptions.
     */
    public function scopeCancelled($query)
    {
        return $query->whereIn('stripe_status', ['canceled', 'cancelled']);
    }

    /**
     * Scope to get subscriptions by plan.
     */
    public function scopeByPlan($query, SubscriptionPlan $plan)
    {
        return $query->where('stripe_price', $plan->stripePriceId());
    }
}