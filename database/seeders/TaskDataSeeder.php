<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Models\Tag;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TaskDataSeeder extends Seeder
{
    /**
     * Seed the application's database with task data from JSON files.
     */
    public function run(): void
    {
        $this->command->info('Starting TaskDataSeeder...');

        // Get a system user to assign as the author of imported content
        $systemUser = $this->getSystemUser();

        // Import outcomes (rewards and punishments)
        $outcomes = $this->importOutcomes($systemUser);
        $this->command->info('Imported ' . count($outcomes) . ' outcomes');

        // Import tasks with their relationships
        $tasks = $this->importTasks($systemUser, $outcomes);
        $this->command->info('Imported ' . count($tasks) . ' tasks');

        $this->command->info('TaskDataSeeder completed successfully!');
    }

    /**
     * Get or create a system user for imported content
     */
    private function getSystemUser(): User
    {
        $user = User::where('email', 'system@kinkmaster.com')->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'System User',
                'email' => 'system@kinkmaster.com',
                'password' => bcrypt('password'),
            ]);
            
            // Set email_verified_at after creation
            $user->email_verified_at = now();
            $user->save();
        }
        
        return $user;
    }

    /**
     * Import outcomes (rewards and punishments) from JSON files
     */
    private function importOutcomes(User $systemUser): array
    {
        $outcomes = [];

        // Import rewards
        $rewardsData = $this->loadJsonData('rewards.json');
        foreach ($rewardsData as $rewardData) {
            $outcome = Outcome::create([
                'title' => $rewardData['title'],
                'description' => $rewardData['description'],
                'difficulty_level' => $rewardData['difficulty_level'],
                'target_user_type' => TargetUserType::Any, // Default to Any for imported data
                'user_id' => $systemUser->id,
                'status' => ContentStatus::Approved, // Imported data should be approved
                'view_count' => 0,
                'is_premium' => false,
                'intended_type' => 'reward',
            ]);

            // Attach tags if they exist
            if (!empty($rewardData['tags'])) {
                $this->attachTagsToOutcome($outcome, $rewardData['tags'], $systemUser);
            }

            $outcomes['reward_' . $rewardData['id']] = $outcome;
        }

        // Import punishments
        $punishmentsData = $this->loadJsonData('punishments.json');
        foreach ($punishmentsData as $punishmentData) {
            $outcome = Outcome::create([
                'title' => $punishmentData['title'],
                'description' => $punishmentData['description'],
                'difficulty_level' => $punishmentData['difficulty_level'],
                'target_user_type' => TargetUserType::Any, // Default to Any for imported data
                'user_id' => $systemUser->id,
                'status' => ContentStatus::Approved, // Imported data should be approved
                'view_count' => 0,
                'is_premium' => false,
                'intended_type' => 'punishment',
            ]);

            // Attach tags if they exist
            if (!empty($punishmentData['tags'])) {
                $this->attachTagsToOutcome($outcome, $punishmentData['tags'], $systemUser);
            }

            $outcomes['punishment_' . $punishmentData['id']] = $outcome;
        }

        return $outcomes;
    }

    /**
     * Import tasks from JSON file with their relationships
     */
    private function importTasks(User $systemUser, array $outcomes): array
    {
        $tasksData = $this->loadJsonData('tasks.json');
        $tasks = [];

        foreach ($tasksData as $index => $taskData) {
            // Parse duration from the task data
            $durationInfo = $this->parseDuration($taskData['duration']);

            // Create the task
            $task = Task::create([
                'title' => $taskData['task'],
                'description' => $this->generateTaskDescription($taskData),
                'difficulty_level' => $this->calculateDifficultyFromDuration($taskData['duration']),
                'duration_time' => $durationInfo['time'],
                'duration_type' => $durationInfo['type'],
                'target_user_type' => TargetUserType::Any, // Default to Any for imported data
                'user_id' => $systemUser->id,
                'status' => ContentStatus::Approved, // Imported data should be approved
                'view_count' => 0,
                'is_premium' => false,
            ]);

            // Attach recommended rewards if they exist
            if (!empty($taskData['recommended_rewards'])) {
                $rewardIds = collect($taskData['recommended_rewards'])
                    ->map(fn($rewardId) => $outcomes['reward_' . $rewardId] ?? null)
                    ->filter()
                    ->map(fn($outcome) => $outcome->id)
                    ->toArray();

                if (!empty($rewardIds)) {
                    $task->recommendedRewards()->attach($rewardIds);
                }
            }

            // Attach recommended punishments if they exist
            if (!empty($taskData['recommended_punishments'])) {
                $punishmentIds = collect($taskData['recommended_punishments'])
                    ->map(fn($punishmentId) => $outcomes['punishment_' . $punishmentId] ?? null)
                    ->filter()
                    ->map(fn($outcome) => $outcome->id)
                    ->toArray();

                if (!empty($punishmentIds)) {
                    $task->recommendedPunishments()->attach($punishmentIds);
                }
            }

            // Attach tags if they exist
            if (!empty($taskData['tags'])) {
                $this->attachTagsToTask($task, $taskData['tags'], $systemUser);
            }

            $tasks[] = $task;
        }

        return $tasks;
    }

    /**
     * Load JSON data from the data directory
     */
    private function loadJsonData(string $filename): array
    {
        $filePath = database_path("data/{$filename}");
        
        if (!File::exists($filePath)) {
            $this->command->error("File not found: {$filePath}");
            return [];
        }

        $content = File::get($filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Invalid JSON in {$filename}: " . json_last_error_msg());
            return [];
        }

        return $data;
    }

    /**
     * Generate a description for the task based on the data
     */
    private function generateTaskDescription(array $taskData): string
    {
        $description = $taskData['task'];
        
        if (!empty($taskData['tip'])) {
            $description .= "\n\nTips: " . implode(', ', $taskData['tip']);
        }

        $description .= "\n\nDuration: {$taskData['duration']} day(s)";

        return $description;
    }

    /**
     * Parse duration string into time and type
     */
    private function parseDuration(int $duration): array
    {
        // The original data uses days, so we'll convert to appropriate units
        return match (true) {
            $duration <= 1 => ['time' => 24, 'type' => 'hours'],      // 1 day = 24 hours
            $duration <= 3 => ['time' => $duration, 'type' => 'days'], // 2-3 days
            $duration <= 7 => ['time' => $duration, 'type' => 'days'], // 4-7 days
            default => ['time' => ceil($duration / 7), 'type' => 'weeks'], // Convert to weeks
        };
    }

    /**
     * Calculate difficulty level based on duration
     */
    private function calculateDifficultyFromDuration(int $duration): int
    {
        // Map duration to difficulty level (1-10 scale)
        return match (true) {
            $duration <= 1 => 3,  // Easy
            $duration <= 3 => 5,  // Medium
            $duration <= 7 => 7,  // Hard
            default => 9,         // Very Hard
        };
    }

    /**
     * Attach tags to a task from the JSON data
     */
    private function attachTagsToTask(Task $task, array $tagsData, User $systemUser): void
    {
        $tagIds = [];

        foreach ($tagsData as $typeKey => $tagsOfType) {
            if (is_array($tagsOfType)) {
                foreach ($tagsOfType as $tagData) {
                    if (isset($tagData) && !empty(trim($tagData))) {
                        $tag = $this->findOrCreateTag($tagData, $typeKey, $systemUser);
                        if ($tag) {
                            $tagIds[] = $tag->id;
                        }
                    }
                }
            }
        }

        if (!empty($tagIds)) {
            $task->syncTags($tagIds);
        }
    }

    /**
     * Attach tags to an outcome from the JSON data
     */
    private function attachTagsToOutcome(Outcome $outcome, array $tagsData, User $systemUser): void
    {
        $tagIds = [];

        foreach ($tagsData as $typeKey => $tagsOfType) {
            if (is_array($tagsOfType)) {
                foreach ($tagsOfType as $tagData) {
                    if (isset($tagData) && !empty(trim($tagData))) {
                        $tag = $this->findOrCreateTag($tagData, $typeKey, $systemUser);
                        if ($tag) {
                            $tagIds[] = $tag->id;
                        }
                    }
                }
            }
        }

        if (!empty($tagIds)) {
            $outcome->syncTags($tagIds);
        }
    }

    /**
     * Find or create a tag with the given name and type
     */
    private function findOrCreateTag(string $name, string $type, User $systemUser): ?Tag
    {
        // First, try to find an existing tag with this name and type
        $tag = Tag::where('name', $name)
            ->where('type', $type)
            ->first();

        if (!$tag) {
            // Create a new tag
            $tag = Tag::create([
                'name' => $name,
                'type' => $type,
                'status' => ContentStatus::Approved, // Imported tags should be approved
                'created_by' => $systemUser->id,
                'approved_by' => $systemUser->id,
                'approved_at' => now(),
            ]);
        }

        return $tag;
    }
}