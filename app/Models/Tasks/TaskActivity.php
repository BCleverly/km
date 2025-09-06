<?php

declare(strict_types=1);

namespace App\Models\Tasks;

use App\TaskActivityType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivity extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\TaskActivityFactory::new();
    }

    protected $fillable = [
        'user_id',
        'task_id',
        'user_assigned_task_id',
        'activity_type',
        'title',
        'description',
        'metadata',
        'activity_at',
    ];

    protected function casts(): array
    {
        return [
            'activity_type' => TaskActivityType::class,
            'metadata' => 'array',
            'activity_at' => 'datetime',
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

    public function userAssignedTask(): BelongsTo
    {
        return $this->belongsTo(UserAssignedTask::class);
    }

    /**
     * Create a new task activity record
     */
    public static function log(
        TaskActivityType $type,
        User $user,
        \App\Models\Tasks\Task $task,
        ?UserAssignedTask $assignedTask = null,
        ?string $title = null,
        ?string $description = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'user_assigned_task_id' => $assignedTask?->id,
            'activity_type' => $type,
            'title' => $title ?? self::generateTitle($type, $task, $assignedTask),
            'description' => $description ?? self::generateDescription($type, $task, $assignedTask),
            'metadata' => $metadata,
            'activity_at' => now(),
        ]);
    }

    /**
     * Generate a title for the activity
     */
    private static function generateTitle(TaskActivityType $type, \App\Models\Tasks\Task $task, ?UserAssignedTask $assignedTask): string
    {
        return match($type) {
            TaskActivityType::Assigned => "Assigned task: {$task->title}",
            TaskActivityType::Completed => "Completed task: {$task->title}",
            TaskActivityType::Failed => "Failed task: {$task->title}",
            TaskActivityType::RewardReceived => "Received reward for: {$task->title}",
            TaskActivityType::PunishmentReceived => "Received punishment for: {$task->title}",
            TaskActivityType::TaskCreated => "Created task: {$task->title}",
            TaskActivityType::TaskViewed => "Viewed task: {$task->title}",
        };
    }

    /**
     * Generate a description for the activity
     */
    private static function generateDescription(TaskActivityType $type, \App\Models\Tasks\Task $task, ?UserAssignedTask $assignedTask): ?string
    {
        return match($type) {
            TaskActivityType::Assigned => "You were assigned a new task with difficulty level {$task->difficulty_level}.",
            TaskActivityType::Completed => "Great job! You completed the task successfully.",
            TaskActivityType::Failed => "Unfortunately, you didn't complete the task in time.",
            TaskActivityType::RewardReceived => $assignedTask?->receivedReward?->description ?? "You received a reward for completing the task.",
            TaskActivityType::PunishmentReceived => $assignedTask?->receivedPunishment?->description ?? "You received a punishment for not completing the task.",
            TaskActivityType::TaskCreated => "You created a new task for the community.",
            TaskActivityType::TaskViewed => "You viewed the task details.",
        };
    }
}
