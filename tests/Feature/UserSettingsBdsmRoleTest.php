<?php

use App\Enums\BdsmRole;
use App\Livewire\User\Settings;
use App\Models\User;
use Livewire\Livewire;

it('can set BDSM role to dominant', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
    ]);

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.bdsm_role', 1)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile->bdsm_role)->toBe(BdsmRole::Dominant);
    expect($user->isDominant())->toBeTrue();
});

it('can set BDSM role to submissive', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
    ]);

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.bdsm_role', 2)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile->bdsm_role)->toBe(BdsmRole::Submissive);
    expect($user->isSubmissive())->toBeTrue();
});

it('can set BDSM role to switch', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
    ]);

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.bdsm_role', 3)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile->bdsm_role)->toBe(BdsmRole::Switch);
    expect($user->isSwitch())->toBeTrue();
});

it('can clear BDSM role by setting to null', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
        'bdsm_role' => BdsmRole::Dominant,
    ]);

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.bdsm_role', null)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile->bdsm_role)->toBeNull();
    expect($user->hasBdsmRole())->toBeFalse();
});

it('validates BDSM role values', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
    ]);

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.bdsm_role', 4) // Invalid value
        ->call('save')
        ->assertHasErrors(['form.bdsm_role']);
});

it('initializes form with existing BDSM role', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'username' => 'testuser',
        'about' => 'Test user',
        'bdsm_role' => BdsmRole::Switch,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(Settings::class);
    
    // Refresh the user to ensure the profile relationship is loaded
    $user->refresh();
    $user->load('profile');
    
    expect($component->form->bdsm_role)->toBe(3); // Switch value
});

it('creates profile with BDSM role when profile does not exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(Settings::class)
        ->set('form.username', 'newuser')
        ->set('form.about', 'New user')
        ->set('form.bdsm_role', 2)
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->profile)->not->toBeNull();
    expect($user->profile->bdsm_role)->toBe(BdsmRole::Submissive);
    expect($user->isSubmissive())->toBeTrue();
});