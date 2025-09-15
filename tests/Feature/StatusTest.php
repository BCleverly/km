<?php

use App\Livewire\Status\CreateStatus;
use App\Livewire\Status\StatusList;
use App\Livewire\Status\StatusItem;
use App\Models\Status;
use App\Models\User;
use Livewire\Livewire;

it('can create a status when authenticated', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', 'This is my first status!')
        ->set('isPublic', true)
        ->call('create')
        ->assertNotified()
        ->assertSet('content', '');

    $this->assertDatabaseHas('statuses', [
        'user_id' => $user->id,
        'content' => 'This is my first status!',
        'is_public' => true,
    ]);
});

it('cannot create a status when not authenticated', function () {
    Livewire::test(CreateStatus::class)
        ->set('content', 'This should not work')
        ->call('create')
        ->assertDispatched('show-notification', [
            'message' => 'Please log in to create a status.',
            'type' => 'error',
        ]);

    $this->assertDatabaseMissing('statuses', [
        'content' => 'This should not work',
    ]);
});

it('validates status content is required', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', '')
        ->call('create')
        ->assertHasErrors(['content' => 'required']);
});

it('validates status content length', function () {
    $user = User::factory()->create();
    $maxLength = Status::getMaxLength();
    $longContent = str_repeat('a', $maxLength + 1);

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', $longContent)
        ->call('create')
        ->assertHasErrors(['content' => 'max']);
});

it('can create a status with maximum allowed length', function () {
    $user = User::factory()->create();
    $maxLength = Status::getMaxLength();
    $maxContent = str_repeat('a', $maxLength);

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', $maxContent)
        ->call('create')
        ->assertNotified();

    $this->assertDatabaseHas('statuses', [
        'user_id' => $user->id,
        'content' => $maxContent,
    ]);
});

it('enforces daily status limit', function () {
    $user = User::factory()->create();
    $maxPerDay = config('app.statuses.max_per_user_per_day', 10);

    // Create the maximum number of statuses for today
    Status::factory()->count($maxPerDay)->create([
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', 'This should be blocked')
        ->call('create')
        ->assertDispatched('show-notification', [
            'message' => 'You have reached your daily status limit.',
            'type' => 'error',
        ]);

    $this->assertDatabaseMissing('statuses', [
        'content' => 'This should be blocked',
    ]);
});

it('can display status list', function () {
    $user = User::factory()->create();
    $statuses = Status::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_public' => true,
    ]);

    Livewire::test(StatusList::class, ['user' => $user])
        ->assertSee($statuses->first()->content)
        ->assertSee($user->display_name);
});

it('only shows public statuses in status list', function () {
    $user = User::factory()->create();
    $publicStatus = Status::factory()->create([
        'user_id' => $user->id,
        'is_public' => true,
        'content' => 'This is public',
    ]);
    $privateStatus = Status::factory()->create([
        'user_id' => $user->id,
        'is_public' => false,
        'content' => 'This is private',
    ]);

    Livewire::test(StatusList::class, ['user' => $user])
        ->assertSee('This is public')
        ->assertDontSee('This is private');
});

it('can delete own status', function () {
    $user = User::factory()->create();
    $status = Status::factory()->create([
        'user_id' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(StatusItem::class, ['status' => $status])
        ->call('deleteStatus')
        ->assertNotified();

    $this->assertSoftDeleted('statuses', [
        'id' => $status->id,
    ]);
});

it('cannot delete another users status', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $status = Status::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    Livewire::actingAs($user)
        ->test(StatusItem::class, ['status' => $status])
        ->call('deleteStatus')
        ->assertDispatched('show-notification', [
            'message' => 'You are not authorized to delete this status.',
            'type' => 'error',
        ]);

    $this->assertDatabaseHas('statuses', [
        'id' => $status->id,
    ]);
});

it('shows character count correctly', function () {
    $user = User::factory()->create();
    $content = 'Hello world!';

    Livewire::actingAs($user)
        ->test(CreateStatus::class)
        ->set('content', $content)
        ->assertSet('characterCount', strlen($content))
        ->assertSet('remainingCharacters', Status::getMaxLength() - strlen($content));
});

it('shows time ago correctly', function () {
    $status = Status::factory()->create([
        'created_at' => now()->subHours(2),
    ]);

    Livewire::test(StatusItem::class, ['status' => $status])
        ->assertSee('2 hours ago');
});

it('can toggle create form visibility', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(StatusList::class)
        ->assertSet('showCreateForm', false)
        ->call('toggleCreateForm')
        ->assertSet('showCreateForm', true)
        ->call('toggleCreateForm')
        ->assertSet('showCreateForm', false);
});

it('shows daily limit message when reached', function () {
    $user = User::factory()->create();
    $maxPerDay = config('app.statuses.max_per_user_per_day', 10);

    // Create the maximum number of statuses for today
    Status::factory()->count($maxPerDay)->create([
        'user_id' => $user->id,
        'created_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(StatusList::class)
        ->assertSee("Daily limit reached ({$maxPerDay}/{$maxPerDay})")
        ->assertSet('canCreateStatus', false);
});