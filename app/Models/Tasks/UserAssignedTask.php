<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserAssignedTask extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected static function newFactory()
    {
        return \Database\Factories\UserAssignedTaskFactory::new();
    }

    protected $fillable = [
        'user_id',
        'task_id',
        'status',
        'outcome_type',
        'outcome_id',
        'potential_reward_id',
        'potential_punishment_id',
        'assigned_at',
        'deadline_at',
        'completed_at',
        'has_completion_image',
        'completion_note',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'assigned_at' => 'datetime',
            'deadline_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class, 'outcome_id');
    }

    public function potentialReward(): BelongsTo
    {
        return $this->belongsTo(Outcome::class, 'potential_reward_id');
    }

    public function potentialPunishment(): BelongsTo
    {
        return $this->belongsTo(Outcome::class, 'potential_punishment_id');
    }

    /**
     * Get the outcome the user received (if they completed or failed the task)
     */
    public function receivedOutcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class, 'outcome_id');
    }

    /**
     * Get what the user missed (the alternative outcome)
     */
    public function missedOutcome(): BelongsTo
    {
        if ($this->outcome_type === 'reward') {
            return $this->belongsTo(Outcome::class, 'potential_punishment_id');
        }
        
        return $this->belongsTo(Outcome::class, 'potential_reward_id');
    }

    /**
     * Check if this task has passed its deadline.
     */
    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->deadline_at->isPast() && $this->status === TaskStatus::Assigned;
    }

    /**
     * Check if this task is approaching its deadline (within 1 hour).
     */
    public function isApproachingDeadline(): bool
    {
        if (!$this->deadline_at || $this->status !== TaskStatus::Assigned) {
            return false;
        }

        return $this->deadline_at->isFuture() && $this->deadline_at->diffInMinutes(now()) <= 60;
    }

    /**
     * Get the time remaining until deadline.
     */
    public function getTimeRemainingAttribute(): ?string
    {
        if (!$this->deadline_at || $this->status !== TaskStatus::Assigned) {
            return null;
        }

        if ($this->deadline_at->isPast()) {
            return 'Overdue';
        }

        return $this->deadline_at->diffForHumans();
    }

    /**
     * Scope to get overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline_at', '<', now())
                    ->where('status', TaskStatus::Assigned);
    }

    /**
     * Scope to get tasks approaching deadline.
     */
    public function scopeApproachingDeadline($query)
    {
        return $query->where('deadline_at', '>', now())
                    ->where('deadline_at', '<=', now()->addHour())
                    ->where('status', TaskStatus::Assigned);
    }

    /**
     * Register media collections for task completion images
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('completion_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Register media conversions for performance optimization
     */
    public function registerMediaConversions(Media $media = null): void
    {
        // Thumbnail for quick loading
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('completion_images');

        // Medium size for display
        $this->addMediaConversion('medium')
            ->width(800)
            ->height(600)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('completion_images');

        // Large size for full view
        $this->addMediaConversion('large')
            ->width(1200)
            ->height(900)
            ->sharpen(10)
            ->optimize()
            ->performOnCollections('completion_images');
    }

    /**
     * Get the completion image URL
     */
    public function getCompletionImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('completion_images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get the completion image thumbnail URL
     */
    public function getCompletionImageThumbUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('completion_images');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Get the completion image medium URL
     */
    public function getCompletionImageMediumUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('completion_images');
        return $media ? $media->getUrl('medium') : null;
    }

    /**
     * Get the completion image large URL
     */
    public function getCompletionImageLargeUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('completion_images');
        return $media ? $media->getUrl('large') : null;
    }
}
