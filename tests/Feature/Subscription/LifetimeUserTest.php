<?php

use App\Enums\SubscriptionPlan;
use App\Models\User;
use Livewire\Livewire;

it('hides billing link from navigation for lifetime users', function () {
    $lifetimeUser = User::factory()->withLifetimeSubscription()->create();
    
    $this->actingAs($lifetimeUser)
        ->get(route('app.dashboard'))
        ->assertDontSee('Billing');
});

it('shows lifetime member status in settings for lifetime users', function () {
    $lifetimeUser = User::factory()->withLifetimeSubscription()->create();
    
    $this->actingAs($lifetimeUser)
        ->get(route('app.settings'))
        ->assertSee('Lifetime Member')
        ->assertSee('unlimited access to all features')
        ->assertDontSee('Manage Subscription');
});

it('shows special lifetime member page when accessing billing', function () {
    $lifetimeUser = User::factory()->withLifetimeSubscription()->create();
    
    $this->actingAs($lifetimeUser)
        ->get(route('app.subscription.billing'))
        ->assertSee('Lifetime Member')
        ->assertSee('Congratulations!')
        ->assertSee('unlimited access to all features forever')
        ->assertSee('Back to Dashboard');
});

it('redirects lifetime users from choose plan page', function () {
    $lifetimeUser = User::factory()->withLifetimeSubscription()->create();
    
    Livewire::actingAs($lifetimeUser)
        ->test(\App\Livewire\Subscription\ChoosePlan::class)
        ->assertRedirect(route('app.dashboard'));
});
