<?php

declare(strict_types=1);

namespace App\Actions\Api\Tasks;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetTaskStats
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();
        $taskService = $user->tasks();

        return [
            'success' => true,
            'stats' => [
                'summary' => $taskService->getTaskSummary(),
                'streaks' => $taskService->getStreakStats(),
                'active_outcomes' => [
                    'count' => $user->getActiveOutcomeCount(),
                    'max_allowed' => $user->getMaxActiveOutcomes(),
                    'remaining_slots' => $user->getRemainingOutcomeSlots(),
                ],
                'daily_limits' => [
                    'max_tasks_per_day' => $user->getMaxTasksPerDay(),
                    'tasks_today' => $user->assignedTasks()
                        ->whereDate('created_at', today())
                        ->count(),
                    'has_reached_daily_limit' => $user->hasReachedDailyTaskLimit(),
                ],
            ],
        ];
    }
}