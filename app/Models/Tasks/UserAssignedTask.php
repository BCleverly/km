<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
