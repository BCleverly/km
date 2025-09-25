<?php

namespace Database\Factories;

use App\Enums\DesireResponseType;
use App\Models\DesireItem;
use App\Models\PartnerDesireResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PartnerDesireResponse>
 */
class PartnerDesireResponseFactory extends Factory
{
    protected $model = PartnerDesireResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'partner_id' => User::factory(),
            'desire_item_id' => DesireItem::factory(),
            'response_type' => fake()->randomElement(DesireResponseType::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function yes(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_type' => DesireResponseType::Yes,
        ]);
    }

    public function no(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_type' => DesireResponseType::No,
        ]);
    }

    public function maybe(): static
    {
        return $this->state(fn (array $attributes) => [
            'response_type' => DesireResponseType::Maybe,
        ]);
    }
}
