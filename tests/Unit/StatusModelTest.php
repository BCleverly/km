<?php

use App\Models\Status;
use App\Models\User;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $status = Status::factory()->create(['user_id' => $user->id]);

    expect($status->user)->toBeInstanceOf(User::class);
    expect($status->user->id)->toBe($user->id);
});

it('has correct fillable attributes', function () {
    $status = new Status();
    $fillable = ['content', 'user_id', 'is_public'];

    expect($status->getFillable())->toBe($fillable);
});

it('casts is_public to boolean', function () {
    $status = Status::factory()->create(['is_public' => 1]);

    expect($status->is_public)->toBeTrue();
    expect($status->getAttributes()['is_public'])->toBe(1);
});

it('has default max length configuration', function () {
    expect(Status::getMaxLength())->toBe(280);
});

it('can check if content is within limit', function () {
    $status = Status::factory()->create(['content' => str_repeat('a', 100)]);
    
    expect($status->isWithinLimit())->toBeTrue();

    $longStatus = Status::factory()->create(['content' => str_repeat('a', 500)]);
    expect($longStatus->isWithinLimit())->toBeFalse();
});

it('calculates character count correctly', function () {
    $content = 'Hello, world!';
    $status = Status::factory()->create(['content' => $content]);

    expect($status->character_count)->toBe(strlen($content));
});

it('calculates remaining characters correctly', function () {
    $content = 'Hello, world!';
    $status = Status::factory()->create(['content' => $content]);
    $expected = Status::getMaxLength() - strlen($content);

    expect($status->remaining_characters)->toBe($expected);
});

it('shows zero remaining characters when over limit', function () {
    $longContent = str_repeat('a', Status::getMaxLength() + 10);
    $status = Status::factory()->create(['content' => $longContent]);

    expect($status->remaining_characters)->toBe(0);
});

it('can scope to public statuses', function () {
    Status::factory()->create(['is_public' => true]);
    Status::factory()->create(['is_public' => false]);

    $publicStatuses = Status::public()->get();

    expect($publicStatuses)->toHaveCount(1);
    expect($publicStatuses->first()->is_public)->toBeTrue();
});

it('can scope to user statuses', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Status::factory()->create(['user_id' => $user1->id]);
    Status::factory()->create(['user_id' => $user2->id]);

    $user1Statuses = Status::forUser($user1)->get();

    expect($user1Statuses)->toHaveCount(1);
    expect($user1Statuses->first()->user_id)->toBe($user1->id);
});

it('can scope to recent statuses', function () {
    $oldStatus = Status::factory()->create(['created_at' => now()->subDays(5)]);
    $newStatus = Status::factory()->create(['created_at' => now()]);

    $recentStatuses = Status::recent(1)->get();

    expect($recentStatuses)->toHaveCount(1);
    expect($recentStatuses->first()->id)->toBe($newStatus->id);
});

it('can have comments', function () {
    $status = Status::factory()->create();
    $comment = \App\Models\Comment::factory()->create([
        'commentable_type' => Status::class,
        'commentable_id' => $status->id,
    ]);

    expect($status->comments)->toHaveCount(1);
    expect($status->comments->first()->id)->toBe($comment->id);
});

it('only shows approved comments', function () {
    $status = Status::factory()->create();
    
    \App\Models\Comment::factory()->create([
        'commentable_type' => Status::class,
        'commentable_id' => $status->id,
        'is_approved' => true,
    ]);
    
    \App\Models\Comment::factory()->create([
        'commentable_type' => Status::class,
        'commentable_id' => $status->id,
        'is_approved' => false,
    ]);

    expect($status->comments)->toHaveCount(1);
    expect($status->allComments)->toHaveCount(2);
});

it('uses soft deletes', function () {
    $status = Status::factory()->create();
    $statusId = $status->id;

    $status->delete();

    expect(Status::find($statusId))->toBeNull();
    expect(Status::withTrashed()->find($statusId))->not->toBeNull();
});