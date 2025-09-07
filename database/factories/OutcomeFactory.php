<?php

namespace Database\Factories;

use App\Models\Tasks\Outcome;
use App\Models\User;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks\Outcome>
 */
class OutcomeFactory extends Factory
{
    protected $model = Outcome::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'difficulty_level' => $this->faker->numberBetween(1, 10),
            'target_user_type' => $this->faker->randomElement(TargetUserType::cases()),
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'view_count' => $this->faker->numberBetween(0, 1000),
            'is_premium' => $this->faker->boolean(20), // 20% chance of being premium
            'intended_type' => $this->faker->randomElement(['reward', 'punishment']),
        ];
    }

    /**
     * Indicate that the outcome is intended as a reward.
     */
    public function reward(): static
    {
        return $this->state(fn (array $attributes) => [
            'intended_type' => 'reward',
        ]);
    }

    /**
     * Indicate that the outcome is intended as a punishment.
     */
    public function punishment(): static
    {
        return $this->state(fn (array $attributes) => [
            'intended_type' => 'punishment',
        ]);
    }

    /**
     * Indicate that the outcome is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    /**
     * Indicate that the outcome is premium.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    /**
     * Indicate that the outcome is for a specific user type.
     */
    public function forUserType(TargetUserType $userType): static
    {
        return $this->state(fn (array $attributes) => [
            'target_user_type' => $userType,
        ]);
    }

    /**
     * Indicate that the outcome has a specific difficulty level.
     */
    public function difficulty(int $level): static
    {
        return $this->state(fn (array $attributes) => [
            'difficulty_level' => $level,
        ]);
    }
}