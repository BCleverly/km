<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\Models\Tasks\UserAssignedTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'outcome_id',
        'outcome_type',
        'task_id',
        'user_assigned_task_id',
        'status',
        'notes',
        'assigned_at',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this outcome
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the polymorphic outcome (reward or punishment)
     */
    public function outcome(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the task that this outcome is related to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the assigned task that this outcome is related to
     */
    public function assignedTask(): BelongsTo
    {
        return $this->belongsTo(UserAssignedTask::class, 'user_assigned_task_id');
    }

    /**
     * Scope to get active outcomes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get completed outcomes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get expired outcomes
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope to get outcomes that are not expired
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if this outcome is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark this outcome as completed
     */
    public function markAsCompleted(?string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'notes' => $notes ?: $this->notes,
        ]);
    }

    /**
     * Mark this outcome as expired
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    /**
     * Get the outcome title
     */
    public function getOutcomeTitleAttribute(): string
    {
        return $this->outcome?->title ?? 'Unknown Outcome';
    }

    /**
     * Get the outcome description
     */
    public function getOutcomeDescriptionAttribute(): string
    {
        return $this->outcome?->description ?? 'No description available';
    }

    /**
     * Get the outcome type (reward or punishment)
     */
    public function getOutcomeTypeLabelAttribute(): string
    {
        $outcomeType = $this->attributes['outcome_type'] ?? null;
        return $outcomeType === 'App\\Models\\Tasks\\TaskReward' ? 'reward' : 'punishment';
    }
}