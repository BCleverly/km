<?php

declare(strict_types=1);

use App\ContentStatus;
use App\Livewire\Search\SearchContent;
use App\Models\Fantasy;
use App\Models\Story;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Models\Tag;
use App\Models\User;
use App\TargetUserType;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render the search component', function () {
    Livewire::test(SearchContent::class)
        ->assertStatus(200)
        ->assertSee('Search across all content')
        ->assertSee('Search stories, fantasies, tasks...');
});

it('shows empty state when no query is provided', function () {
    Livewire::test(SearchContent::class)
        ->assertSee('Search across all content')
        ->assertSee('Find stories, fantasies, tasks, outcomes, and tags that match your interests.')
        ->assertDontSee('Results:');
});

it('can search for stories', function () {
    $story = Story::factory()->create([
        'title' => 'Test Story Title',
        'summary' => 'Test story summary',
        'content' => 'This is a test story content',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Story')
        ->assertSee('Test Story Title')
        ->assertSee('Test story summary')
        ->assertSee('Story');
});

it('can search for fantasies', function () {
    $fantasy = Fantasy::factory()->create([
        'content' => 'This is a test fantasy content about power exchange',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'power exchange')
        ->assertSee('Fantasy')
        ->assertSee('This is a test fantasy content about power exchange')
        ->assertSee('Fantasy');
});

it('can search for tasks', function () {
    $task = Task::factory()->create([
        'title' => 'Test Task Title',
        'description' => 'This is a test task description',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Task')
        ->assertSee('Test Task Title')
        ->assertSee('This is a test task description')
        ->assertSee('Task');
});

it('can search for outcomes', function () {
    $outcome = Outcome::factory()->create([
        'title' => 'Test Outcome Title',
        'description' => 'This is a test outcome description',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Outcome')
        ->assertSee('Test Outcome Title')
        ->assertSee('This is a test outcome description')
        ->assertSee('Outcome');
});

it('can search for tags', function () {
    $tag = Tag::create([
        'name' => 'Test Tag',
        'type' => 'target_kink',
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Tag')
        ->assertSee('Test Tag')
        ->assertSee('Tag');
});

it('can filter by content type', function () {
    $story = Story::factory()->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    $fantasy = Fantasy::factory()->create([
        'content' => 'Test Fantasy',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test')
        ->set('type', 'stories')
        ->assertSee('Test Story')
        ->assertDontSee('Test Fantasy');
});

it('can filter by premium content', function () {
    $premiumStory = Story::factory()->create([
        'title' => 'Premium Story',
        'is_premium' => true,
        'status' => ContentStatus::Approved,
    ]);

    $freeStory = Story::factory()->create([
        'title' => 'Free Story',
        'is_premium' => false,
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Story')
        ->set('premium', false)
        ->assertSee('Free Story')
        ->assertDontSee('Premium Story');

    Livewire::test(SearchContent::class)
        ->set('query', 'Story')
        ->set('premium', true)
        ->assertSee('Premium Story')
        ->assertSee('Free Story');
});

it('shows result counts for each content type', function () {
    Story::factory()->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    Fantasy::factory()->create([
        'content' => 'Test Fantasy',
        'status' => ContentStatus::Approved,
    ]);

    Task::factory()->create([
        'title' => 'Test Task',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test')
        ->assertSee('Stories (1)')
        ->assertSee('Fantasies (1)')
        ->assertSee('Tasks (1)');
});

it('can clear search', function () {
    Livewire::test(SearchContent::class)
        ->set('query', 'Test')
        ->set('type', 'stories')
        ->set('premium', true)
        ->call('clearSearch')
        ->assertSet('query', '')
        ->assertSet('type', 'all')
        ->assertSet('premium', false);
});

it('resets page when query changes', function () {
    $component = Livewire::test(SearchContent::class);
    
    // Create enough content to trigger pagination
    Story::factory()->count(20)->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    $component->set('query', 'Test')
        ->set('page', 2)
        ->set('query', 'New Query')
        ->assertSet('page', 1);
});

it('resets page when type changes', function () {
    $component = Livewire::test(SearchContent::class);
    
    Story::factory()->count(20)->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    $component->set('query', 'Test')
        ->set('page', 2)
        ->set('type', 'fantasies')
        ->assertSet('page', 1);
});

it('resets page when premium filter changes', function () {
    $component = Livewire::test(SearchContent::class);
    
    Story::factory()->count(20)->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    $component->set('query', 'Test')
        ->set('page', 2)
        ->set('premium', true)
        ->assertSet('page', 1);
});

it('shows no results message when no matches found', function () {
    Livewire::test(SearchContent::class)
        ->set('query', 'NonExistentContent')
        ->assertSee('No results found')
        ->assertSee('Try adjusting your search terms or filters.');
});

it('can accept query parameter from URL', function () {
    Story::factory()->create([
        'title' => 'URL Test Story',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class, ['q' => 'URL Test'])
        ->assertSet('query', 'URL Test')
        ->assertSee('URL Test Story');
});

it('only shows approved content in results', function () {
    Story::factory()->create([
        'title' => 'Approved Story',
        'status' => ContentStatus::Approved,
    ]);

    Story::factory()->create([
        'title' => 'Pending Story',
        'status' => ContentStatus::Pending,
    ]);

    Story::factory()->create([
        'title' => 'Rejected Story',
        'status' => ContentStatus::Rejected,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Story')
        ->assertSee('Approved Story')
        ->assertDontSee('Pending Story')
        ->assertDontSee('Rejected Story');
});

it('shows author information in results', function () {
    $author = User::factory()->create(['name' => 'Test Author']);
    
    Story::factory()->create([
        'title' => 'Test Story',
        'user_id' => $author->id,
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Story')
        ->assertSee('By Test Author');
});

it('shows anonymous for anonymous fantasies', function () {
    Fantasy::factory()->create([
        'content' => 'Anonymous fantasy content',
        'is_anonymous' => true,
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Anonymous fantasy')
        ->assertSee('By Anonymous');
});

it('shows premium badge for premium content', function () {
    Story::factory()->create([
        'title' => 'Premium Story',
        'is_premium' => true,
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Premium Story')
        ->assertSee('Premium');
});

it('shows tags in search results', function () {
    $tag = Tag::create([
        'name' => 'Test Tag',
        'type' => 'target_kink',
    ]);

    $story = Story::factory()->create([
        'title' => 'Test Story',
        'status' => ContentStatus::Approved,
    ]);

    $story->syncTags([$tag]);

    Livewire::test(SearchContent::class)
        ->set('query', 'Test Story')
        ->assertSee('Test Tag');
});

it('handles empty search query gracefully', function () {
    Livewire::test(SearchContent::class)
        ->set('query', '   ')
        ->assertSee('Search across all content')
        ->assertDontSee('Results:');
});

it('can search across multiple content types simultaneously', function () {
    Story::factory()->create([
        'title' => 'Power Exchange Story',
        'status' => ContentStatus::Approved,
    ]);

    Fantasy::factory()->create([
        'content' => 'Power exchange fantasy content',
        'status' => ContentStatus::Approved,
    ]);

    Task::factory()->create([
        'title' => 'Power Exchange Task',
        'status' => ContentStatus::Approved,
    ]);

    Livewire::test(SearchContent::class)
        ->set('query', 'power exchange')
        ->assertSee('Power Exchange Story')
        ->assertSee('Power exchange fantasy content')
        ->assertSee('Power Exchange Task');
});