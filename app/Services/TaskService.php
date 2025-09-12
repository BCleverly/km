<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Tasks\UserAssignedTask;
use App\TaskStatus;
use Carbon\Carbon;

/**
 * Service class for task-related operations and statistics.
 * 
 * This service provides methods for managing user tasks, calculating statistics,
 * and handling task-related business logic.
 */
class TaskService
{
    public function __construct(
        private User $user
    ) {}

    /**
     * Get the user's current active task
     */
    public function getActiveTask(): ?UserAssignedTask
    {
        return $this->user->assignedTasks()
            ->where('status', TaskStatus::Assigned)
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->first();
    }

    /**
     * Get the user's longest streak of daily task completions
     */
    public function getLongestStreak(): int
    {
        $completedTasks = $this->user->assignedTasks()
            ->where('status', TaskStatus::Completed)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->get(['completed_at']);

        if ($completedTasks->isEmpty()) {
            return 0;
        }

        // Group tasks by completion date to ensure each day is counted once
        $tasksByDate = $completedTasks->groupBy(function ($task) {
            return $task->completed_at->toDateString();
        });

        $uniqueDates = $tasksByDate->keys()->sort()->values();

        if ($uniqueDates->isEmpty()) {
            return 0;
        }

        $streaks = [];
        $currentStreak = 1;
        $previousDate = null;

        foreach ($uniqueDates as $currentDate) {
            if ($previousDate === null) {
                $previousDate = $currentDate;
                continue;
            }

            $daysDifference = abs(Carbon::parse($currentDate)->diffInDays(Carbon::parse($previousDate)));

            if ($daysDifference === 1) {
                // Consecutive day - continue streak
                $currentStreak++;
            } elseif ($daysDifference === 0) {
                // Same day - don't break streak, but don't increment (already handled by uniqueDates)
                continue;
            } else {
                // Gap in days - end current streak and start new one
                $streaks[] = $currentStreak;
                $currentStreak = 1;
            }

            $previousDate = $currentDate;
        }

        // Add the final streak
        $streaks[] = $currentStreak;

        return empty($streaks) ? 0 : max($streaks);
    }

    /**
     * Get the user's current active streak of daily task completions
     */
    public function getCurrentStreak(): int
    {
        $completedTasks = $this->user->assignedTasks()
            ->where('status', TaskStatus::Completed)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->get(['completed_at']);

        if ($completedTasks->isEmpty()) {
            return 0;
        }

        // Group tasks by completion date
        $tasksByDate = $completedTasks->groupBy(function ($task) {
            return $task->completed_at->toDateString();
        });

        $uniqueDates = $tasksByDate->keys()->sort()->values();

        if ($uniqueDates->isEmpty()) {
            return 0;
        }

        $currentStreak = 0;
        $today = now()->toDateString();
        $lastCompletedDate = $uniqueDates->last(); // Most recent completion date

        // Check if the last completion was today or yesterday (allowing for 1 day gap to continue streak)
        $daysSinceLastCompletion = Carbon::parse($lastCompletedDate)->diffInDays(now());

        if ($daysSinceLastCompletion > 1) {
            // Streak is broken if more than 1 day gap
            return 0;
        }

        // Start counting from the most recent completion date backwards
        $expectedDate = $today;
        foreach ($uniqueDates->reverse() as $completionDate) { // Iterate in reverse chronological order
            if ($completionDate === $expectedDate) {
                $currentStreak++;
                $expectedDate = Carbon::parse($expectedDate)->subDay()->toDateString();
            } elseif (Carbon::parse($completionDate)->diffInDays(Carbon::parse($expectedDate)) === 0) {
                // This case should ideally not be hit if uniqueDates are truly unique and sorted,
                // but acts as a safeguard for same-day multiple completions if not grouped perfectly.
                // For unique dates, this means the date is not the expected one.
                break;
            } else {
                // Gap found - streak is broken
                break;
            }
        }

        return $currentStreak;
    }

    /**
     * Get streak statistics for the user
     */
    public function getStreakStats(): array
    {
        return [
            'current_streak' => $this->getCurrentStreak(),
            'longest_streak' => $this->getLongestStreak(),
            'total_completed_tasks' => $this->getTotalCompletedTasks(),
            'completion_rate' => $this->getCompletionRate(),
        ];
    }

    /**
     * Get the user's task completion rate (percentage)
     */
    public function getCompletionRate(): float
    {
        $totalTasks = $this->user->assignedTasks()->where('status', '!=', TaskStatus::Assigned)->count();
        
        if ($totalTasks === 0) {
            return 0.0;
        }

        $completedTasks = $this->user->assignedTasks()->where('status', TaskStatus::Completed)->count();
        
        return round(($completedTasks / $totalTasks) * 100, 1);
    }

    /**
     * Get total completed tasks count
     */
    public function getTotalCompletedTasks(): int
    {
        return $this->user->assignedTasks()->where('status', TaskStatus::Completed)->count();
    }

    /**
     * Get all assigned tasks for the user
     */
    public function getAssignedTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->user->assignedTasks()
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    /**
     * Get completed tasks for the user
     */
    public function getCompletedTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->user->assignedTasks()
            ->where('status', TaskStatus::Completed)
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    /**
     * Get failed tasks for the user
     */
    public function getFailedTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->user->assignedTasks()
            ->where('status', TaskStatus::Failed)
            ->with(['task', 'potentialReward', 'potentialPunishment'])
            ->orderBy('failed_at', 'desc')
            ->get();
    }

    /**
     * Check if user has an active task
     */
    public function hasActiveTask(): bool
    {
        return $this->getActiveTask() !== null;
    }

    /**
     * Get task statistics summary
     */
    public function getTaskSummary(): array
    {
        $assignedTasks = $this->user->assignedTasks();
        
        return [
            'total_assigned' => $assignedTasks->count(),
            'completed' => $assignedTasks->where('status', TaskStatus::Completed)->count(),
            'failed' => $assignedTasks->where('status', TaskStatus::Failed)->count(),
            'active' => $assignedTasks->where('status', TaskStatus::Assigned)->count(),
            'completion_rate' => $this->getCompletionRate(),
            'current_streak' => $this->getCurrentStreak(),
            'longest_streak' => $this->getLongestStreak(),
        ];
    }
}
