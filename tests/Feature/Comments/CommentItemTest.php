<?php

declare(strict_types=1);

use App\Livewire\Comments\CommentItem;
use App\Models\Comment;
use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
    $this->admin->givePermissionTo('edit comments');
    $this->admin->givePermissionTo('delete comments');
    $this->story = Story::factory()->create();
});

it('can render comment item component', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Test comment', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->assertStatus(200)
        ->assertSee('Test comment')
        ->assertSee($this->user->name);
});

it('can edit comment when user is owner', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Original content', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->assertSet('isEditing', true)
        ->set('editContent', 'Updated content')
        ->call('saveEdit')
        ->assertSet('isEditing', false)
        ->assertDispatched('comment-updated');

    expect($comment->fresh()->content)->toBe('Updated content');
});

it('can edit comment when user has permission', function () {
    $this->actingAs($this->admin);

    $comment = $this->story->addComment('Original content', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->set('editContent', 'Updated by admin')
        ->call('saveEdit')
        ->assertDispatched('comment-updated');

    expect($comment->fresh()->content)->toBe('Updated by admin');
});

it('cannot edit comment when user is not owner and has no permission', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $comment = $this->story->addComment('Original content', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->assertSet('isEditing', false);
});

it('can cancel edit', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Original content', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->set('editContent', 'Modified content')
        ->call('cancelEdit')
        ->assertSet('isEditing', false)
        ->assertSet('editContent', 'Original content');
});

it('can delete comment when user is owner', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Comment to delete', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('deleteComment')
        ->assertDispatched('comment-deleted');

    expect(Comment::count())->toBe(0);
});

it('can delete comment when user has permission', function () {
    $this->actingAs($this->admin);

    $comment = $this->story->addComment('Comment to delete', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('deleteComment')
        ->assertDispatched('comment-deleted');

    expect(Comment::count())->toBe(0);
});

it('cannot delete comment when user is not owner and has no permission', function () {
    $otherUser = User::factory()->create();
    $this->actingAs($otherUser);

    $comment = $this->story->addComment('Comment to delete', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('deleteComment');

    expect(Comment::count())->toBe(1);
});

it('can toggle replies visibility', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Parent comment', null, $this->user->id);
    $this->story->addComment('Reply comment', $comment->id, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->assertSet('showReplies', true)
        ->call('toggleReplies')
        ->assertSet('showReplies', false)
        ->call('toggleReplies')
        ->assertSet('showReplies', true);
});

it('can check if user can reply', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Parent comment', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->assertMethod('canReply', true);

    // Test depth limit
    $reply = $this->story->addComment('First reply', $comment->id, $this->user->id);
    $secondReply = $this->story->addComment('Second reply', $reply->id, $this->user->id);
    $thirdReply = $this->story->addComment('Third reply', $secondReply->id, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $thirdReply])
        ->assertMethod('canReply', false);
});

it('can handle reply added event', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Parent comment', null, $this->user->id);

    $component = Livewire::test(CommentItem::class, ['comment' => $comment]);

    $this->story->addComment('New reply', $comment->id, $this->user->id);

    $component->dispatch('reply-added')
        ->assertSee('New reply');
});

it('validates edit content', function () {
    $this->actingAs($this->user);

    $comment = $this->story->addComment('Original content', null, $this->user->id);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->set('editContent', '')
        ->call('saveEdit')
        ->assertHasErrors(['editContent' => 'required']);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->call('startEdit')
        ->set('editContent', str_repeat('a', 5001))
        ->call('saveEdit')
        ->assertHasErrors(['editContent' => 'max']);
});