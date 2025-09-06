<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\User;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskActivity;
use App\TaskStatus;
use App\TaskActivityType;
use Lorisleiva\Actions\Concerns\AsAction;

class FailTask
{
    use AsAction;

    public function handle(User $user): array
    {
        $activeTask = $user->assignedTasks()
            ->where('status', TaskStatus::Assigned)
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->first();
        
        if (!$activeTask) {
            return [
                'success' => false,
                'message' => 'No active task to fail',
                'task' => null,
            ];
        }

        // Update task status to failed
        $activeTask->update([
            'status' => TaskStatus::Failed,
            'completed_at' => now(),
            'outcome_type' => 'punishment',
            'outcome_id' => $activeTask->potential_punishment_id,
        ]);

        // Log the failure activity
        TaskActivity::log(
            type: TaskActivityType::Failed,
            user: $user,
            task: $activeTask->task,
            assignedTask: $activeTask,
            title: "Failed task: {$activeTask->task->title}",
            description: "Unfortunately, you didn't complete the task in time."
        );

        // Log punishment received activity
        if ($activeTask->potentialPunishment) {
            TaskActivity::log(
                type: TaskActivityType::PunishmentReceived,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Received punishment for: {$activeTask->task->title}",
                description: $activeTask->potentialPunishment->description
            );
        }

        $punishmentTitle = $activeTask->potentialPunishment?->title ?? 'Unknown punishment';

        return [
            'success' => true,
            'message' => "Task failed. You received a punishment: {$punishmentTitle}",
            'task' => $activeTask->fresh(['task', 'potentialReward', 'potentialPunishment']),
        ];
    }
}