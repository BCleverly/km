<?php

namespace Database\Factories;

use App\Models\User;
use App\ContentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fantasy>
 */
class FantasyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Fantasy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraphs(3, true);
        
        return [
            'content' => $content,
            'word_count' => str_word_count($content),
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'view_count' => fake()->numberBetween(0, 1000),
            'report_count' => fake()->numberBetween(0, 5),
            'is_premium' => fake()->boolean(20), // 20% chance of being premium
        ];
    }

    /**
     * Create a pending fantasy
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    /**
     * Create an approved fantasy
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Approved,
        ]);
    }

    /**
     * Create a rejected fantasy
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Rejected,
        ]);
    }

    /**
     * Create a premium fantasy
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    /**
     * Create a short fantasy (under 100 words)
     */
    public function short(): static
    {
        $content = fake()->paragraph(1);
        
        return $this->state(fn (array $attributes) => [
            'content' => $content,
            'word_count' => str_word_count($content),
        ]);
    }

    /**
     * Create a long fantasy (close to 280 words)
     */
    public function long(): static
    {
        $content = fake()->paragraphs(5, true);
        
        return $this->state(fn (array $attributes) => [
            'content' => $content,
            'word_count' => str_word_count($content),
        ]);
    }
}