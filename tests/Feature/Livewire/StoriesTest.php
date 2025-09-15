<?php

use App\ContentStatus;
use App\Livewire\Stories\ListStories;
use App\Livewire\Stories\CreateStory;
use App\Livewire\Stories\ShowStory;
use App\Models\User;
use App\Models\Story;
use Livewire\Livewire;

it('can list stories', function () {
    $user = User::factory()->create();
    
    Story::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => ContentStatus::Approved,
    ]);

    Livewire::actingAs($user)
        ->test(ListStories::class)
        ->assertSee('Stories')
        ->assertSee('Write Story');
});

it('can create a story as draft', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'This is a test story summary.')
        ->set('content', 'This is a test story content.')
        ->call('saveAsDraft')
        ->assertRedirect(route('app.stories.index'));

    $this->assertDatabaseHas('stories', [
        'title' => 'Test Story',
        'summary' => 'This is a test story summary.',
        'user_id' => $user->id,
        'status' => ContentStatus::Draft,
    ]);
});

it('can submit a story for review', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'This is a test story summary.')
        ->set('content', 'This is a test story with enough words to meet the minimum requirements. It should be at least 100 words long to pass validation. This content is being written to ensure we meet the word count requirement for the story creation test.')
        ->call('submitForReview')
        ->assertRedirect(route('app.stories.index'));

    $this->assertDatabaseHas('stories', [
        'title' => 'Test Story',
        'summary' => 'This is a test story summary.',
        'user_id' => $user->id,
        'status' => ContentStatus::Pending,
    ]);
});

it('validates story content minimum length for submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'Test summary')
        ->set('content', 'Short content')
        ->call('submitForReview')
        ->assertHasErrors(['content']);
});

it('allows saving draft with minimal content', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'Test summary')
        ->set('content', 'Short content')
        ->call('saveAsDraft')
        ->assertRedirect(route('app.stories.index'));

    $this->assertDatabaseHas('stories', [
        'title' => 'Test Story',
        'user_id' => $user->id,
        'status' => ContentStatus::Draft,
    ]);
});

it('can show a story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create([
        'status' => ContentStatus::Approved,
    ]);
    
    Livewire::actingAs($user)
        ->test(ShowStory::class, ['story' => $story])
        ->assertSee($story->title)
        ->assertSee($story->content);

    // View count is now tracked in Redis, not in the database
    // We can't easily test this without Redis, so we'll just verify the component loads
});

it('can report a story', function () {
    $user = User::factory()->create();
    $story = Story::factory()->create([
        'status' => ContentStatus::Approved,
    ]);
    
    Livewire::actingAs($user)
        ->test(ListStories::class)
        ->call('reportStory', $story->id)
        ->assertDispatched('notify');

    $story->refresh();
    expect($story->report_count)->toBe(1);
});