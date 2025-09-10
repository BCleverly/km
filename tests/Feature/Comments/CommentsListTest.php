<?php

declare(strict_types=1);

use App\Livewire\Comments\CommentsList;
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

it('can render comments list component', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentsList::class, ['commentable' => $this->story])
        ->assertStatus(200)
        ->assertSee('No comments yet');
});

it('can display existing comments', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Test comment', null, $this->user->id);

    Livewire::test(CommentsList::class, ['commentable' => $this->story])
        ->assertSee('Test comment')
        ->assertSee($this->user->name);
});

it('can handle comment added event', function () {
    $this->actingAs($this->user);

    $component = Livewire::test(CommentsList::class, ['commentable' => $this->story]);

    $this->story->addComment('New comment', null, $this->user->id);

    $component->dispatch('comment-added')
        ->assertSee('New comment');
});

it('can handle reply added event', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent comment', null, $this->user->id);

    $component = Livewire::test(CommentsList::class, ['commentable' => $this->story]);

    $this->story->addComment('Reply comment', $parentComment->id, $this->user->id);

    $component->dispatch('reply-added')
        ->assertSee('Reply comment');
});

it('can start and cancel reply', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent comment', null, $this->user->id);

    Livewire::test(CommentsList::class, ['commentable' => $this->story])
        ->call('startReply', $parentComment->id)
        ->assertSet('replyingTo', $parentComment->id)
        ->call('cancelReply')
        ->assertSet('replyingTo', null);
});

it('can paginate comments', function () {
    $this->actingAs($this->user);

    // Create 15 comments
    for ($i = 1; $i <= 15; $i++) {
        $this->story->addComment("Comment {$i}", null, $this->user->id);
    }

    Livewire::test(CommentsList::class, ['commentable' => $this->story, 'perPage' => 10])
        ->assertSee('Comment 1')
        ->assertSee('Comment 10')
        ->assertDontSee('Comment 11')
        ->call('nextPage')
        ->assertSee('Comment 11')
        ->assertDontSee('Comment 1');
});

it('can hide comment form when showForm is false', function () {
    $this->actingAs($this->user);

    Livewire::test(CommentsList::class, ['commentable' => $this->story, 'showForm' => false])
        ->assertDontSee('Leave a comment');
});