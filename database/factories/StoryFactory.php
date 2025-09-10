<?php

namespace Database\Factories;

use App\Models\User;
use App\ContentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Story::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $content = fake()->paragraphs(8, true);
        $wordCount = str_word_count($content);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->paragraph(2),
            'content' => $content,
            'word_count' => $wordCount,
            'reading_time_minutes' => max(1, ceil($wordCount / 200)), // 200 words per minute
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'report_count' => fake()->numberBetween(0, 5),
        ];
    }

    /**
     * Create a pending story
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    /**
     * Create an approved story
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Approved,
        ]);
    }

    /**
     * Create a rejected story
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Rejected,
        ]);
    }

    /**
     * Create a draft story
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0, // Draft
        ]);
    }

    /**
     * Create a short story (around 100-200 words)
     */
    public function short(): static
    {
        $title = fake()->sentence(3);
        $content = fake()->paragraphs(2, true);
        $wordCount = str_word_count($content);
        
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'word_count' => $wordCount,
            'reading_time_minutes' => max(1, ceil($wordCount / 200)),
        ]);
    }

    /**
     * Create a long story (500+ words)
     */
    public function long(): static
    {
        $title = fake()->sentence(5);
        $content = fake()->paragraphs(12, true);
        $wordCount = str_word_count($content);
        
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'word_count' => $wordCount,
            'reading_time_minutes' => max(1, ceil($wordCount / 200)),
        ]);
    }

    /**
     * Create a story with a specific title
     */
    public function withTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => $title,
            'slug' => Str::slug($title),
        ]);
    }
}