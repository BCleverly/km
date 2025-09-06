<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\ContentStatus;
use App\TargetUserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskReward extends Model
{
    /** @use HasFactory<\Database\Factories\TaskRewardFactory> */
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\TaskRewardFactory::new();
    }

    protected $fillable = [
        'title',
        'description',
        'difficulty_level',
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

    public function assignedOutcomes(): HasMany
    {
        return $this->hasMany(UserAssignedTask::class, 'outcome_id')
            ->where('outcome_type', 'reward');
    }

    public function recommendedForTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_recommended_rewards')
            ->withPivot('sort_order')
            ->orderBy('task_recommended_rewards.sort_order');
    }
}
