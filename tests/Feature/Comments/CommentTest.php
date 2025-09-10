<?php

declare(strict_types=1);

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

it('can create a comment', function () {
    $this->actingAs($this->user);

    $comment = Comment::create([
        'content' => 'This is a test comment',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
    ]);

    expect($comment)->toBeInstanceOf(Comment::class);
    expect($comment->content)->toBe('This is a test comment');
    expect($comment->commentable)->toBeInstanceOf(Story::class);
    expect($comment->user)->toBeInstanceOf(User::class);
});

it('can create a reply to a comment', function () {
    $this->actingAs($this->user);

    $parentComment = Comment::create([
        'content' => 'Parent comment',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
    ]);

    $reply = Comment::create([
        'content' => 'This is a reply',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
        'parent_id' => $parentComment->id,
    ]);

    expect($reply->parent)->toBeInstanceOf(Comment::class);
    expect($reply->parent->id)->toBe($parentComment->id);
    expect($parentComment->replies)->toHaveCount(1);
    expect($parentComment->replies->first()->id)->toBe($reply->id);
});

it('can get nested comments for a story', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Top level comment', null, $this->user->id);
    $reply = $this->story->addComment('Reply to comment', $comment->id, $this->user->id);

    $nestedComments = $this->story->getNestedComments();

    expect($nestedComments)->toHaveCount(1);
    expect($nestedComments->first()->replies)->toHaveCount(1);
});

it('can approve and unapprove comments', function () {
    $this->actingAs($this->user);

    $comment = Comment::create([
        'content' => 'Test comment',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
        'is_approved' => false,
    ]);

    expect($comment->is_approved)->toBeFalse();

    $comment->approve($this->user);

    expect($comment->fresh()->is_approved)->toBeTrue();
    expect($comment->fresh()->approved_at)->not->toBeNull();
    expect($comment->fresh()->approved_by)->toBe($this->user->id);
});

it('can get comments count for a story', function () {
    $this->actingAs($this->user);

    $this->story->addComment('Comment 1', null, $this->user->id);
    $this->story->addComment('Comment 2', null, $this->user->id);

    expect($this->story->comments_count)->toBe(2);
    expect($this->story->hasComments())->toBeTrue();
});

it('can check if comment is a reply', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent', null, $this->user->id);
    $reply = $this->story->addComment('Reply', $parentComment->id, $this->user->id);

    expect($parentComment->isReply())->toBeFalse();
    expect($reply->isReply())->toBeTrue();
});

it('can get comment depth', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent', null, $this->user->id);
    $reply = $this->story->addComment('Reply', $parentComment->id, $this->user->id);

    expect($parentComment->depth)->toBe(0);
    expect($reply->depth)->toBe(1);
});

it('can scope comments by approval status', function () {
    $this->actingAs($this->user);

    Comment::create([
        'content' => 'Approved comment',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
        'is_approved' => true,
    ]);

    Comment::create([
        'content' => 'Unapproved comment',
        'commentable_type' => Story::class,
        'commentable_id' => $this->story->id,
        'user_id' => $this->user->id,
        'is_approved' => false,
    ]);

    $approvedComments = Comment::approved()->get();
    $allComments = Comment::all();

    expect($approvedComments)->toHaveCount(1);
    expect($allComments)->toHaveCount(2);
});

it('can scope comments by level', function () {
    $this->actingAs($this->user);

    $parentComment = $this->story->addComment('Parent', null, $this->user->id);
    $reply = $this->story->addComment('Reply', $parentComment->id, $this->user->id);

    $topLevelComments = Comment::topLevel()->get();
    $replyComments = Comment::replies()->get();

    expect($topLevelComments)->toHaveCount(1);
    expect($replyComments)->toHaveCount(1);
    expect($topLevelComments->first()->id)->toBe($parentComment->id);
    expect($replyComments->first()->id)->toBe($reply->id);
});