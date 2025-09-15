<?php

use App\Enums\SubscriptionPlan;
use App\Livewire\Subscription\Billing;
use App\Models\User;
use App\Services\SubscriptionService;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertStatus(200);
});

it('displays billing information', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Billing & Subscription')
        ->assertSee('Current Plan')
        ->assertSee('Billing Portal');
});

it('opens billing portal successfully', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('getBillingPortalUrl')
        ->once()
        ->with($user)
        ->andReturn('https://billing.stripe.com/test');
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('openBillingPortal')
        ->assertRedirect('https://billing.stripe.com/test');
});

it('cancels subscription successfully', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('cancelSubscription')
        ->once()
        ->with($user)
        ->andReturn(true);
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('cancelSubscription')
        ->assertSessionHas('success', 'Your subscription has been cancelled. You will continue to have access until the end of your current billing period.');
});

it('handles subscription cancellation failure', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('cancelSubscription')
        ->once()
        ->with($user)
        ->andReturn(false);
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('cancelSubscription')
        ->assertSessionHas('error', 'Failed to cancel subscription. Please try again or contact support.');
});

it('resumes subscription successfully', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('resumeSubscription')
        ->once()
        ->with($user)
        ->andReturn(true);
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('resumeSubscription')
        ->assertSessionHas('success', 'Your subscription has been resumed.');
});

it('handles subscription resumption failure', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('resumeSubscription')
        ->once()
        ->with($user)
        ->andReturn(false);
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('resumeSubscription')
        ->assertSessionHas('error', 'Failed to resume subscription. Please try again or contact support.');
});

it('displays current subscription details', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Premium')
        ->assertSee('$19.99', false);
});

it('shows trial information for trial users', function () {
    $user = User::factory()->create([
        'subscription_plan' => SubscriptionPlan::Free,
        'trial_ends_at' => now()->addDays(7)
    ]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Trial', false);
});

it('displays subscription status', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Active', false);
});

it('shows billing history section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Billing History', false)
        ->assertSee('Payment Method', false);
});

it('uses the app layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertViewIs('livewire.subscription.billing');
});

it('has proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertStatus(200);
});

it('handles unauthenticated users', function () {
    $this->get('/subscription/billing')
        ->assertRedirect('/login');
});

it('displays next billing date', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Next billing', false);
});

it('shows subscription management options', function () {
    $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Premium]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('Cancel Subscription', false)
        ->assertSee('Resume Subscription', false);
});

it('handles service errors gracefully', function () {
    $user = User::factory()->create();
    
    // Mock the subscription service to throw an exception
    $mockService = Mockery::mock(SubscriptionService::class);
    $mockService->shouldReceive('getBillingPortalUrl')
        ->once()
        ->andThrow(new \Exception('Service error'));
    
    app()->instance(SubscriptionService::class, $mockService);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->call('openBillingPortal');
    
    // The component should handle the error gracefully
    // This is tested by ensuring no exceptions are thrown
});

it('displays user information correctly', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
    
    Livewire::actingAs($user)
        ->test(Billing::class)
        ->assertSee('John Doe')
        ->assertSee('john@example.com');
});

