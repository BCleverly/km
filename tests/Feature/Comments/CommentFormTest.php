<?php

declare(strict_types=1);

use App\Livewire\Comments\CommentForm;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->story = Story::factory()->create();
});

it('can render comment form component', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->assertStatus(200)
        ->assertSee('Leave a comment')
        ->assertSee('You can use Markdown');
});

it('can submit a comment', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->set('content', 'This is a test comment')
        ->call('submit')
        ->assertDispatched('comment-added');

    expect(Comment::count())->toBe(1);
    expect(Comment::first()->content)->toBe('This is a test comment');
    expect(Comment::first()->user_id)->toBe($this->user->id);
});

it('can submit a reply', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent comment', null, $this->user->id);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id, 'parentId' => $parentComment->id])
        ->set('content', 'This is a reply')
        ->call('submit')
        ->assertDispatched('reply-added');

    expect(Comment::count())->toBe(2);
    expect(Comment::where('parent_id', $parentComment->id)->first()->content)->toBe('This is a reply');
});

it('validates comment content', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->set('content', '')
        ->call('submit')
        ->assertHasErrors(['content' => 'required']);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->set('content', str_repeat('a', 5001))
        ->call('submit')
        ->assertHasErrors(['content' => 'max']);
});

it('requires authentication to submit comment', function () {
    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->set('content', 'Test comment')
        ->call('submit')
        ->assertHasErrors(['content']);

    expect(Comment::count())->toBe(0);
});

it('can toggle preview', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->set('content', '**Bold text**')
        ->call('togglePreview')
        ->assertSet('showPreview', true)
        ->assertSet('previewContent', '**Bold text**')
        ->assertSee('Preview:')
        ->call('togglePreview')
        ->assertSet('showPreview', false);
});

it('can insert markdown formatting', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->call('insertBold')
        ->assertSet('content', '**bold text**')
        ->call('insertItalic')
        ->assertSet('content', '**bold text***italic text*')
        ->call('insertQuote')
        ->assertSet('content', '**bold text***italic text*> quote text')
        ->call('insertLink')
        ->assertSet('content', '**bold text***italic text*> quote text[link text](https://example.com)');
});

it('can handle start reply event', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent comment', null, $this->user->id);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id])
        ->dispatch('start-reply', ['parentId' => $parentComment->id])
        ->assertSet('parentId', $parentComment->id);
});

it('can handle cancel reply event', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent comment', null, $this->user->id);

    Livewire::test(CommentForm::class, ['modelPath' => 'App\Models\Story:' . $this->story->id, 'parentId' => $parentComment->id])
        ->set('content', 'Some content')
        ->dispatch('cancel-reply')
        ->assertSet('parentId', null)
        ->assertSet('content', '')
        ->assertSet('showPreview', false);
});