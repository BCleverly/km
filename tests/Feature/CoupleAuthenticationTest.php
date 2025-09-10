<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows user with partner to access platform when partner has active subscription', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Give user1 an active subscription
    $user1->newSubscription('premium', 'price_premium')->create();
    
    // User2 should have access through user1's subscription
    expect($user2->hasActiveSubscription())->toBeTrue();
    expect($user2->hasActivePremiumSubscription())->toBeTrue();
    expect($user2->subscription_status)->toBe('Couple Premium');
});

it('allows user with partner to access platform when user has active subscription', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Give user2 an active subscription
    $user2->newSubscription('premium', 'price_premium')->create();
    
    // User1 should have access through user2's subscription
    expect($user1->hasActiveSubscription())->toBeTrue();
    expect($user1->hasActivePremiumSubscription())->toBeTrue();
    expect($user1->subscription_status)->toBe('Couple Premium');
});

it('denies access when neither partner has active subscription', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Neither user has a subscription
    expect($user1->hasActiveSubscription())->toBeFalse();
    expect($user2->hasActiveSubscription())->toBeFalse();
    expect($user1->subscription_status)->toBe('Free');
    expect($user2->subscription_status)->toBe('Free');
});

it('correctly identifies couple relationships', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Both users should be part of a couple
    expect($user1->isPartOfCouple())->toBeTrue();
    expect($user2->isPartOfCouple())->toBeTrue();
    
    // They should be able to get each other as partners
    expect($user1->getCouplePartner()->id)->toBe($user2->id);
    expect($user2->getCouplePartner()->id)->toBe($user1->id);
});

it('handles lifetime subscriptions for couples', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Give user1 a lifetime subscription
    $user1->newSubscription('lifetime', 'price_lifetime')->create();
    
    // User2 should have access through user1's lifetime subscription
    expect($user2->hasActiveSubscription())->toBeTrue();
    expect($user2->hasLifetimeSubscription())->toBeTrue();
    expect($user2->subscription_status)->toBe('Couple Lifetime');
});

it('redirects to subscription management when couple subscription expires', function () {
    // Create two users and link them as partners
    $user1 = User::factory()->create();
    $user2 = User::factory()->create(['partner_id' => $user1->id]);
    
    // Neither user has a subscription
    $this->actingAs($user2);
    
    // Try to access a protected route
    $response = $this->get('/app/dashboard');
    
    // Should redirect to subscription management
    $response->assertRedirect('/subscription/manage');
    $response->assertSessionHas('error', 'Your couple subscription has expired. Please renew to continue accessing the platform.');
});

it('allows access when user has individual subscription', function () {
    // Create a user with their own subscription
    $user = User::factory()->create();
    $user->newSubscription('premium', 'price_premium')->create();
    
    $this->actingAs($user);
    
    // Should be able to access protected routes
    $response = $this->get('/app/dashboard');
    $response->assertStatus(200);
    
    expect($user->hasActiveSubscription())->toBeTrue();
    expect($user->subscription_status)->toBe('Premium');
});

it('allows admin access regardless of subscription status', function () {
    // Create an admin user without subscription
    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    
    $this->actingAs($admin);
    
    // Should be able to access protected routes
    $response = $this->get('/app/dashboard');
    $response->assertStatus(200);
    
    expect($admin->subscription_status)->toBe('Admin');
});