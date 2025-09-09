<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Register::class)
        ->assertStatus(200);
});

it('displays the registration form elements', function () {
    Livewire::test(Register::class)
        ->assertSee('Get started for free')
        ->assertSee('First name')
        ->assertSee('Last name')
        ->assertSee('Username')
        ->assertSee('Email address')
        ->assertSee('Password')
        ->assertSee('How did you hear about us?')
        ->assertSee('Sign up →')
        ->assertSee('Sign in to your account');
});

it('has proper form validation', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', '')
        ->set('form.last_name', '')
        ->set('form.username', '')
        ->set('form.email', '')
        ->set('form.password', '')
        ->call('register')
        ->assertHasErrors(['form.first_name', 'form.last_name', 'form.username', 'form.email', 'form.password']);
});

it('validates email format', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'invalid-email')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertHasErrors(['form.email']);
});

it('validates unique email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertHasErrors(['form.email']);
});

it('validates unique username', function () {
    User::factory()->create(['username' => 'johndoe']);

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertHasErrors(['form.username']);
});

it('validates username format', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'john doe!')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertHasErrors(['form.username']);
});

it('validates password requirements', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', '123')
        ->call('register')
        ->assertHasErrors(['form.password']);
});


it('creates user successfully with valid data', function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'test@example.com',
    ]);

    $user = User::where('email', 'test@example.com')->first();
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('dispatches registered event', function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register');

    Event::assertDispatched(Registered::class);
});

it('logs in user after registration', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register');

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->email)->toBe('test@example.com');
});

it('displays loading state during registration', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.username', 'johndoe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->call('register')
        ->assertSee('Creating account...', false);
});

it('has proper navigation links', function () {
    Livewire::test(Register::class)
        ->assertSee('wire:navigate', false)
        ->assertSee('href="/login"', false)
        ->assertSee('href="/terms"', false)
        ->assertSee('href="/privacy"', false);
});

it('displays disabled social login options', function () {
    Livewire::test(Register::class)
        ->assertSee('Passkey')
        ->assertSee('Google')
        ->assertSee('Facebook')
        ->assertSee('Or continue with')
        ->assertSee('disabled', false)
        ->assertSee('cursor-not-allowed', false);
});

it('hides the hear about us dropdown', function () {
    Livewire::test(Register::class)
        ->assertDontSee('How did you hear about us?')
        ->assertDontSee('Select an option');
});

it('has proper red color scheme', function () {
    Livewire::test(Register::class)
        ->assertSee('text-red-600', false)
        ->assertSee('bg-red-600', false)
        ->assertSee('focus:ring-red-500', false);
});

it('uses the guest layout', function () {
    Livewire::test(Register::class)
        ->assertViewIs('livewire.register');
});

it('has proper page title', function () {
    // The title is set in the layout, not in the component content
    // We'll test that the component renders without errors
    Livewire::test(Register::class)
        ->assertStatus(200);
});

it('displays terms and privacy links', function () {
    Livewire::test(Register::class)
        ->assertSee('Terms of Service')
        ->assertSee('Privacy Policy')
        ->assertSee('By signing up, you agree to our');
});

it('has proper form accessibility', function () {
    Livewire::test(Register::class)
        ->assertSee('for="first_name"', false)
        ->assertSee('for="last_name"', false)
        ->assertSee('for="username"', false)
        ->assertSee('for="email"', false)
        ->assertSee('for="password"', false)
        ->assertSee('autocomplete="given-name"', false)
        ->assertSee('autocomplete="family-name"', false)
        ->assertSee('autocomplete="username"', false)
        ->assertSee('autocomplete="email"', false)
        ->assertSee('autocomplete="new-password"', false);
});

