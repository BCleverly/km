<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskActivity;
use App\TaskStatus;
use App\ContentStatus;
use App\TargetUserType;
use App\TaskActivityType;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignRandomTask
{
    use AsAction;

    public function handle(User $user): array
    {
        // Check if user already has an active task
        $existingActiveTask = UserAssignedTask::where('user_id', $user->id)
            ->where('status', TaskStatus::Assigned)
            ->first();
            
        if ($existingActiveTask) {
            return [
                'success' => false,
                'message' => 'You already have an active task. Complete or fail your current task before getting a new one.',
                'task' => null,
            ];
        }

        // Get a random task suitable for the user
        $randomTask = $this->getRandomTaskForUser($user);
        
        if (!$randomTask) {
            return [
                'success' => false,
                'message' => 'No available tasks found. Try again later or create your own!',
                'task' => null,
            ];
        }

        // Get random reward and punishment for this task
        $reward = $this->getRandomOutcomeForUser($user, 'reward');
        $punishment = $this->getRandomOutcomeForUser($user, 'punishment');

        // Create the assigned task
        $assignedTask = UserAssignedTask::create([
            'user_id' => $user->id,
            'task_id' => $randomTask->id,
            'status' => TaskStatus::Assigned,
            'potential_reward_id' => $reward?->id,
            'potential_punishment_id' => $punishment?->id,
            'assigned_at' => now(),
        ]);

        // Log the activity
        TaskActivity::log(
            type: TaskActivityType::Assigned,
            user: $user,
            task: $randomTask,
            title: "Assigned random task: {$randomTask->title}",
            description: "You were assigned a random task with difficulty level {$randomTask->difficulty_level}."
        );

        return [
            'success' => true,
            'message' => "New task assigned: {$randomTask->title}",
            'task' => $assignedTask->load(['task', 'potentialReward', 'potentialPunishment']),
        ];
    }

    private function getRandomTaskForUser(User $user): ?Task
    {
        $userType = $user->profile?->user_type ?? 'any';
        
        return Task::where('status', ContentStatus::Approved)
            ->where(function ($query) use ($userType) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $userType);
            })
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('task_id')
                      ->from('user_assigned_tasks')
                      ->where('user_id', $user->id); // Exclude ALL previously assigned tasks
            })
            ->inRandomOrder()
            ->first();
    }

    private function getRandomOutcomeForUser(User $user, string $intendedType): ?Outcome
    {
        $userType = $user->profile?->user_type ?? 'any';
        
        return Outcome::where('status', ContentStatus::Approved)
            ->where('intended_type', $intendedType)
            ->where(function ($query) use ($userType) {
                $query->where('target_user_type', TargetUserType::Any)
                      ->orWhere('target_user_type', $userType);
            })
            ->inRandomOrder()
            ->first();
    }
}