<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Status extends Model implements ReactableInterface
{
    use HasFactory, Reactable, LogsActivity, SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $with = ['user'];

    /**
     * Get the user who created the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for this status.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'commentable_id')
            ->where('commentable_type', self::class)
            ->approved()
            ->orderBy('created_at', 'asc');
    }

    /**
     * Get all comments for this status (including unapproved).
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'commentable_id')
            ->where('commentable_type', self::class)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Scope to get only public statuses.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get statuses for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to get recent statuses.
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get the maximum character limit for status content.
     */
    public static function getMaxLength(): int
    {
        return config('app.statuses.max_length', 280);
    }

    /**
     * Check if the status content is within the character limit.
     */
    public function isWithinLimit(): bool
    {
        return strlen($this->content) <= self::getMaxLength();
    }

    /**
     * Get the character count for the status content.
     */
    public function getCharacterCountAttribute(): int
    {
        return strlen($this->content);
    }

    /**
     * Get the remaining characters available.
     */
    public function getRemainingCharactersAttribute(): int
    {
        return max(0, self::getMaxLength() - $this->character_count);
    }

    /**
     * Get activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['content', 'is_public'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}