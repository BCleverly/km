<?php

declare(strict_types=1);

namespace App\Actions\Tasks;

use App\ContentStatus;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCustomTask
{
    use AsAction;

    public function handle(
        User $user,
        string $title,
        string $description,
        int $difficultyLevel,
        int $durationTime,
        string $durationType,
        TargetUserType $targetUserType,
        bool $isPremium = false,
        ?string $rewardTitle = null,
        ?string $rewardDescription = null,
        ?int $rewardDifficultyLevel = null,
        ?string $punishmentTitle = null,
        ?string $punishmentDescription = null,
        ?int $punishmentDifficultyLevel = null,
    ): array {
        // Validate inputs
        $this->validateInputs($title, $description, $difficultyLevel, $durationTime, $durationType);

        return DB::transaction(function () use (
            $user,
            $title,
            $description,
            $difficultyLevel,
            $durationTime,
            $durationType,
            $targetUserType,
            $isPremium,
            $rewardTitle,
            $rewardDescription,
            $rewardDifficultyLevel,
            $punishmentTitle,
            $punishmentDescription,
            $punishmentDifficultyLevel,
        ) {
            // Create the task
            $task = Task::create([
                'title' => $title,
                'description' => $description,
                'difficulty_level' => $difficultyLevel,
                'duration_time' => $durationTime,
                'duration_type' => $durationType,
                'target_user_type' => $targetUserType,
                'user_id' => $user->id,
                'status' => ContentStatus::Pending, // Custom tasks need approval
                'is_premium' => $isPremium,
            ]);

            $createdItems = ['task' => $task];

            // Create reward if provided
            if ($rewardTitle && $rewardDescription) {
                $reward = TaskReward::create([
                    'title' => $rewardTitle,
                    'description' => $rewardDescription,
                    'difficulty_level' => $rewardDifficultyLevel ?? $difficultyLevel,
                    'target_user_type' => $targetUserType,
                    'user_id' => $user->id,
                    'status' => ContentStatus::Pending,
                    'is_premium' => $isPremium,
                ]);

                $createdItems['reward'] = $reward;

                // Link reward to task
                $task->recommendedRewards()->attach($reward->id, ['sort_order' => 1]);
            }

            // Create punishment if provided
            if ($punishmentTitle && $punishmentDescription) {
                $punishment = TaskPunishment::create([
                    'title' => $punishmentTitle,
                    'description' => $punishmentDescription,
                    'difficulty_level' => $punishmentDifficultyLevel ?? $difficultyLevel,
                    'target_user_type' => $targetUserType,
                    'user_id' => $user->id,
                    'status' => ContentStatus::Pending,
                    'is_premium' => $isPremium,
                ]);

                $createdItems['punishment'] = $punishment;

                // Link punishment to task
                $task->recommendedPunishments()->attach($punishment->id, ['sort_order' => 1]);
            }

            return $createdItems;
        });
    }

    private function validateInputs(
        string $title,
        string $description,
        int $difficultyLevel,
        int $durationTime,
        string $durationType,
    ): void {
        if (empty(trim($title))) {
            throw ValidationException::withMessages([
                'title' => 'Task title is required.',
            ]);
        }

        if (empty(trim($description))) {
            throw ValidationException::withMessages([
                'description' => 'Task description is required.',
            ]);
        }

        if ($difficultyLevel < 1 || $difficultyLevel > 5) {
            throw ValidationException::withMessages([
                'difficulty_level' => 'Difficulty level must be between 1 and 5.',
            ]);
        }

        if ($durationTime < 1) {
            throw ValidationException::withMessages([
                'duration_time' => 'Duration time must be at least 1.',
            ]);
        }

        if (!in_array($durationType, ['minutes', 'hours', 'days', 'weeks'])) {
            throw ValidationException::withMessages([
                'duration_type' => 'Duration type must be one of: minutes, hours, days, weeks.',
            ]);
        }
    }
}