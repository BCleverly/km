<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\ContentStatus;
use App\Models\Models\Tag;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;

class Task extends Model implements ReactableInterface
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, HasTags, Reactable;

    protected static function newFactory()
    {
        return \Database\Factories\TaskFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'difficulty_level',
        'duration_time',
        'duration_type',
        'target_user_type',
        'user_id',
        'status',
        'view_count',
        'is_premium',
    ];

    protected function casts(): array
    {
        return [
            'difficulty_level' => 'integer',
            'duration_time' => 'integer',
            'target_user_type' => TargetUserType::class,
            'status' => ContentStatus::class,
            'view_count' => 'integer',
            'is_premium' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(UserAssignedTask::class);
    }

    public function recommendedOutcomes(): BelongsToMany
    {
        return $this->belongsToMany(Outcome::class, 'task_recommended_outcomes')
            ->withPivot('sort_order')
            ->orderBy('task_recommended_outcomes.sort_order');
    }

    public function recommendedRewards(): BelongsToMany
    {
        return $this->recommendedOutcomes()->where('intended_type', 'reward');
    }

    public function recommendedPunishments(): BelongsToMany
    {
        return $this->recommendedOutcomes()->where('intended_type', 'punishment');
    }

    /**
     * Scope a query to only include approved tasks.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', ContentStatus::Approved);
    }

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', ContentStatus::Pending);
    }

    /**
     * Scope a query to only include premium tasks.
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope a query to only include tasks for a specific user type.
     */
    public function scopeForUserType($query, TargetUserType $userType)
    {
        return $query->where('target_user_type', $userType);
    }

    /**
     * Calculate the deadline for this task based on when it was assigned.
     */
    public function calculateDeadline(?\DateTimeInterface $assignedAt = null): \DateTimeInterface
    {
        $assignedAt = $assignedAt ?? now();

        return match ($this->duration_type) {
            'minutes' => $assignedAt->addMinutes($this->duration_time),
            'hours' => $assignedAt->addHours($this->duration_time),
            'days' => $assignedAt->addDays($this->duration_time),
            'weeks' => $assignedAt->addWeeks($this->duration_time),
            default => $assignedAt->addHours($this->duration_time), // Default to hours
        };
    }

    /**
     * Get the duration in a human-readable format.
     */
    public function getDurationDisplayAttribute(): string
    {
        $time = $this->duration_time;
        $type = $this->duration_type;

        // Handle pluralization
        if ($time > 1) {
            $type = match ($type) {
                'minutes' => 'minutes',
                'hours' => 'hours',
                'days' => 'days',
                'weeks' => 'weeks',
                default => 'hours',
            };
        } else {
            $type = match ($type) {
                'minutes' => 'minute',
                'hours' => 'hour',
                'days' => 'day',
                'weeks' => 'week',
                default => 'hour',
            };
        }

        return "{$time} {$type}";
    }

    /**
     * Check if the task has a valid duration configuration.
     */
    public function hasValidDuration(): bool
    {
        return in_array($this->duration_type, ['minutes', 'hours', 'days', 'weeks'])
            && $this->duration_time > 0;
    }

    /**
     * Get the tag class name for this model
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * Override the tags relationship to use our custom tag model
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }
}
