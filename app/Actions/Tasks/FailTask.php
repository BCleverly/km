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

        // Create UserOutcome record for the punishment
        if ($activeTask->potentialPunishment) {
            // Clean up expired outcomes first
            $user->cleanupExpiredOutcomes();
            
            UserOutcome::create([
                'user_id' => $user->id,
                'outcome_id' => $activeTask->potentialPunishment->id,
                'outcome_type' => 'punishment',
                'task_id' => $activeTask->task->id,
                'user_assigned_task_id' => $activeTask->id,
                'status' => 'active',
                'assigned_at' => now(),
                'expires_at' => $this->calculatePunishmentExpiry($activeTask->potentialPunishment),
            ]);

            // Log combined failure + punishment activity
            TaskActivity::log(
                type: TaskActivityType::Failed,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Failed task: {$activeTask->task->title}",
                description: "Unfortunately, you didn't complete the task in time and received a punishment: {$activeTask->potentialPunishment->title}. {$activeTask->potentialPunishment->description}"
            );
        } else {
            // Log failure activity without punishment
            TaskActivity::log(
                type: TaskActivityType::Failed,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Failed task: {$activeTask->task->title}",
                description: "Unfortunately, you didn't complete the task in time."
            );
        }

        $punishmentTitle = $activeTask->potentialPunishment?->title ?? 'Unknown punishment';

        return [
            'success' => true,
            'message' => "Task failed. You received a punishment: {$punishmentTitle}",
            'task' => $activeTask->fresh(['task', 'potentialReward', 'potentialPunishment']),
        ];
    }

    /**
     * Calculate when a punishment should expire based on its difficulty level
     */
    private function calculatePunishmentExpiry($punishment): ?\Carbon\Carbon
    {
        // Punishments with higher difficulty levels last longer
        $daysToExpire = match ($punishment->difficulty_level) {
            1, 2, 3 => 1,    // Easy punishments expire in 1 day
            4, 5, 6 => 3,    // Medium punishments expire in 3 days
            7, 8, 9 => 7,    // Hard punishments expire in 1 week
            10 => 14,        // Very hard punishments expire in 2 weeks
            default => 1,    // Default to 1 day
        };

        return now()->addDays($daysToExpire);
    }
}