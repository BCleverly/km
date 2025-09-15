<?php

namespace App\Models;

use App\Enums\CoupleTaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoupleTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'assigned_by',
        'assigned_to',
        'title',
        'description',
        'dom_message',
        'difficulty_level',
        'duration_hours',
        'status',
        'reward_id',
        'punishment_id',
        'assigned_at',
        'deadline_at',
        'completed_at',
        'completion_notes',
        'thank_you_message',
        'thanked_at',
    ];

    /**     
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => CoupleTaskStatus::class,
            'assigned_at' => 'datetime',
            'deadline_at' => 'datetime',
            'completed_at' => 'datetime',
            'thanked_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($coupleTask) {
            // Ensure a user can't assign tasks to themselves
            if ($coupleTask->assigned_by === $coupleTask->assigned_to) {
                throw new \InvalidArgumentException('A user cannot assign tasks to themselves.');
            }
        });
    }

    /**
     * Get the user who assigned this task (the dom)
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who received this task (the sub)
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the reward for completing this task
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\TaskOutcome::class, 'reward_id');
    }

    /**
     * Get the punishment for failing this task
     */
    public function punishment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\TaskOutcome::class, 'punishment_id');
    }

    /**
     * Check if the task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->deadline_at && $this->deadline_at->isPast() && $this->status->isActive();
    }

    /**
     * Check if the task can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === CoupleTaskStatus::Pending;
    }

    /**
     * Check if the task can be thanked
     */
    public function canBeThanked(): bool
    {
        return $this->status === CoupleTaskStatus::Completed && !$this->thanked_at;
    }

    /**
     * Mark the task as completed
     */
    public function markAsCompleted(string $completionNotes = null): void
    {
        $this->update([
            'status' => CoupleTaskStatus::Completed,
            'completed_at' => now(),
            'completion_notes' => $completionNotes,
        ]);

        // Notify the dom that the task was completed
        $this->assignedBy->notify(new \App\Notifications\CoupleTaskCompleted($this));
    }

    /**
     * Mark the task as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => CoupleTaskStatus::Failed,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the task as declined
     */
    public function markAsDeclined(): void
    {
        $this->update([
            'status' => CoupleTaskStatus::Declined,
            'completed_at' => now(),
        ]);
    }

    /**
     * Add a thank you message from the sub to the dom
     */
    public function addThankYou(string $message): void
    {
        $this->update([
            'thank_you_message' => $message,
            'thanked_at' => now(),
        ]);

        // Notify the dom that they were thanked
        $this->assignedBy->notify(new \App\Notifications\CoupleTaskThanked($this));
    }

    /**
     * Scope for tasks assigned by a specific user
     */
    public function scopeAssignedBy($query, User $user)
    {
        return $query->where('assigned_by', $user->id);
    }

    /**
     * Scope for tasks assigned to a specific user
     */
    public function scopeAssignedTo($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    /**
     * Scope for active tasks
     */
    public function scopeActive($query)
    {
        return $query->where('status', CoupleTaskStatus::Pending);
    }

    /**
     * Scope for completed tasks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', CoupleTaskStatus::Completed);
    }

    /**
     * Scope for overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline_at', '<', now())
            ->where('status', CoupleTaskStatus::Pending);
    }
}
