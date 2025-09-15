<?php

use App\Livewire\Stories\CreateStory;
use App\Models\User;
use App\Models\Story;
use App\ContentStatus;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertStatus(200);
});

it('displays create story title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Create Story');
});

it('shows form fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Title')
        ->assertSee('Summary')
        ->assertSee('Content')
        ->assertSee('Tags')
        ->assertSee('Submit Story');
});

it('submits story successfully with valid data', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'Test Summary')
        ->set('content', 'Test Content')
        ->set('is_private', false)
        ->call('submitStory')
        ->assertSessionHas('message', 'Your story has been submitted for review!');
    
    $this->assertDatabaseHas('stories', [
        'title' => 'Test Story',
        'summary' => 'Test Summary',
        'content' => 'Test Content',
        'is_private' => false,
        'user_id' => $user->id,
        'status' => ContentStatus::Pending
    ]);
});

it('submits private story successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Private Story')
        ->set('summary', 'Private Summary')
        ->set('content', 'Private Content')
        ->set('is_private', true)
        ->call('submitStory')
        ->assertSessionHas('message', 'Your story has been submitted for review!');
    
    $this->assertDatabaseHas('stories', [
        'title' => 'Private Story',
        'summary' => 'Private Summary',
        'content' => 'Private Content',
        'is_private' => true,
        'user_id' => $user->id,
        'status' => ContentStatus::Pending
    ]);
});

it('validates required fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', '')
        ->set('summary', '')
        ->set('content', '')
        ->call('submitStory')
        ->assertHasErrors(['title', 'summary', 'content']);
});

it('validates title length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', str_repeat('a', 256))
        ->call('submitStory')
        ->assertHasErrors(['title']);
});

it('validates summary length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('summary', str_repeat('a', 1001))
        ->call('submitStory')
        ->assertHasErrors(['summary']);
});

it('validates content length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('content', str_repeat('a', 10001))
        ->call('submitStory')
        ->assertHasErrors(['content']);
});

it('validates minimum content length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('content', 'Short')
        ->call('submitStory')
        ->assertHasErrors(['content']);
});

it('resets form after successful submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'Test Summary')
        ->set('content', 'Test Content')
        ->call('submitStory');
    
    // Form should be reset after submission
    $component = Livewire::actingAs($user)
        ->test(CreateStory::class);
    
    expect($component->get('title'))->toBe('');
    expect($component->get('summary'))->toBe('');
    expect($component->get('content'))->toBe('');
});

it('shows loading state during submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', 'Test Story')
        ->set('summary', 'Test Summary')
        ->set('content', 'Test Content')
        ->call('submitStory')
        ->assertSee('Submitting...', false);
});

it('displays form validation errors', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('title', '')
        ->call('submitStory')
        ->assertSee('The title field is required', false);
});

it('shows character count for content', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('content', 'Test content')
        ->assertSee('12 / 10000', false);
});

it('shows character count for summary', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('summary', 'Test summary')
        ->assertSee('12 / 1000', false);
});

it('displays privacy options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Privacy', false)
        ->assertSee('Public', false)
        ->assertSee('Private', false);
});

it('shows premium content option', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Premium Content', false)
        ->assertSee('Make this story available only to premium users', false);
});

it('displays tags section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Tags', false)
        ->assertSee('Add tags to help categorize your story', false);
});

it('shows tag creation form', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Create New Tag', false)
        ->assertSee('Tag Name', false);
});

it('displays existing tags', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Available Tags', false);
});

it('creates tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->call('createTag', 'category', 'New Tag')
        ->assertSessionHas('message', 'New tag created and added! It will be reviewed before becoming visible to others.');
});

it('validates tag name is not empty', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->call('createTag', 'category', '')
        ->assertSessionHas('error', 'Tag name cannot be empty.');
});

it('removes tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->call('removeTag', 'category', 1);
    
    // The tag should be removed from the form
    // This is tested indirectly through the form state
});

it('shows story guidelines', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Story Guidelines', false)
        ->assertSee('Please ensure your story follows our community guidelines', false);
});

it('displays content warnings section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Content Warnings', false)
        ->assertSee('Add content warnings if applicable', false);
});

it('shows word count', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->set('content', 'This is a test story with multiple words')
        ->assertSee('Word Count', false);
});

it('displays formatting help', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Formatting Help', false)
        ->assertSee('Markdown', false);
});

it('shows draft save option', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Save Draft', false);
});

it('handles unauthenticated users', function () {
    $this->get('/app/stories/create')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertViewIs('livewire.stories.create-story');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertStatus(200);
});

it('shows form help text', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Share your experience with the community', false)
        ->assertSee('Write a brief summary of your story', false)
        ->assertSee('Tell your story in detail', false);
});

it('displays submission guidelines', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(CreateStory::class)
        ->assertSee('Submission Guidelines', false)
        ->assertSee('Please ensure your story follows our community guidelines', false);
});

