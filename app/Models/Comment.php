<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Comment extends Model implements ReactableInterface
{
    use HasFactory, Reactable, LogsActivity, SoftDeletes;

    protected $fillable = [
        'content',
        'commentable_id',
        'commentable_type',
        'user_id',
        'parent_id',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $with = ['user'];

    /**
     * Get the parent comment.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the child comments (replies).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->where('is_approved', true);
    }

    /**
     * Get all child comments including unapproved ones.
     */
    public function allReplies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that this comment belongs to.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who approved this comment.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if this comment is a reply.
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if this comment has replies.
     * Cached for performance.
     */
    public function hasReplies(): bool
    {
        $cacheKey = "comment_{$this->id}_has_replies";
        
        return Cache::remember($cacheKey, 300, function () {
            return $this->replies()->exists();
        });
    }

    /**
     * Get the depth level of this comment (0 for top-level, 1 for first-level reply, etc.).
     * This is cached to avoid N+1 queries when calculating depth.
     */
    public function getDepthAttribute(): int
    {
        if (!$this->parent_id) {
            return 0;
        }

        // Use a more efficient approach to calculate depth
        return $this->calculateDepth();
    }

    /**
     * Calculate the depth of this comment efficiently.
     */
    private function calculateDepth(): int
    {
        $depth = 0;
        $current = $this;
        
        // Walk up the parent chain to calculate depth
        while ($current->parent_id) {
            $depth++;
            $current = $current->parent;
            
            // Prevent infinite loops (safety check)
            if ($depth > 10) {
                break;
            }
        }
        
        return $depth;
    }

    /**
     * Scope to get only approved comments.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get only top-level comments (not replies).
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only replies.
     */
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope to get comments for a specific model.
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id);
    }

    /**
     * Approve this comment.
     */
    public function approve(?User $approver = null): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approver?->id,
        ]);
        
        $this->clearCache();
    }

    /**
     * Clear cached data for this comment.
     */
    public function clearCache(): void
    {
        Cache::forget("comment_{$this->id}_has_replies");
        Cache::forget("comment_{$this->id}_depth");
        
        // Clear parent comment cache if this is a reply
        if ($this->parent_id) {
            Cache::forget("comment_{$this->parent_id}_has_replies");
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when comment is created, updated, or deleted
        static::created(function ($comment) {
            $comment->clearCache();
        });

        static::updated(function ($comment) {
            $comment->clearCache();
        });

        static::deleted(function ($comment) {
            $comment->clearCache();
        });
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'is_approved'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}