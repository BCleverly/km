<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\UserAssignedTask;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCustomTask
{
    use AsAction;

    public function handle(
        User $user,
        Task $task,
        ?Outcome $reward = null,
        ?Outcome $punishment = null,
        bool $keepPrivate = true,
    ): UserAssignedTask {
        return DB::transaction(function () use ($user, $task, $reward, $punishment) {
            // Create the custom task assignment
            $assignedTask = UserAssignedTask::create([
                'user_id' => $user->id,
                'task_id' => $task->id,
                'status' => TaskStatus::Assigned,
                'potential_reward_id' => $reward?->id,
                'potential_punishment_id' => $punishment?->id,
                'assigned_at' => now(),
                'deadline_at' => $this->calculateDeadline($task),
            ]);

            return $assignedTask;
        });
    }

    private function calculateDeadline(Task $task): \DateTime
    {
        $duration = $task->duration_time;
        $type = $task->duration_type;

        return match ($type) {
            'minutes' => now()->addMinutes($duration),
            'hours' => now()->addHours($duration),
            'days' => now()->addDays($duration),
            'weeks' => now()->addWeeks($duration),
            default => now()->addHours($duration),
        };
    }
}