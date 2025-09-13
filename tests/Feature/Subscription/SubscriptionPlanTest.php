<?php

declare(strict_types=1);

use App\Enums\SubscriptionPlan;
use App\Models\User;

describe('SubscriptionPlan Enum', function () {
    it('has correct plan values', function () {
        expect(SubscriptionPlan::Free->value)->toBe(0);
        expect(SubscriptionPlan::Solo->value)->toBe(1);
        expect(SubscriptionPlan::Premium->value)->toBe(2);
        expect(SubscriptionPlan::Couple->value)->toBe(3);
        expect(SubscriptionPlan::Lifetime->value)->toBe(4);
    });

    it('has correct labels', function () {
        expect(SubscriptionPlan::Free->label())->toBe('Free');
        expect(SubscriptionPlan::Solo->label())->toBe('Solo');
        expect(SubscriptionPlan::Premium->label())->toBe('Premium');
        expect(SubscriptionPlan::Couple->label())->toBe('Couple');
        expect(SubscriptionPlan::Lifetime->label())->toBe('Lifetime');
    });

    it('has correct prices', function () {
        expect(SubscriptionPlan::Free->price())->toBe(0);
        expect(SubscriptionPlan::Solo->price())->toBe(199); // £1.99 in pence
        expect(SubscriptionPlan::Premium->price())->toBe(299); // £2.99 in pence
        expect(SubscriptionPlan::Couple->price())->toBe(399); // £3.99 in pence
        expect(SubscriptionPlan::Lifetime->price())->toBe(9999); // £99.99 in pence
    });

    it('has correct formatted prices', function () {
        expect(SubscriptionPlan::Free->priceFormatted())->toBe('£0.00');
        expect(SubscriptionPlan::Solo->priceFormatted())->toBe('£1.99');
        expect(SubscriptionPlan::Premium->priceFormatted())->toBe('£2.99');
        expect(SubscriptionPlan::Couple->priceFormatted())->toBe('£3.99');
        expect(SubscriptionPlan::Lifetime->priceFormatted())->toBe('£99.99');
    });

    it('correctly identifies recurring plans', function () {
        expect(SubscriptionPlan::Free->isRecurring())->toBeFalse();
        expect(SubscriptionPlan::Solo->isRecurring())->toBeTrue();
        expect(SubscriptionPlan::Premium->isRecurring())->toBeTrue();
        expect(SubscriptionPlan::Couple->isRecurring())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->isRecurring())->toBeFalse();
    });

    it('has correct intervals', function () {
        expect(SubscriptionPlan::Free->interval())->toBeNull();
        expect(SubscriptionPlan::Solo->interval())->toBe('month');
        expect(SubscriptionPlan::Premium->interval())->toBe('month');
        expect(SubscriptionPlan::Couple->interval())->toBe('month');
        expect(SubscriptionPlan::Lifetime->interval())->toBeNull();
    });

    it('has correct feature permissions', function () {
        // Free plan
        expect(SubscriptionPlan::Free->canCreateStories())->toBeFalse();
        expect(SubscriptionPlan::Free->canUploadImages())->toBeFalse();
        expect(SubscriptionPlan::Free->canAccessPremiumContent())->toBeFalse();
        expect(SubscriptionPlan::Free->canCreateCustomTasks())->toBeFalse();
        expect(SubscriptionPlan::Free->maxTasksPerDay())->toBe(1);

        // Solo plan
        expect(SubscriptionPlan::Solo->canCreateStories())->toBeTrue();
        expect(SubscriptionPlan::Solo->canUploadImages())->toBeFalse();
        expect(SubscriptionPlan::Solo->canAccessPremiumContent())->toBeFalse();
        expect(SubscriptionPlan::Solo->canCreateCustomTasks())->toBeFalse();
        expect(SubscriptionPlan::Solo->maxTasksPerDay())->toBeNull();

        // Premium plan
        expect(SubscriptionPlan::Premium->canCreateStories())->toBeTrue();
        expect(SubscriptionPlan::Premium->canUploadImages())->toBeTrue();
        expect(SubscriptionPlan::Premium->canAccessPremiumContent())->toBeTrue();
        expect(SubscriptionPlan::Premium->canCreateCustomTasks())->toBeTrue();
        expect(SubscriptionPlan::Premium->maxTasksPerDay())->toBeNull();

        // Couple plan
        expect(SubscriptionPlan::Couple->canCreateStories())->toBeTrue();
        expect(SubscriptionPlan::Couple->canUploadImages())->toBeTrue();
        expect(SubscriptionPlan::Couple->canAccessPremiumContent())->toBeTrue();
        expect(SubscriptionPlan::Couple->canCreateCustomTasks())->toBeTrue();
        expect(SubscriptionPlan::Couple->maxTasksPerDay())->toBeNull();
        expect(SubscriptionPlan::Couple->isCouplePlan())->toBeTrue();

        // Lifetime plan
        expect(SubscriptionPlan::Lifetime->canCreateStories())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->canUploadImages())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->canAccessPremiumContent())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->canCreateCustomTasks())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->maxTasksPerDay())->toBeNull();
        expect(SubscriptionPlan::Lifetime->isLifetime())->toBeTrue();
    });

    it('correctly identifies paid plans', function () {
        expect(SubscriptionPlan::Free->isPaid())->toBeFalse();
        expect(SubscriptionPlan::Solo->isPaid())->toBeTrue();
        expect(SubscriptionPlan::Premium->isPaid())->toBeTrue();
        expect(SubscriptionPlan::Couple->isPaid())->toBeTrue();
        expect(SubscriptionPlan::Lifetime->isPaid())->toBeTrue();
    });
});

describe('User Subscription Methods', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('correctly identifies trial status', function () {
        // User not on trial
        expect($this->user->isOnTrial())->toBeFalse();

        // User on generic trial
        $this->user->update(['trial_ends_at' => now()->addDays(7)]);
        expect($this->user->isOnTrial())->toBeTrue();

        // User with expired trial
        $this->user->update(['trial_ends_at' => now()->subDays(1)]);
        expect($this->user->isOnTrial())->toBeFalse();
    });

    it('correctly identifies active subscription', function () {
        // User without subscription
        expect($this->user->hasActiveSubscription())->toBeFalse();

        // User on trial
        $this->user->update(['trial_ends_at' => now()->addDays(7)]);
        expect($this->user->hasActiveSubscription())->toBeTrue();

        // User with paid subscription
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->hasActiveSubscription())->toBeTrue();

        // User with lifetime subscription
        $this->user->update(['subscription_plan' => SubscriptionPlan::Lifetime]);
        expect($this->user->hasActiveSubscription())->toBeTrue();
    });

    it('correctly identifies paid subscription', function () {
        // User without paid subscription
        expect($this->user->hasPaidSubscription())->toBeFalse();

        // User on trial (not paid)
        $this->user->update(['trial_ends_at' => now()->addDays(7)]);
        expect($this->user->hasPaidSubscription())->toBeFalse();

        // User with paid subscription
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->hasPaidSubscription())->toBeTrue();

        // User with lifetime subscription
        $this->user->update(['subscription_plan' => SubscriptionPlan::Lifetime]);
        expect($this->user->hasPaidSubscription())->toBeTrue();
    });

    it('correctly identifies premium subscription', function () {
        // Free user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Free]);
        expect($this->user->hasActivePremiumSubscription())->toBeFalse();

        // Solo user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Solo]);
        expect($this->user->hasActivePremiumSubscription())->toBeFalse();

        // Premium user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->hasActivePremiumSubscription())->toBeTrue();

        // Couple user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Couple]);
        expect($this->user->hasActivePremiumSubscription())->toBeTrue();

        // Lifetime user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Lifetime]);
        expect($this->user->hasActivePremiumSubscription())->toBeTrue();
    });

    it('correctly identifies lifetime subscription', function () {
        // Non-lifetime user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->hasLifetimeSubscription())->toBeFalse();

        // Lifetime user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Lifetime]);
        expect($this->user->hasLifetimeSubscription())->toBeTrue();
    });

    it('correctly checks feature permissions', function () {
        // Free user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Free]);
        expect($this->user->canCreateStories())->toBeFalse();
        expect($this->user->canUploadCompletionImages())->toBeFalse();
        expect($this->user->canAccessPremiumContent())->toBeFalse();
        expect($this->user->canCreateCustomTasks())->toBeFalse();

        // Solo user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Solo]);
        expect($this->user->canCreateStories())->toBeTrue();
        expect($this->user->canUploadCompletionImages())->toBeFalse();
        expect($this->user->canAccessPremiumContent())->toBeFalse();
        expect($this->user->canCreateCustomTasks())->toBeFalse();

        // Premium user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->canCreateStories())->toBeTrue();
        expect($this->user->canUploadCompletionImages())->toBeTrue();
        expect($this->user->canAccessPremiumContent())->toBeTrue();
        expect($this->user->canCreateCustomTasks())->toBeTrue();
    });

    it('correctly checks daily task limits', function () {
        // Free user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Free]);
        expect($this->user->getMaxTasksPerDay())->toBe(1);
        expect($this->user->hasReachedDailyTaskLimit())->toBeFalse();

        // Premium user
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->getMaxTasksPerDay())->toBeNull();
        expect($this->user->hasReachedDailyTaskLimit())->toBeFalse();
    });

    it('correctly identifies subscription choice requirement', function () {
        // User who hasn't used trial
        expect($this->user->needsSubscriptionChoice())->toBeFalse();

        // User who used trial but is still on trial
        $this->user->update([
            'trial_ends_at' => now()->addDays(7),
            'has_used_trial' => true,
        ]);
        expect($this->user->needsSubscriptionChoice())->toBeFalse();

        // User who used trial and it expired
        $this->user->update([
            'trial_ends_at' => now()->subDays(1),
            'has_used_trial' => true,
        ]);
        expect($this->user->needsSubscriptionChoice())->toBeTrue();

        // User with paid subscription
        $this->user->update(['subscription_plan' => SubscriptionPlan::Premium]);
        expect($this->user->needsSubscriptionChoice())->toBeFalse();
    });

    it('starts trial correctly', function () {
        expect($this->user->has_used_trial)->toBeFalse();
        expect($this->user->trial_ends_at)->toBeNull();

        $this->user->startTrial();

        expect($this->user->fresh()->has_used_trial)->toBeTrue();
        expect($this->user->fresh()->trial_ends_at)->not->toBeNull();
        expect($this->user->fresh()->trial_ends_at->isFuture())->toBeTrue();
    });

    it('does not start trial if already used', function () {
        $this->user->update([
            'has_used_trial' => true,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $originalTrialEnd = $this->user->trial_ends_at;

        $this->user->startTrial();

        expect($this->user->fresh()->trial_ends_at->toDateString())
            ->toBe($originalTrialEnd->toDateString());
    });
});
