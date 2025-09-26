<?php

declare(strict_types=1);

use App\Livewire\User\SocialProfile;
use App\Livewire\User\SocialProfile\MediaTab;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('loads only the active tab initially', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Only the posts tab should be loaded initially
    $component->assertSee('Posts')
        ->assertDontSee('All Media'); // Media tab should not be loaded yet
});

it('lazy loads media tab when accessed', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // Add some media to test
    $profile->addMedia(UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Switch to media tab - this should trigger lazy loading
    $component->call('setActiveTab', 'media')
        ->assertSee('All Media')
        ->assertSee('Profile'); // Should now show the media
});

it('lazy loads activity tab when accessed', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Switch to activity tab
    $component->call('setActiveTab', 'activity')
        ->assertSee('Recent Activity');
});

it('lazy loads about tab when accessed', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Switch to about tab
    $component->call('setActiveTab', 'about')
        ->assertSee('About')
        ->assertSee('Statistics');
});

it('handles tab switching efficiently', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Test switching between tabs
    $component->call('setActiveTab', 'about')
        ->assertSet('activeTab', 'about')
        ->assertSee('About');

    $component->call('setActiveTab', 'activity')
        ->assertSet('activeTab', 'activity')
        ->assertSee('Recent Activity');

    $component->call('setActiveTab', 'media')
        ->assertSet('activeTab', 'media')
        ->assertSee('All Media');
});

it('media tab component works independently', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // Add media
    $profile->addMedia(UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');

    Livewire::test(MediaTab::class, [
        'user' => $user,
        'profile' => $profile,
        'isOwnProfile' => false,
    ])
        ->assertStatus(200)
        ->assertSee('All Media')
        ->assertSee('Profile');
});
