<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Status>
 */
class StatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(2),
            'is_public' => $this->faker->boolean(80), // 80% chance of being public
        ];
    }

    /**
     * Indicate that the status is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate that the status is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }

    /**
     * Create a status with specific content length.
     */
    public function withLength(int $length): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->text($length),
        ]);
    }

    /**
     * Create a status with maximum allowed length.
     */
    public function maxLength(): static
    {
        $maxLength = Status::getMaxLength();
        return $this->state(fn (array $attributes) => [
            'content' => $this->faker->text($maxLength),
        ]);
    }
}