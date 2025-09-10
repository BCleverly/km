<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Commentable
{
    /**
     * Get all comments for this model.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get only approved comments for this model.
     */
    public function approvedComments(): MorphMany
    {
        return $this->comments()->approved();
    }

    /**
     * Get only top-level comments (not replies) for this model.
     */
    public function topLevelComments(): MorphMany
    {
        return $this->approvedComments()->topLevel();
    }

    /**
     * Get the total count of approved comments for this model.
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->approvedComments()->count();
    }

    /**
     * Get the total count of all comments (including unapproved) for this model.
     */
    public function getAllCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }

    /**
     * Add a comment to this model.
     */
    public function addComment(string $content, ?int $parentId = null, ?int $userId = null): Comment
    {
        return $this->comments()->create([
            'content' => $content,
            'user_id' => $userId ?? auth()->id(),
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Check if this model has comments.
     */
    public function hasComments(): bool
    {
        return $this->approvedComments()->exists();
    }

    /**
     * Get comments with their replies in a nested structure.
     */
    public function getNestedComments()
    {
        return $this->topLevelComments()
            ->with(['replies.user', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }
}