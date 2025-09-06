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

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\TaskFactory::new();
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

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(UserAssignedTask::class);
    }

    public function recommendedRewards(): BelongsToMany
    {
        return $this->belongsToMany(TaskReward::class, 'task_recommended_rewards')
            ->withPivot('sort_order')
            ->orderBy('task_recommended_rewards.sort_order');
    }

    public function recommendedPunishments(): BelongsToMany
    {
        return $this->belongsToMany(TaskPunishment::class, 'task_recommended_punishments')
            ->withPivot('sort_order')
            ->orderBy('task_recommended_punishments.sort_order');
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
}
