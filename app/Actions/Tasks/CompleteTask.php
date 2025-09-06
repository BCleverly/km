<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\User;
use App\Models\UserOutcome;
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

        // Create UserOutcome record for the reward
        if ($activeTask->potentialReward) {
            // Clean up expired outcomes first
            $user->cleanupExpiredOutcomes();
            
            // Check if user has reached outcome limit
            if ($user->hasReachedOutcomeLimit()) {
                // Replace the oldest active outcome
                $oldestOutcome = $user->getOldestActiveOutcome();
                if ($oldestOutcome) {
                    $oldestOutcome->markAsExpired();
                }
            }
            
            UserOutcome::create([
                'user_id' => $user->id,
                'outcome_id' => $activeTask->potentialReward->id,
                'outcome_type' => 'App\\Models\\Tasks\\TaskReward',
                'task_id' => $activeTask->task->id,
                'user_assigned_task_id' => $activeTask->id,
                'status' => 'active',
                'assigned_at' => now(),
                'expires_at' => $this->calculateRewardExpiry($activeTask->potentialReward),
            ]);

            // Log reward received activity
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

    /**
     * Calculate when a reward should expire based on its difficulty level
     */
    private function calculateRewardExpiry($reward): ?\Carbon\Carbon
    {
        // Rewards with higher difficulty levels last longer
        $daysToExpire = match ($reward->difficulty_level) {
            1, 2, 3 => 1,    // Easy rewards expire in 1 day
            4, 5, 6 => 3,    // Medium rewards expire in 3 days
            7, 8, 9 => 7,    // Hard rewards expire in 1 week
            10 => 14,        // Very hard rewards expire in 2 weeks
            default => 1,    // Default to 1 day
        };

        return now()->addDays($daysToExpire);
    }
}