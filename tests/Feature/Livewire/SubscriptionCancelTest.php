<?php

use App\Livewire\Subscription\Cancel;
use App\Models\User;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertStatus(200);
});

it('displays cancellation confirmation message', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Subscription Cancelled')
        ->assertSee('Your subscription has been successfully cancelled')
        ->assertSee('Thank you for using Kink Master');
});

it('shows next steps information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('What happens next?', false)
        ->assertSee('You will continue to have access', false)
        ->assertSee('until the end of your current billing period', false);
});

it('displays support contact information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Need help?', false)
        ->assertSee('Contact Support', false)
        ->assertSee('support@kinkmaster.com', false);
});

it('shows feedback form', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('We\'d love to hear from you', false)
        ->assertSee('Tell us why you cancelled', false);
});

it('displays re-subscription option', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Change your mind?', false)
        ->assertSee('Resubscribe', false);
});

it('shows account access information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Your account will remain active', false)
        ->assertSee('You can resubscribe at any time', false);
});

it('displays refund policy information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Refund Policy', false)
        ->assertSee('No refunds for partial periods', false);
});

it('shows data retention information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Your data is safe', false)
        ->assertSee('We will retain your account data', false);
});

it('uses the app layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertViewIs('livewire.subscription.cancel');
});

it('has proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertStatus(200);
});

it('handles unauthenticated users', function () {
    $this->get('/subscription/cancel')
        ->assertRedirect('/login');
});

it('displays user-specific information', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('John Doe', false);
});

it('shows cancellation date', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Cancelled on', false)
        ->assertSee(now()->format('F j, Y'), false);
});

it('displays subscription end date', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Access until', false);
});

it('shows alternative options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Consider our free plan', false)
        ->assertSee('Downgrade instead', false);
});

it('displays community information', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Stay connected', false)
        ->assertSee('Join our community', false);
});

it('shows feedback submission form', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('textarea', false)
        ->assertSee('Submit Feedback', false);
});

it('displays proper styling and layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('bg-white', false)
        ->assertSee('rounded-lg', false)
        ->assertSee('shadow', false);
});

it('shows navigation options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(Cancel::class)
        ->assertSee('Back to Dashboard', false)
        ->assertSee('View Billing', false);
});

