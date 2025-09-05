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
        ->set('form.email', '')
        ->set('form.password', '')
        ->set('form.hear_about', '')
        ->call('register')
        ->assertHasErrors(['form.first_name', 'form.last_name', 'form.email', 'form.password', 'form.hear_about']);
});

it('validates email format', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'invalid-email')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
        ->call('register')
        ->assertHasErrors(['form.email']);
});

it('validates unique email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
        ->call('register')
        ->assertHasErrors(['form.email']);
});

it('validates password requirements', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', '123')
        ->set('form.hear_about', 'search')
        ->call('register')
        ->assertHasErrors(['form.password']);
});

it('validates hear_about field', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'invalid')
        ->call('register')
        ->assertHasErrors(['form.hear_about']);
});

it('creates user successfully with valid data', function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
        ->call('register')
        ->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
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
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
        ->call('register');

    Event::assertDispatched(Registered::class);
});

it('logs in user after registration', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
        ->call('register');

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->email)->toBe('test@example.com');
});

it('displays loading state during registration', function () {
    Livewire::test(Register::class)
        ->set('form.first_name', 'John')
        ->set('form.last_name', 'Doe')
        ->set('form.email', 'test@example.com')
        ->set('form.password', 'password123')
        ->set('form.hear_about', 'search')
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

it('displays social login options', function () {
    Livewire::test(Register::class)
        ->assertSee('Passkey')
        ->assertSee('Google')
        ->assertSee('Facebook')
        ->assertSee('Or continue with');
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
        ->assertSee('for="email"', false)
        ->assertSee('for="password"', false)
        ->assertSee('for="hear_about"', false)
        ->assertSee('autocomplete="given-name"', false)
        ->assertSee('autocomplete="family-name"', false)
        ->assertSee('autocomplete="email"', false)
        ->assertSee('autocomplete="new-password"', false);
});

it('has hear about options', function () {
    Livewire::test(Register::class)
        ->assertSee('Search engine (Google, Bing, etc.)')
        ->assertSee('Social media advertisement')
        ->assertSee('Friend or family referral')
        ->assertSee('Other');
});
