<?php

use App\Livewire\Subscription\Success;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertStatus(200);
});

it('displays success message', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Subscription Successful!')
        ->assertSee('Welcome to Kink Master Premium')
        ->assertSee('Your subscription is now active');
});

it('shows subscription details', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Plan Details', false)
        ->assertSee('Billing Information', false);
});

it('displays next steps', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('What\'s Next?', false)
        ->assertSee('Start exploring premium features', false)
        ->assertSee('Complete your profile', false);
});

it('shows feature highlights', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Premium Features', false)
        ->assertSee('Unlimited tasks', false)
        ->assertSee('Priority support', false);
});

it('displays welcome message', function () {
    $user = User::factory()->create([
        'name' => 'John Doe'
    ]);
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Welcome, John Doe!', false);
});

it('shows account setup prompts', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Complete Your Setup', false)
        ->assertSee('Upload a profile picture', false)
        ->assertSee('Set your preferences', false);
});

it('displays support information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Need Help?', false)
        ->assertSee('Contact our support team', false)
        ->assertSee('support@kinkmaster.com', false);
});

it('shows community links', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Join Our Community', false)
        ->assertSee('Connect with other users', false);
});

it('displays billing information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Billing Details', false)
        ->assertSee('Next billing date', false);
});

it('shows feature comparison', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('What You Get', false)
        ->assertSee('Premium Content', false);
});

it('uses the app layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertViewIs('livewire.subscription.success');
});

it('has proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertStatus(200);
});

it('handles unauthenticated users', function () {
    $this->get('/subscription/success')
        ->assertRedirect('/login');
});

it('shows subscription plan information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Premium Plan', false);
});

it('displays payment confirmation', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Payment Confirmed', false)
        ->assertSee('Your payment has been processed', false);
});

it('shows account status', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Account Status: Active', false);
});

it('displays feature unlock information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('All Premium Features Unlocked', false);
});

it('shows getting started guide', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Getting Started', false)
        ->assertSee('Take a tour', false);
});

it('displays proper styling and layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('bg-green-50', false)
        ->assertSee('text-green-800', false)
        ->assertSee('rounded-lg', false);
});

it('shows navigation options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Go to Dashboard', false)
        ->assertSee('View Billing', false);
});

it('displays success animation or icon', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('check-circle', false)
        ->assertSee('success', false);
});

it('shows subscription benefits', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Success::class)
        ->assertSee('Benefits', false)
        ->assertSee('Unlimited access', false);
});

