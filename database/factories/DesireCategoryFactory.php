<?php

namespace Database\Factories;

use App\Enums\DesireItemType;
use App\Models\DesireCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesireCategory>
 */
class DesireCategoryFactory extends Factory
{
    protected $model = DesireCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'item_type' => fake()->randomElement(DesireItemType::cases()),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
        ];
    }

    public function forItemType(DesireItemType $itemType): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => $itemType,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
