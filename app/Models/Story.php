<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Story extends Model implements ReactableInterface
{
    use HasFactory, Reactable, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'word_count',
        'reading_time_minutes',
        'user_id',
        'status',
        'view_count',
        'report_count',
        'is_premium',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'word_count' => 'integer',
            'reading_time_minutes' => 'integer',
            'view_count' => 'integer',
            'report_count' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * Get the user that owns the story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reactions for the story.
     */
    public function reactions(): MorphMany
    {
        return $this->morphMany(\Qirolab\Laravel\Reactions\Models\Reaction::class, 'reactable');
    }

    /**
     * Get the connected stories (many-to-many relationship).
     */
    public function connectedStories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_connections', 'story_id', 'connected_story_id')
            ->withPivot('connection_type', 'description')
            ->withTimestamps();
    }

    /**
     * Get the stories that connect to this story.
     */
    public function connectingStories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'story_connections', 'connected_story_id', 'story_id')
            ->withPivot('connection_type', 'description')
            ->withTimestamps();
    }

    /**
     * Get the activity log options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'summary', 'content', 'status', 'report_count'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope to get approved stories.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 2);
    }

    /**
     * Scope to get pending stories.
     */
    public function scopePending($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get rejected stories.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 4);
    }

    /**
     * Scope to get stories in review.
     */
    public function scopeInReview($query)
    {
        return $query->where('status', 3);
    }

    /**
     * Scope to get public stories (approved and not premium).
     */
    public function scopePublic($query)
    {
        return $query->approved()->where('is_premium', false);
    }

    /**
     * Scope to get premium stories.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            1 => 'Pending',
            2 => 'Approved',
            3 => 'In Review',
            4 => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Check if the story is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 2;
    }

    /**
     * Check if the story is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if the story is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 4;
    }

    /**
     * Check if the story is in review.
     */
    public function isInReview(): bool
    {
        return $this->status === 3;
    }

    /**
     * Get the view count (database + current Redis).
     */
    public function getViewCount(): int
    {
        return app(\App\Services\ViewTrackingService::class)->getViewCount('story', $this->id);
    }

    /**
     * Get the database view count only.
     */
    public function getDatabaseViewCount(): int
    {
        return (int) $this->view_count ?? 0;
    }

    /**
     * Get the current Redis view count only.
     */
    public function getRedisViewCount(): int
    {
        return app(\App\Services\ViewTrackingService::class)->getRedisViewCount('story', $this->id);
    }

    /**
     * Increment the report count.
     */
    public function incrementReportCount(): void
    {
        $this->increment('report_count');
    }

    /**
     * Get the word count from content.
     */
    public function calculateWordCount(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Calculate reading time in minutes based on average reading speed.
     */
    public function calculateReadingTime(): int
    {
        $wordsPerMinute = 200; // Average reading speed
        $wordCount = $this->calculateWordCount();
        
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Generate a unique slug from the title.
     */
    public function generateSlug(): string
    {
        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key name for model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($story) {
            if (empty($story->slug)) {
                $story->slug = $story->generateSlug();
            }
            if (empty($story->word_count)) {
                $story->word_count = $story->calculateWordCount();
            }
            if (empty($story->reading_time_minutes)) {
                $story->reading_time_minutes = $story->calculateReadingTime();
            }
        });

        static::updating(function ($story) {
            if ($story->isDirty('title') && empty($story->slug)) {
                $story->slug = $story->generateSlug();
            }
            if ($story->isDirty('content')) {
                $story->word_count = $story->calculateWordCount();
                $story->reading_time_minutes = $story->calculateReadingTime();
            }
        });
    }
}