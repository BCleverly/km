<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserAssignedTask extends Model
{
    use HasFactory;

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
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'assigned_at' => 'datetime',
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

    public function outcome(): MorphTo
    {
        return $this->morphTo('outcome', 'outcome_type', 'outcome_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(TaskReward::class, 'outcome_id')
            ->where('outcome_type', 'reward');
    }

    public function punishment(): BelongsTo
    {
        return $this->belongsTo(TaskPunishment::class, 'outcome_id')
            ->where('outcome_type', 'punishment');
    }

    public function potentialReward(): BelongsTo
    {
        return $this->belongsTo(TaskReward::class, 'potential_reward_id');
    }

    public function potentialPunishment(): BelongsTo
    {
        return $this->belongsTo(TaskPunishment::class, 'potential_punishment_id');
    }

    /**
     * Get the reward the user received (if they completed the task)
     */
    public function receivedReward(): BelongsTo
    {
        return $this->belongsTo(TaskReward::class, 'outcome_id')
            ->where('outcome_type', 'reward');
    }

    /**
     * Get the punishment the user received (if they failed the task)
     */
    public function receivedPunishment(): BelongsTo
    {
        return $this->belongsTo(TaskPunishment::class, 'outcome_id')
            ->where('outcome_type', 'punishment');
    }

    /**
     * Get what the user missed (the alternative outcome)
     */
    public function missedOutcome(): BelongsTo
    {
        if ($this->outcome_type === 'reward') {
            return $this->belongsTo(TaskPunishment::class, 'potential_punishment_id');
        }
        
        return $this->belongsTo(TaskReward::class, 'potential_reward_id');
    }
}
