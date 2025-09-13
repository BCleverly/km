<?php

use App\Enums\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the SubscriptionService to avoid actual Stripe calls
    $this->mock(SubscriptionService::class, function ($mock) {
        $mock->shouldReceive('createCheckoutSession')
            ->andReturn('https://checkout.stripe.com/test-session');
    });
});

it('allows user to register with free trial plan', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'john@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', SubscriptionPlan::Free->value)
        ->call('register')
        ->assertRedirect(route('app.dashboard'));

    $user = User::where('email', 'john@example.com')->first();
    
    expect($user)->not->toBeNull();
    expect($user->subscription_plan)->toBe(SubscriptionPlan::Free);
    expect($user->trial_ends_at)->not->toBeNull();
    expect($user->has_used_trial)->toBeTrue();
});

it('allows user to register with paid plan and redirects to checkout', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'Jane')
        ->set('form.last_name', 'Smith')
        ->set('form.username', 'janesmith')
        ->set('form.email', 'jane@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', SubscriptionPlan::Solo->value)
        ->call('register')
        ->assertRedirect('https://checkout.stripe.com/test-session');

    $user = User::where('email', 'jane@example.com')->first();
    
    expect($user)->not->toBeNull();
    expect($user->subscription_plan)->toBe(SubscriptionPlan::Solo);
    expect($user->trial_ends_at)->not->toBeNull();
    expect($user->has_used_trial)->toBeTrue();
});

it('validates required subscription plan selection', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'Test')
        ->set('form.last_name', 'User')
        ->set('form.username', 'testuser')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', '')
        ->call('register')
        ->assertHasErrors(['form.subscription_plan' => 'required']);
});

it('provides all subscription plans in the plans property', function () {
    $component = Livewire::test(\App\Livewire\Auth\Register::class);
    
    $plans = $component->get('plans');
    
    expect($plans)->toHaveCount(5);
    expect($plans)->toContain(SubscriptionPlan::Free);
    expect($plans)->toContain(SubscriptionPlan::Solo);
    expect($plans)->toContain(SubscriptionPlan::Premium);
    expect($plans)->toContain(SubscriptionPlan::Couple);
    expect($plans)->toContain(SubscriptionPlan::Lifetime);
});

it('defaults to free plan selection', function () {
    $component = Livewire::test(\App\Livewire\Auth\Register::class);
    
    expect($component->get('form.subscription_plan'))->toBe(SubscriptionPlan::Free->value);
});

it('handles premium plan registration correctly', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'Premium')
        ->set('form.last_name', 'User')
        ->set('form.username', 'premiumuser')
        ->set('form.email', 'premium@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', SubscriptionPlan::Premium->value)
        ->call('register')
        ->assertRedirect('https://checkout.stripe.com/test-session');

    $user = User::where('email', 'premium@example.com')->first();
    
    expect($user->subscription_plan)->toBe(SubscriptionPlan::Premium);
});

it('handles couple plan registration correctly', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'Couple')
        ->set('form.last_name', 'User')
        ->set('form.username', 'coupleuser')
        ->set('form.email', 'couple@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', SubscriptionPlan::Couple->value)
        ->call('register')
        ->assertRedirect('https://checkout.stripe.com/test-session');

    $user = User::where('email', 'couple@example.com')->first();
    
    expect($user->subscription_plan)->toBe(SubscriptionPlan::Couple);
});

it('handles lifetime plan registration correctly', function () {
    Livewire::test(\App\Livewire\Auth\Register::class)
        ->set('form.first_name', 'Lifetime')
        ->set('form.last_name', 'User')
        ->set('form.username', 'lifetimeuser')
        ->set('form.email', 'lifetime@example.com')
        ->set('form.password', 'password123')
        ->set('form.subscription_plan', SubscriptionPlan::Lifetime->value)
        ->call('register')
        ->assertRedirect('https://checkout.stripe.com/test-session');

    $user = User::where('email', 'lifetime@example.com')->first();
    
    expect($user->subscription_plan)->toBe(SubscriptionPlan::Lifetime);
});
