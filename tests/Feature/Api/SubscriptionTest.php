<?php

declare(strict_types=1);

use App\Models\User;
use App\Enums\SubscriptionPlan;

it('can get subscription plans', function () {
    $response = $this->getJson('/api/v1/subscription/plans');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'plans' => [
                '*' => [
                    'value',
                    'label',
                    'description',
                    'price',
                    'price_formatted',
                    'is_recurring',
                    'interval',
                    'features',
                    'max_tasks_per_day',
                    'can_create_stories',
                    'can_upload_images',
                    'can_access_premium_content',
                    'can_create_custom_tasks',
                    'is_couple_plan',
                    'is_lifetime',
                    'is_paid',
                ],
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('plans'))->toHaveCount(5); // Free, Solo, Premium, Couple, Lifetime
});

it('can get user subscription', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Premium,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/subscription/current');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'subscription' => [
                'current_plan' => [
                    'value',
                    'label',
                    'description',
                ],
                'status',
                'has_active_subscription',
                'has_paid_subscription',
                'is_on_trial',
                'needs_subscription_choice',
                'permissions',
                'limits',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('subscription.current_plan.value'))->toBe(SubscriptionPlan::Premium->value);
    expect($response->json('subscription.current_plan.label'))->toBe('Premium');
});

it('can create checkout session', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/subscription/checkout', [
            'plan' => SubscriptionPlan::Premium->value,
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'checkout_url',
            'plan' => [
                'value',
                'label',
                'price_formatted',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('checkout_url'))->toContain('checkout.stripe.com');
    expect($response->json('plan.value'))->toBe(SubscriptionPlan::Premium->value);
});

it('cannot create checkout session for invalid plan', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/subscription/checkout', [
            'plan' => 999, // Invalid plan
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['plan'],
        ]);
});

it('validates checkout session data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/subscription/checkout', []);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['plan'],
        ]);
});

it('shows user permissions based on subscription', function () {
    $freeUser = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $premiumUser = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Premium,
    ]);

    // Test free user permissions
    $response = $this->actingAs($freeUser, 'sanctum')
        ->getJson('/api/v1/subscription/current');

    $response->assertSuccessful();
    expect($response->json('subscription.permissions.can_create_stories'))->toBeFalse();
    expect($response->json('subscription.permissions.can_upload_images'))->toBeFalse();
    expect($response->json('subscription.permissions.can_access_premium_content'))->toBeFalse();

    // Test premium user permissions
    $response = $this->actingAs($premiumUser, 'sanctum')
        ->getJson('/api/v1/subscription/current');

    $response->assertSuccessful();
    expect($response->json('subscription.permissions.can_create_stories'))->toBeTrue();
    expect($response->json('subscription.permissions.can_upload_images'))->toBeTrue();
    expect($response->json('subscription.permissions.can_access_premium_content'))->toBeTrue();
});

it('shows user limits based on subscription', function () {
    $freeUser = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    $premiumUser = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Premium,
    ]);

    // Test free user limits
    $response = $this->actingAs($freeUser, 'sanctum')
        ->getJson('/api/v1/subscription/current');

    $response->assertSuccessful();
    expect($response->json('subscription.limits.max_tasks_per_day'))->toBe(1);

    // Test premium user limits
    $response = $this->actingAs($premiumUser, 'sanctum')
        ->getJson('/api/v1/subscription/current');

    $response->assertSuccessful();
    expect($response->json('subscription.limits.max_tasks_per_day'))->toBeNull(); // Unlimited
});