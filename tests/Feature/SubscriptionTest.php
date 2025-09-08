<?php

use App\Actions\Subscriptions\CancelSubscriptionAction;
use App\Actions\Subscriptions\CreateSubscriptionAction;
use App\Actions\Subscriptions\UpdateSubscriptionAction;
use App\Enums\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription as CashierSubscription;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can create a monthly subscription', function () {
    $result = CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);

    expect($result['success'])->toBeTrue();
    expect($result['subscription'])->not->toBeNull();
    expect($this->user->hasActiveSubscription())->toBeTrue();
    expect($this->user->getCurrentPlan())->toBe(SubscriptionPlan::MONTHLY);
});

it('can create a couple subscription', function () {
    $result = CreateSubscriptionAction::run($this->user, SubscriptionPlan::COUPLE);

    expect($result['success'])->toBeTrue();
    expect($result['subscription'])->not->toBeNull();
    expect($this->user->hasActiveSubscription())->toBeTrue();
    expect($this->user->getCurrentPlan())->toBe(SubscriptionPlan::COUPLE);
});

it('can create a lifetime subscription', function () {
    // Mock Stripe payment method
    $this->user->createAsStripeCustomer();
    
    $result = CreateSubscriptionAction::run($this->user, SubscriptionPlan::LIFETIME, 'pm_test_123');

    expect($result['success'])->toBeTrue();
    expect($result['subscription'])->not->toBeNull();
    expect($this->user->hasActiveSubscription())->toBeTrue();
    expect($this->user->getCurrentPlan())->toBe(SubscriptionPlan::LIFETIME);
});

it('prevents creating multiple active subscriptions', function () {
    // Create first subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    // Try to create second subscription
    $result = CreateSubscriptionAction::run($this->user, SubscriptionPlan::COUPLE);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('already has an active subscription');
});

it('can update subscription from monthly to couple', function () {
    // Create monthly subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    // Update to couple subscription
    $result = UpdateSubscriptionAction::run($this->user, SubscriptionPlan::COUPLE);

    expect($result['success'])->toBeTrue();
    expect($this->user->getCurrentPlan())->toBe(SubscriptionPlan::COUPLE);
});

it('can cancel subscription', function () {
    // Create subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    // Cancel subscription
    $result = CancelSubscriptionAction::run($this->user, false);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('will be cancelled');
});

it('can cancel subscription immediately', function () {
    // Create subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    // Cancel subscription immediately
    $result = CancelSubscriptionAction::run($this->user, true);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('cancelled immediately');
});

it('prevents cancelling lifetime subscription', function () {
    // Create lifetime subscription
    $this->user->createAsStripeCustomer();
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::LIFETIME, 'pm_test_123');
    
    // Try to cancel lifetime subscription
    $result = CancelSubscriptionAction::run($this->user, false);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('cannot be cancelled');
});

it('can check subscription limits', function () {
    // Free plan limits
    expect($this->user->canCreateContent())->toBeFalse();
    expect($this->user->canAccessPremiumContent())->toBeFalse();
    expect($this->user->getMaxActiveOutcomesForSubscription())->toBe(1);

    // Create monthly subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    // Monthly plan limits
    expect($this->user->canCreateContent())->toBeTrue();
    expect($this->user->canAccessPremiumContent())->toBeTrue();
    expect($this->user->getMaxActiveOutcomesForSubscription())->toBe(5);
});

it('can check couple subscription features', function () {
    // Create couple subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::COUPLE);
    
    // Couple plan features
    expect($this->user->canCreateContent())->toBeTrue();
    expect($this->user->canAccessPremiumContent())->toBeTrue();
    expect($this->user->canAssignPartnerTasks())->toBeTrue();
    expect($this->user->getMaxActiveOutcomesForSubscription())->toBe(10);
});

it('can check lifetime subscription features', function () {
    // Create lifetime subscription
    $this->user->createAsStripeCustomer();
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::LIFETIME, 'pm_test_123');
    
    // Lifetime plan features
    expect($this->user->canCreateContent())->toBeTrue();
    expect($this->user->canAccessPremiumContent())->toBeTrue();
    expect($this->user->canAssignPartnerTasks())->toBeTrue();
    expect($this->user->hasPremiumSupport())->toBeTrue();
});

it('can get subscription status', function () {
    // Free plan
    expect($this->user->subscription_status)->toBe('Free');
    expect($this->user->onTrial())->toBeFalse();

    // Create trial subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    expect($this->user->subscription_status)->toBe('Trial');
    expect($this->user->onTrial())->toBeTrue();
});

it('can get subscription expiry date', function () {
    // No subscription
    expect($this->user->subscription_expiry)->toBeNull();

    // Create subscription
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    expect($this->user->subscription_expiry)->not->toBeNull();
});

it('can check daily task limits', function () {
    // Free plan - limited tasks
    expect($this->user->getMaxTasksPerDayForSubscription())->toBe(3);
    expect($this->user->hasReachedDailyTaskLimit())->toBeFalse();

    // Create monthly subscription - unlimited tasks
    CreateSubscriptionAction::run($this->user, SubscriptionPlan::MONTHLY);
    
    expect($this->user->getMaxTasksPerDayForSubscription())->toBeNull();
    expect($this->user->hasReachedDailyTaskLimit())->toBeFalse();
});

it('can get subscription plan features', function () {
    $monthlyPlan = SubscriptionPlan::MONTHLY;
    
    expect($monthlyPlan->label())->toBe('Monthly');
    expect($monthlyPlan->description())->toContain('Full access');
    expect($monthlyPlan->price())->toBe(299);
    expect($monthlyPlan->formattedPrice())->toBe('£2.99/month');
    expect($monthlyPlan->isRecurring())->toBeTrue();
    expect($monthlyPlan->trialDays())->toBe(14);
    expect($monthlyPlan->features())->toContain('Unlimited task assignments');
});

it('can get lifetime plan details', function () {
    $lifetimePlan = SubscriptionPlan::LIFETIME;
    
    expect($lifetimePlan->label())->toBe('Lifetime');
    expect($lifetimePlan->description())->toContain('One-time payment');
    expect($lifetimePlan->price())->toBe(9900);
    expect($lifetimePlan->formattedPrice())->toBe('£99.00 (one-time)');
    expect($lifetimePlan->isRecurring())->toBeFalse();
    expect($lifetimePlan->trialDays())->toBe(0);
    expect($lifetimePlan->features())->toContain('No recurring payments');
});