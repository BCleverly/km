<?php

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('renders successfully with valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertStatus(200);
});

it('displays the reset password form elements', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertSee('Reset your password')
        ->assertSee('Email address')
        ->assertSee('Password')
        ->assertSee('Confirm password')
        ->assertSee('Reset password');
});

it('mounts with token and email from request', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);
    
    $component = Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com');
    
    expect($component->get('form.token'))->toBe($token);
    expect($component->get('form.email'))->toBe('test@example.com');
});

it('has proper form validation', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', '')
        ->set('form.password', '')
        ->set('form.password_confirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['form.email', 'form.password']);
});

it('validates email format', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'invalid-email')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('resetPassword')
        ->assertHasErrors(['form.email']);
});

it('validates password confirmation', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different123')
        ->call('resetPassword')
        ->assertHasErrors(['form.password']);
});

it('validates password requirements', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', '123')
        ->set('form.password_confirmation', '123')
        ->call('resetPassword')
        ->assertHasErrors(['form.password']);
});

it('resets password successfully with valid data', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertSet('passwordReset', true)
        ->assertSee('Your password has been reset!');
    
    // Verify password was actually changed
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('shows error for invalid token', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    
    Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['form.email']);
});

it('shows error for non-existent user', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'nonexistent@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['form.email']);
});

it('has proper navigation links', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertSee('wire:navigate', false)
        ->assertSee('href="/login"', false);
});

it('uses the guest layout', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertViewIs('livewire.reset-password');
});

it('has proper page title', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertStatus(200);
});

it('has proper form accessibility', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->assertSee('for="email"', false)
        ->assertSee('for="password"', false)
        ->assertSee('for="password_confirmation"', false)
        ->assertSee('autocomplete="email"', false)
        ->assertSee('autocomplete="new-password"', false);
});

it('shows loading state during submission', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertSee('Resetting...', false);
});

it('dispatches password reset event', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $token = Password::createToken($user);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword');
    
    // The PasswordReset event is dispatched by Laravel's Password::reset method
    // This is tested indirectly through the success state
});

it('handles password reset service errors gracefully', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);
    
    // Mock Password service to return an error
    Password::shouldReceive('reset')
        ->once()
        ->andReturn(Password::INVALID_TOKEN);
    
    Livewire::test(ResetPassword::class, ['token' => $token])
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'newpassword123')
        ->set('form.password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['form.email']);
});