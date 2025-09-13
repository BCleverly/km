<?php

use App\Enums\SubscriptionPlan;
use App\Livewire\Subscription\ChoosePlan;
use App\Models\User;
use Livewire\Livewire;

it('shows current plan status for free users', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
    ]);

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Choose Your Plan')
        ->assertSee('Unlock the full potential of Kink Master')
        ->assertDontSee('Current Plan');
});

it('shows current plan status for paid users', function () {
    $user = User::factory()->withPaidSubscription(SubscriptionPlan::Premium)->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Manage Your Subscription')
        ->assertSee('Upgrade or downgrade to a different plan below')
        ->assertSee('Current Plan');
});

it('shows upgrade options for lower tier users', function () {
    $user = User::factory()->withPaidSubscription(SubscriptionPlan::Solo)->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Upgrade to Premium')
        ->assertSee('Upgrade to Couple')
        ->assertSee('Upgrade to Lifetime');
});

it('shows downgrade options for higher tier users', function () {
    $user = User::factory()->withPaidSubscription(SubscriptionPlan::Couple)->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Downgrade to Solo')
        ->assertSee('Downgrade to Premium')
        ->assertSee('Upgrade to Lifetime');
});

it('prevents subscribing to the same plan', function () {
    $user = User::factory()->withPaidSubscription(SubscriptionPlan::Premium)->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Premium->value)
        ->call('subscribe')
        ->assertHasErrors(['plan']);
});

it('redirects lifetime users to dashboard', function () {
    $user = User::factory()->withLifetimeSubscription()->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertRedirect(route('app.dashboard'));
});

it('shows trial information for trial users', function () {
    $user = User::factory()->onTrial()->create();

    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Free Trial Active')
        ->assertSee('trial ends on');
});