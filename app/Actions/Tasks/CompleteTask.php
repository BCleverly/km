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
use Illuminate\Http\UploadedFile;

class CompleteTask
{
    use AsAction;

    public function handle(User $user, mixed $completionImage = null, ?string $completionNote = null): array
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
        
        // Check if user can upload images (premium feature)
        $hasImage = false;
        if ($completionImage && $user->canUploadCompletionImages()) {
            $hasImage = true;
        }
        
        //Update task status to completed
        $activeTask->update([
            'status' => TaskStatus::Completed,
            'completed_at' => now(),
            'outcome_type' => 'reward',
            'outcome_id' => $activeTask->potential_reward_id,
            'has_completion_image' => $hasImage,
            'completion_note' => $completionNote,
        ]);

        
        // Handle image upload if provided and user has premium
        if ($completionImage && $user->canUploadCompletionImages()) {
            $activeTask->addMedia($completionImage->getRealPath())
                ->usingName($completionImage->getClientOriginalName())
                ->usingFileName($completionImage->getClientOriginalName())
                ->toMediaCollection('completion_images');
        }

        // Create UserOutcome record for the reward
        if ($activeTask->potentialReward) {
            // Clean up expired outcomes first
            $user->cleanupExpiredOutcomes();
            
            UserOutcome::create([
                'user_id' => $user->id,
                'outcome_id' => $activeTask->potentialReward->id,
                'outcome_type' => 'reward',
                'task_id' => $activeTask->task->id,
                'user_assigned_task_id' => $activeTask->id,
                'status' => 'active',
                'assigned_at' => now(),
                'expires_at' => $this->calculateRewardExpiry($activeTask->potentialReward),
            ]);

            // Log combined completion + reward activity
            TaskActivity::log(
                type: TaskActivityType::Completed,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Completed task: {$activeTask->task->title}",
                description: "Great job! You completed the task successfully and earned a reward: {$activeTask->potentialReward->title}. {$activeTask->potentialReward->description}"
            );
        } else {
            // Log completion activity without reward
            TaskActivity::log(
                type: TaskActivityType::Completed,
                user: $user,
                task: $activeTask->task,
                assignedTask: $activeTask,
                title: "Completed task: {$activeTask->task->title}",
                description: "Great job! You completed the task successfully."
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