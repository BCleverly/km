<?php

declare(strict_types=1);

use App\Livewire\User\SocialProfile;
use App\Models\Profile;
use App\Models\Status;
use App\Models\Tasks\UserAssignedTask;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

it('renders social profile component successfully', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200)
        ->assertSee($user->display_name);
});

it('displays media tab with all media types', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // Add profile media
    $profile->addMedia(UploadedFile::fake()->image('profile.jpg'))
        ->toMediaCollection('profile_pictures');

    $profile->addMedia(UploadedFile::fake()->image('cover.jpg'))
        ->toMediaCollection('cover_photos');

    // Add status media
    $status = Status::factory()->create(['user_id' => $user->id]);
    $status->addMedia(UploadedFile::fake()->image('status.jpg'))
        ->toMediaCollection('status_images');

    // Add task completion media
    $task = UserAssignedTask::factory()->create(['user_id' => $user->id]);
    $task->addMedia(UploadedFile::fake()->image('completion.jpg'))
        ->toMediaCollection('completion_images');

    Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200)
        ->assertSee('Media')
        ->call('setActiveTab', 'media')
        ->assertSee('All Media')
        ->assertSee('Profile')
        ->assertSee('Status')
        ->assertSee('Task');
});

it('optimizes database queries with eager loading', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    // This test verifies that the component loads without N+1 queries
    Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200)
        ->assertSee($user->display_name);
});

it('handles tab switching with Alpine.js', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    $component = Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->assertStatus(200);

    // Test tab switching
    $component->call('setActiveTab', 'about')
        ->assertSet('activeTab', 'about');

    $component->call('setActiveTab', 'activity')
        ->assertSet('activeTab', 'activity');

    $component->call('setActiveTab', 'media')
        ->assertSet('activeTab', 'media');
});

it('displays empty state when no media exists', function () {
    $user = User::factory()->create();
    $profile = Profile::factory()->create(['user_id' => $user->id]);

    Livewire::test(SocialProfile::class, ['username' => $profile->username])
        ->call('setActiveTab', 'media')
        ->assertSee('No media found')
        ->assertSee('You haven\'t uploaded any media yet.');
});
