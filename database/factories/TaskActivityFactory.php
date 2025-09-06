<?php

namespace Database\Factories;

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskActivity;
use App\Models\User;
use App\Models\Tasks\UserAssignedTask;
use App\TaskActivityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks\TaskActivity>
 */
class TaskActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Tasks\TaskActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $activityType = fake()->randomElement(TaskActivityType::cases());
        
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'user_assigned_task_id' => null,
            'activity_type' => $activityType,
            'title' => $this->generateTitle($activityType),
            'description' => $this->generateDescription($activityType),
            'metadata' => $this->generateMetadata($activityType),
            'activity_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Create an activity for a specific user and task
     */
    public function forUserAndTask(User $user, Task $task): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'task_id' => $task->id,
        ]);
    }

    /**
     * Create an activity with a specific type
     */
    public function ofType(TaskActivityType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type' => $type,
            'title' => $this->generateTitle($type),
            'description' => $this->generateDescription($type),
            'metadata' => $this->generateMetadata($type),
        ]);
    }

    /**
     * Create an activity for an assigned task
     */
    public function forAssignedTask(UserAssignedTask $assignedTask): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $assignedTask->user_id,
            'task_id' => $assignedTask->task_id,
            'user_assigned_task_id' => $assignedTask->id,
        ]);
    }

    private function generateTitle(TaskActivityType $type): string
    {
        return match($type) {
            TaskActivityType::Assigned => 'Assigned task: ' . fake()->sentence(3),
            TaskActivityType::Completed => 'Completed task: ' . fake()->sentence(3),
            TaskActivityType::Failed => 'Failed task: ' . fake()->sentence(3),
            TaskActivityType::RewardReceived => 'Received reward for: ' . fake()->sentence(3),
            TaskActivityType::PunishmentReceived => 'Received punishment for: ' . fake()->sentence(3),
            TaskActivityType::TaskCreated => 'Created task: ' . fake()->sentence(3),
            TaskActivityType::TaskViewed => 'Viewed task: ' . fake()->sentence(3),
        };
    }

    private function generateDescription(TaskActivityType $type): ?string
    {
        return match($type) {
            TaskActivityType::Assigned => 'You were assigned a new task with difficulty level ' . fake()->numberBetween(1, 10) . '.',
            TaskActivityType::Completed => 'Great job! You completed the task successfully.',
            TaskActivityType::Failed => 'Unfortunately, you didn\'t complete the task in time.',
            TaskActivityType::RewardReceived => 'You received a reward for completing the task: ' . fake()->sentence(),
            TaskActivityType::PunishmentReceived => 'You received a punishment for not completing the task: ' . fake()->sentence(),
            TaskActivityType::TaskCreated => 'You created a new task for the community.',
            TaskActivityType::TaskViewed => 'You viewed the task details.',
        };
    }

    private function generateMetadata(TaskActivityType $type): ?array
    {
        return match($type) {
            TaskActivityType::Assigned => [
                'difficulty_level' => fake()->numberBetween(1, 10),
                'assigned_by' => 'system',
            ],
            TaskActivityType::Completed => [
                'completion_time' => fake()->numberBetween(1, 24) . ' hours',
                'rating' => fake()->numberBetween(1, 5),
            ],
            TaskActivityType::Failed => [
                'failure_reason' => fake()->randomElement(['timeout', 'abandoned', 'incomplete']),
            ],
            TaskActivityType::RewardReceived => [
                'reward_type' => fake()->randomElement(['physical', 'experience', 'privilege']),
                'value' => fake()->numberBetween(1, 10),
            ],
            TaskActivityType::PunishmentReceived => [
                'punishment_type' => fake()->randomElement(['restriction', 'chore', 'denial']),
                'severity' => fake()->numberBetween(1, 10),
            ],
            TaskActivityType::TaskCreated => [
                'category' => fake()->randomElement(['daily', 'weekly', 'special']),
                'target_audience' => fake()->randomElement(['male', 'female', 'couple', 'any']),
            ],
            TaskActivityType::TaskViewed => [
                'view_duration' => fake()->numberBetween(10, 300) . ' seconds',
                'source' => fake()->randomElement(['dashboard', 'search', 'recommendation']),
            ],
        };
    }
}
