<?php

namespace Database\Factories;

use App\Models\Tasks\Task;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks\UserAssignedTask>
 */
class UserAssignedTaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Tasks\UserAssignedTask::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task_id' => Task::factory(),
            'status' => TaskStatus::Assigned,
            'outcome_type' => null,
            'outcome_id' => null,
            'potential_reward_id' => TaskReward::factory(),
            'potential_punishment_id' => TaskPunishment::factory(),
            'assigned_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'completed_at' => null,
        ];
    }

    /**
     * Create an assigned task
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Assigned,
            'completed_at' => null,
        ]);
    }

    /**
     * Create a completed task
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed,
            'outcome_type' => 'reward',
            'outcome_id' => $attributes['potential_reward_id'],
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a failed task
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Failed,
            'outcome_type' => 'punishment',
            'outcome_id' => $attributes['potential_punishment_id'],
            'completed_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
