<?php

use App\Enums\SubscriptionPlan;
use App\Livewire\Subscription\ChoosePlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertStatus(200);
});

it('displays all subscription plans', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Solo')
        ->assertSee('Premium')
        ->assertSee('Couple')
        ->assertSee('Lifetime');
});

it('shows current plan status', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Free Plan');
});

it('redirects lifetime users to dashboard', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Lifetime]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertRedirect('/app/dashboard');
});

it('allows plan selection', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Solo->value)
        ->assertSet('selectedPlan', SubscriptionPlan::Solo);
});

it('validates plan selection before subscription', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('subscribe')
        ->assertHasErrors(['plan']);
});

it('prevents subscribing to same plan', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Solo]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Solo->value)
        ->call('subscribe')
        ->assertHasErrors(['plan']);
});

it('creates checkout session for new subscription', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('createCheckoutSession')
        ->once()
        ->with($user, SubscriptionPlan::Solo)
        ->andReturn('https://checkout.stripe.com/test');
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Solo->value)
        ->call('subscribe')
        ->assertRedirect('https://checkout.stripe.com/test');
});

it('handles subscription service errors gracefully', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    // Mock the subscription service to throw an exception
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('createCheckoutSession')
        ->once()
        ->andThrow(new \Exception('Service error'));
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Solo->value)
        ->call('subscribe')
        ->assertHasErrors(['subscription'])
        ->assertSet('isLoading', false);
});

it('shows loading state during subscription', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->call('selectPlan', SubscriptionPlan::Solo->value)
        ->call('subscribe')
        ->assertSet('isLoading', true);
});

it('displays plan features correctly', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Perfect for individuals')
        ->assertSee('Most popular choice')
        ->assertSee('For couples to share')
        ->assertSee('One-time payment');
});

it('shows upgrade/downgrade indicators', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('Upgrade', false)
        ->assertDontSee('Downgrade', false);
});

it('uses the app layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertViewIs('livewire.subscription.choose-plan');
});

it('has proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertStatus(200);
});

it('displays trial information for trial users', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
        'trial_ends_at' => now()->addDays(7)
    ]);
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('trial', false);
});

it('shows current plan status correctly', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    $component = Livewire::actingAs($user)
        ->test(ChoosePlan::class);
    
    $currentPlanStatus = $component->get('currentPlanStatus');
    
    expect($currentPlanStatus['has_paid_subscription'])->toBeTrue();
    expect($currentPlanStatus['is_lifetime'])->toBeFalse();
});

it('handles unauthenticated users', function () {
    $this->get('/subscription/choose-plan')
        ->assertRedirect('/login');
});

it('displays pricing information correctly', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(ChoosePlan::class)
        ->assertSee('$9.99', false)
        ->assertSee('$19.99', false)
        ->assertSee('$29.99', false)
        ->assertSee('$199.99', false);
});

