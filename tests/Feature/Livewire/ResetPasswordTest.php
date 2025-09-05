<?php

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('renders successfully with token and email', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertStatus(200);
});

it('displays the reset password form elements', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('Reset your password')
        ->assertSee('Email address')
        ->assertSee('New password')
        ->assertSee('Confirm new password')
        ->assertSee('Reset Password')
        ->assertSee('Back to sign in');
});

it('has proper form validation', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->set('form.password', '')
        ->set('form.password_confirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['form.password', 'form.password_confirmation']);
});

it('validates password requirements', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->set('form.password', '123')
        ->set('form.password_confirmation', '123')
        ->call('resetPassword')
        ->assertHasErrors(['form.password']);
});

it('validates password confirmation', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different123')
        ->call('resetPassword')
        ->assertHasErrors(['form.password_confirmation']);
});

it('resets password successfully with valid data', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => 'test@example.com'])
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertSet('passwordReset', true)
        ->assertSee('Password reset successful!')
        ->assertSee('Sign in to your account');

    // Verify password was actually changed
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('shows error for invalid token', function () {
    $token = 'invalid-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['form.email']);
});

it('displays loading state during password reset', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => 'test@example.com'])
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertSee('Resetting...', false)
        ->assertSee('Password reset successful!');
});

it('has proper navigation links', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('wire:navigate', false)
        ->assertSee('href="/login"', false);
});

it('has proper red color scheme', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('text-red-600', false)
        ->assertSee('bg-red-600', false)
        ->assertSee('focus:ring-red-500', false);
});

it('uses the guest layout', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertViewIs('livewire.reset-password');
});

it('has proper form accessibility', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('for="email"', false)
        ->assertSee('for="password"', false)
        ->assertSee('for="password_confirmation"', false)
        ->assertSee('autocomplete="new-password"', false)
        ->assertSee('required', false);
});

it('shows success message after password reset', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => 'test@example.com'])
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertSee('Your password has been successfully reset')
        ->assertSee('You can now sign in with your new password');
});

it('has proper button cursor states', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('cursor-pointer', false)
        ->assertSee('disabled:cursor-not-allowed', false);
});

it('sets email field as read-only', function () {
    $token = 'test-token';
    $email = 'test@example.com';

    Livewire::test(ResetPassword::class, ['token' => $token, 'email' => $email])
        ->assertSee('readonly', false)
        ->assertSee('cursor-not-allowed', false);
});
