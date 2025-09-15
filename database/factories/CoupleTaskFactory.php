<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoupleTask>
 */
class CoupleTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assigned_by' => \App\Models\User::factory(),
            'assigned_to' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'dom_message' => $this->faker->optional(0.7)->sentence(),
            'difficulty_level' => $this->faker->numberBetween(1, 10),
            'duration_hours' => $this->faker->numberBetween(1, 168),
            'status' => \App\Enums\CoupleTaskStatus::Pending,
            'assigned_at' => now(),
            'deadline_at' => now()->addHours($this->faker->numberBetween(1, 168)),
        ];
    }
}
