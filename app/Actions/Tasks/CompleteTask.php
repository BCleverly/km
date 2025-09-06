<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\User;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskActivity;
use App\TaskStatus;
use App\TaskActivityType;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteTask
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
                'message' => 'No active task to complete',
                'task' => null,
            ];
        }

        // Update task status to completed
        $activeTask->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
            'outcome_type' => 'reward',
            'outcome_id' => $activeTask->potential_reward_id,
        ]);

        // Log the completion activity
        TaskActivity::log(
            type: TaskActivityType::Completed,
            user: $user,
            task: $activeTask->task,
            assignedTask: $activeTask,
            title: "Completed task: {$activeTask->task->title}",
            description: "Great job! You completed the task successfully."
        );

        // Log reward received activity
        if ($activeTask->potentialReward) {
            TaskActivity::log(
                type: TaskActivityType::RewardReceived,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Received reward for: {$activeTask->task->title}",
                description: $activeTask->potentialReward->description
            );
        }

        $rewardTitle = $activeTask->potentialReward?->title ?? 'Unknown reward';

        return [
            'success' => true,
            'message' => "Task completed! You earned a reward: {$rewardTitle}",
            'task' => $activeTask->fresh(['task', 'potentialReward', 'potentialPunishment']),
        ];
    }
}