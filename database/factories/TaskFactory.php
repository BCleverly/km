<?php

namespace Database\Factories;

use App\Models\User;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Tasks\Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $durationTypes = ['minutes', 'hours', 'days', 'weeks'];
        $durationType = fake()->randomElement($durationTypes);
        
        // Set reasonable duration times based on type
        $durationTime = match ($durationType) {
            'minutes' => fake()->numberBetween(15, 120), // 15 minutes to 2 hours
            'hours' => fake()->numberBetween(1, 48),     // 1 hour to 2 days
            'days' => fake()->numberBetween(1, 7),       // 1 to 7 days
            'weeks' => fake()->numberBetween(1, 4),      // 1 to 4 weeks
            default => fake()->numberBetween(1, 24),     // Default to hours
        };

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'difficulty_level' => fake()->numberBetween(1, 10),
            'duration_time' => $durationTime,
            'duration_type' => $durationType,
            'target_user_type' => fake()->randomElement(TargetUserType::cases()),
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'view_count' => fake()->numberBetween(0, 1000),
            'is_premium' => fake()->boolean(20), // 20% chance of being premium
        ];
    }

    /**
     * Create a pending task
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    /**
     * Create an approved task
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Approved,
        ]);
    }

    /**
     * Create a premium task
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    /**
     * Create a task for a specific user type
     */
    public function forUserType(TargetUserType $userType): static
    {
        return $this->state(fn (array $attributes) => [
            'target_user_type' => $userType,
        ]);
    }

    /**
     * Create a quick task (minutes)
     */
    public function quick(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_time' => fake()->numberBetween(15, 60),
            'duration_type' => 'minutes',
        ]);
    }

    /**
     * Create a short task (hours)
     */
    public function short(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_time' => fake()->numberBetween(1, 12),
            'duration_type' => 'hours',
        ]);
    }

    /**
     * Create a long task (days)
     */
    public function long(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_time' => fake()->numberBetween(3, 14),
            'duration_type' => 'days',
        ]);
    }

    /**
     * Create a very long task (weeks)
     */
    public function veryLong(): static
    {
        return $this->state(fn (array $attributes) => [
            'duration_time' => fake()->numberBetween(1, 4),
            'duration_type' => 'weeks',
        ]);
    }
}
