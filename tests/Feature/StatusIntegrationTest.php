<?php

use App\Livewire\Status\StatusList;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

it('can create and display statuses on user profile', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Create some statuses for the user
    $status1 = Status::factory()->create([
        'user_id' => $user->id,
        'content' => 'My first status update!',
        'is_public' => true,
    ]);

    $status2 = Status::factory()->create([
        'user_id' => $user->id,
        'content' => 'Another status update',
        'is_public' => true,
    ]);

    // Create a private status that shouldn't show
    Status::factory()->create([
        'user_id' => $user->id,
        'content' => 'This is private',
        'is_public' => false,
    ]);

    // Test the status list component
    Livewire::test(StatusList::class, ['user' => $user])
        ->assertSee('My first status update!')
        ->assertSee('Another status update')
        ->assertDontSee('This is private')
        ->assertSee($user->display_name);
});

it('shows create form for authenticated users', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(StatusList::class)
        ->assertSee('New Status')
        ->call('toggleCreateForm')
        ->assertSee('Cancel')
        ->assertSee('What\'s on your mind?');
});

it('does not show create form for unauthenticated users', function () {
    Livewire::test(StatusList::class)
        ->assertDontSee('New Status');
});

it('enforces daily limit correctly', function () {
    $user = User::factory()->create();
    $maxPerDay = config('app.statuses.max_per_user_per_day', 10);

    // Create the maximum number of statuses
    Status::factory()->count($maxPerDay)->create([
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(StatusList::class)
        ->assertSee("Daily limit reached ({$maxPerDay}/{$maxPerDay})")
        ->assertDontSee('New Status');
});

it('shows character count and limit correctly', function () {
    $user = User::factory()->create();
    $maxLength = Status::getMaxLength();

    Livewire::actingAs($user)
        ->test(StatusList::class)
        ->call('toggleCreateForm')
        ->assertSee("0 / {$maxLength}");
});

it('can delete own statuses', function () {
    $user = User::factory()->create();
    $status = Status::factory()->create([
        'user_id' => $user->id,
        'content' => 'This will be deleted',
    ]);

    Livewire::actingAs($user)
        ->test(StatusList::class, ['user' => $user])
        ->assertSee('This will be deleted')
        ->call('$refresh'); // Refresh to get the StatusItem component

    // The status should be soft deleted
    expect(Status::find($status->id))->toBeNull();
    expect(Status::withTrashed()->find($status->id))->not->toBeNull();
});

it('shows status timestamps correctly', function () {
    $status = Status::factory()->create([
        'created_at' => now()->subHours(3),
    ]);

    Livewire::test(StatusList::class, ['user' => $status->user])
        ->assertSee('3 hours ago');
});

it('shows public/private status indicators', function () {
    $user = User::factory()->create();
    
    $publicStatus = Status::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    $privateStatus = Status::factory()->create([
        'user_id' => $user->id,
        'is_public' => false,
    ]);

    Livewire::test(StatusList::class, ['user' => $user])
        ->assertSee('Public')
        ->assertDontSee('Private'); // Private statuses shouldn't show in public list
});