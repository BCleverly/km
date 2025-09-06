<?php

namespace Database\Factories;

use App\Models\User;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks\TaskReward>
 */
class TaskRewardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Tasks\TaskReward::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(2),
            'difficulty_level' => fake()->numberBetween(1, 10),
            'target_user_type' => fake()->randomElement(TargetUserType::cases()),
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'view_count' => fake()->numberBetween(0, 500),
            'is_premium' => fake()->boolean(15), // 15% chance of being premium
        ];
    }

    /**
     * Create a pending reward
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    /**
     * Create an approved reward
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Approved,
        ]);
    }

    /**
     * Create a premium reward
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    /**
     * Create a reward for a specific user type
     */
    public function forUserType(TargetUserType $userType): static
    {
        return $this->state(fn (array $attributes) => [
            'target_user_type' => $userType,
        ]);
    }
}
