<?php

namespace Database\Factories;

use App\Models\AffiliateLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AffiliateLink>
 */
class AffiliateLinkFactory extends Factory
{
    protected $model = AffiliateLink::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $partnerTypes = array_keys(AffiliateLink::getPartnerTypes());
        $commissionTypes = array_keys(AffiliateLink::getCommissionTypes());
        
        $partnerType = fake()->randomElement($partnerTypes);
        $commissionType = fake()->randomElement($commissionTypes);
        
        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Store', 'Shop', 'Outlet', 'Marketplace']),
            'description' => fake()->sentence(10),
            'url' => fake()->url(),
            'partner_type' => $partnerType,
            'commission_type' => $commissionType,
            'commission_rate' => $commissionType === 'percentage' ? fake()->randomFloat(2, 1, 15) : null,
            'commission_fixed' => $commissionType === 'fixed' ? fake()->randomFloat(2, 1, 50) : null,
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'is_active' => fake()->boolean(80), // 80% chance of being active
            'is_premium' => fake()->boolean(20), // 20% chance of being premium
            'tracking_id' => fake()->optional(0.7)->bothify('TRK-####-????'),
            'notes' => fake()->optional(0.5)->sentence(15),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the affiliate link is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the affiliate link is premium.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    /**
     * Indicate that the affiliate link is for sex toys.
     */
    public function toys(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_type' => 'toys',
            'name' => fake()->randomElement(['Lovehoney', 'Adam & Eve', 'PinkCherry', 'SheVibe', 'Babeland']) . ' ' . fake()->randomElement(['Store', 'Shop', 'Outlet']),
        ]);
    }

    /**
     * Indicate that the affiliate link is for clothing/lingerie.
     */
    public function clothing(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_type' => 'clothing',
            'name' => fake()->randomElement(['Victoria\'s Secret', 'Agent Provocateur', 'Honey Birdette', 'Savage X Fenty']) . ' ' . fake()->randomElement(['Store', 'Boutique', 'Shop']),
        ]);
    }
}