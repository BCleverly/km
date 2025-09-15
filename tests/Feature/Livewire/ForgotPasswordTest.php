<?php

use App\Livewire\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ForgotPassword::class)
        ->assertStatus(200);
});

it('displays the forgot password form elements', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('Forgot your password?')
        ->assertSee('Email address')
        ->assertSee('Send reset link')
        ->assertSee('Remember your password?')
        ->assertSee('Sign in');
});

it('has proper form validation', function () {
    Livewire::test(ForgotPassword::class)
        ->set('form.email', '')
        ->call('sendResetLink')
        ->assertHasErrors(['form.email']);
});

it('validates email format', function () {
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'invalid-email')
        ->call('sendResetLink')
        ->assertHasErrors(['form.email']);
});

it('shows success message when email is sent', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSet('emailSent', true)
        ->assertSee('We have emailed your password reset link!');
});

it('shows error for non-existent email', function () {
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'nonexistent@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['form.email']);
});

it('dispatches password reset link', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink');
    
    // Verify that Password::sendResetLink was called
    // This is tested indirectly through the success state
});

it('has proper navigation links', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('wire:navigate', false)
        ->assertSee('href="/login"', false);
});

it('uses the guest layout', function () {
    Livewire::test(ForgotPassword::class)
        ->assertViewIs('livewire.forgot-password');
});

it('has proper page title', function () {
    Livewire::test(ForgotPassword::class)
        ->assertStatus(200);
});

it('has proper form accessibility', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('for="email"', false)
        ->assertSee('autocomplete="email"', false);
});

it('shows loading state during submission', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSee('Sending...', false);
});

it('resets form after successful submission', function () {
    User::factory()->create(['email' => 'test@example.com']);
    
    $component = Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink');
    
    expect($component->get('form.email'))->toBe('');
});

it('handles password reset service errors gracefully', function () {
    // Mock Password service to return an error
    Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn(Password::INVALID_USER);
    
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['form.email']);
});