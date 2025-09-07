<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tasks\TaskActivity;
use App\Models\Tasks\UserAssignedTask;
use App\Models\UserOutcome;
use App\Notifications\TaskFailedDueToDeadline;
use App\TaskActivityType;
use App\TaskStatus;
use Illuminate\Console\Command;

class CheckTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for tasks that have passed their deadline and automatically fail them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for overdue tasks...');

        // Get all overdue tasks
        $overdueTasks = UserAssignedTask::overdue()
            ->with(['user', 'task', 'potentialPunishment'])
            ->get();

        if ($overdueTasks->isEmpty()) {
            $this->info('No overdue tasks found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$overdueTasks->count()} overdue task(s).");

        $failedCount = 0;

        foreach ($overdueTasks as $assignedTask) {
            $this->processOverdueTask($assignedTask);
            $failedCount++;
        }

        $this->info("Successfully processed {$failedCount} overdue task(s).");

        return Command::SUCCESS;
    }

    /**
     * Process a single overdue task.
     */
    private function processOverdueTask(UserAssignedTask $assignedTask): void
    {
        $user = $assignedTask->user;
        $task = $assignedTask->task;
        $punishment = $assignedTask->potentialPunishment;

        $this->line("Processing overdue task: {$task->title} for user: {$user->name}");

        // Update task status to failed
        $assignedTask->update([
            'status' => TaskStatus::Failed,
            'completed_at' => now(),
            'outcome_type' => 'punishment',
            'outcome_id' => $punishment?->id,
        ]);

        // Log the failure activity
        TaskActivity::log(
            type: TaskActivityType::Failed,
            user: $user,
            task: $task,
            assignedTask: $assignedTask,
            title: "Task failed due to deadline: {$task->title}",
            description: "Task automatically failed due to missing the deadline of {$assignedTask->deadline_at->format('M j, Y g:i A')}."
        );

        // Create UserOutcome record for the punishment if it exists
        if ($punishment) {
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
                'outcome_id' => $punishment->id,
                'task_id' => $task->id,
                'user_assigned_task_id' => $assignedTask->id,
                'status' => 'active',
                'assigned_at' => now(),
                'expires_at' => $this->calculatePunishmentExpiry($punishment),
            ]);

            // Log punishment received activity
            TaskActivity::log(
                type: TaskActivityType::PunishmentReceived,
                user: $user,
                task: $task,
                assignedTask: $assignedTask,
                title: "Received punishment for missed deadline: {$task->title}",
                description: $punishment->description
            );
        }

        // Send notification to user
        $user->notify(new TaskFailedDueToDeadline($assignedTask));

        $this->line("✓ Task failed and user notified: {$user->name}");
    }

    /**
     * Calculate when a punishment should expire based on its difficulty level.
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
