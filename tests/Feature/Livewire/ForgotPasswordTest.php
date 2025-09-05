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
        ->assertSee('Send Reset Link')
        ->assertSee('Back to sign in');
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

it('sends reset link successfully', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSet('emailSent', true)
        ->assertSee('Reset link sent!')
        ->assertSee('test@example.com');
});

it('shows error for non-existent email', function () {
    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'nonexistent@example.com')
        ->call('sendResetLink')
        ->assertHasErrors(['form.email']);
});

it('displays loading state during reset link sending', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSee('Sending...', false);
});

it('has proper navigation links', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('wire:navigate', false)
        ->assertSee('href="/login"', false);
});

it('has proper red color scheme', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('text-red-600', false)
        ->assertSee('bg-red-600', false)
        ->assertSee('focus:ring-red-500', false);
});

it('uses the guest layout', function () {
    Livewire::test(ForgotPassword::class)
        ->assertViewIs('livewire.forgot-password');
});

it('has proper form accessibility', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('for="email"', false)
        ->assertSee('autocomplete="email"', false)
        ->assertSee('required', false);
});

it('shows success message after email sent', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(ForgotPassword::class)
        ->set('form.email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSee('We\'ve sent a password reset link to')
        ->assertSee('Please check your email and click the link to reset your password');
});

it('has proper button cursor states', function () {
    Livewire::test(ForgotPassword::class)
        ->assertSee('cursor-pointer', false)
        ->assertSee('disabled:cursor-not-allowed', false);
});
