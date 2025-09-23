<?php

namespace Database\Factories;

use App\ContentStatus;
use App\Enums\DesireItemType;
use App\Models\DesireCategory;
use App\Models\DesireItem;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DesireItem>
 */
class DesireItemFactory extends Factory
{
    protected $model = DesireItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $itemType = fake()->randomElement(DesireItemType::cases());

        return [
            'title' => $this->generateTitle($itemType),
            'description' => $this->generateDescription($itemType),
            'item_type' => $itemType,
            'category_id' => DesireCategory::where('item_type', $itemType)->inRandomOrder()->first()?->id,
            'target_user_type' => fake()->randomElement(TargetUserType::cases()),
            'user_id' => User::factory(),
            'status' => ContentStatus::Approved,
            'view_count' => $this->faker->numberBetween(0, 1000),
            'is_premium' => $this->faker->boolean(20), // 20% chance of being premium
            'difficulty_level' => $this->faker->numberBetween(1, 10),
            'tags' => $this->generateTags($itemType),
        ];
    }

    private function generateTitle(DesireItemType $itemType): string
    {
        $titles = match ($itemType) {
            DesireItemType::Fetish => [
                'Blindfolded Sensory Play',
                'Light Bondage with Rope',
                'Feather Tickling',
                'Ice Cube Play',
                'Sensual Massage',
                'Body Worship',
                'Clothing Fetish',
                'Foot Worship',
                'Sensory Deprivation',
                'Temperature Play',
            ],
            DesireItemType::Fantasy => [
                'Beach Vacation Fantasy',
                'Office Romance Scenario',
                'Stranger in a Bar',
                'Teacher-Student Roleplay',
                'Doctor-Patient Fantasy',
                'Celebrity Encounter',
                'Time Travel Romance',
                'Superhero Fantasy',
                'Royal Court Intrigue',
                'Space Adventure Romance',
            ],
            DesireItemType::Kink => [
                'Light Spanking',
                'Hair Pulling',
                'Biting and Nibbling',
                'Choking (Light)',
                'Power Exchange',
                'Dominance Play',
                'Submission Scenarios',
                'Edge Play',
                'Consensual Non-Consent',
                'Sensory Overload',
            ],
            DesireItemType::Toy => [
                'Vibrating Massager',
                'Silk Blindfold',
                'Feather Tickler',
                'Ice Cubes',
                'Massage Oil',
                'Silk Rope',
                'Paddle',
                'Flogger',
                'Candles (Wax Play)',
                'Restraints',
            ],
            DesireItemType::Activity => [
                'Cooking Together Naked',
                'Dancing in the Living Room',
                'Reading Erotica Aloud',
                'Watching Adult Movies',
                'Playing Truth or Dare',
                'Strip Poker',
                'Massage Exchange',
                'Bubble Bath Together',
                'Stargazing',
                'Picnic in Bed',
            ],
            DesireItemType::Roleplay => [
                'Boss and Employee',
                'Doctor and Patient',
                'Teacher and Student',
                'Strangers Meeting',
                'Celebrity and Fan',
                'Royalty and Commoner',
                'Superhero and Civilian',
                'Detective and Suspect',
                'Pilot and Passenger',
                'Chef and Food Critic',
            ],
        };

        return fake()->randomElement($titles);
    }

    private function generateDescription(DesireItemType $itemType): string
    {
        $descriptions = match ($itemType) {
            DesireItemType::Fetish => [
                'Explore the heightened sensations that come from focusing on specific body parts or activities.',
                'A gentle introduction to sensory play that can be incredibly intimate and arousing.',
                'Discover new ways to experience pleasure through focused attention and exploration.',
                'Perfect for couples looking to add variety and excitement to their intimate moments.',
            ],
            DesireItemType::Fantasy => [
                'Act out your deepest desires in a safe, consensual environment with your partner.',
                'Escape reality together and explore different personas and scenarios.',
                'A creative way to explore new dynamics and keep your relationship exciting.',
                'Let your imagination run wild while staying connected with your partner.',
            ],
            DesireItemType::Kink => [
                'Explore power dynamics and consensual play in a safe, trusting environment.',
                'Add intensity and excitement to your intimate moments with controlled play.',
                'Discover new sensations and experiences through consensual kink activities.',
                'Build trust and communication while exploring your boundaries together.',
            ],
            DesireItemType::Toy => [
                'Enhance your intimate experiences with carefully selected toys and accessories.',
                'Add new sensations and variety to your playtime together.',
                'Explore different textures, temperatures, and sensations in a fun way.',
                'Perfect for couples looking to experiment with new forms of pleasure.',
            ],
            DesireItemType::Activity => [
                'Connect with your partner through fun, intimate activities that build closeness.',
                'Create special moments and memories while exploring your relationship.',
                'A great way to spend quality time together while being playful and intimate.',
                'Build intimacy through shared experiences and mutual exploration.',
            ],
            DesireItemType::Roleplay => [
                'Step into different roles and explore new dynamics with your partner.',
                'Act out scenarios that excite you both in a safe, consensual way.',
                'Discover new aspects of your relationship through creative roleplay.',
                'Have fun exploring different personas and situations together.',
            ],
        };

        return fake()->randomElement($descriptions);
    }

    private function generateTags(DesireItemType $itemType): array
    {
        $tagOptions = match ($itemType) {
            DesireItemType::Fetish => ['sensory', 'intimate', 'exploration', 'trust', 'connection'],
            DesireItemType::Fantasy => ['roleplay', 'imagination', 'escape', 'adventure', 'romance'],
            DesireItemType::Kink => ['power', 'control', 'intensity', 'boundaries', 'communication'],
            DesireItemType::Toy => ['enhancement', 'variety', 'sensation', 'play', 'experiment'],
            DesireItemType::Activity => ['fun', 'intimate', 'connection', 'quality-time', 'playful'],
            DesireItemType::Roleplay => ['acting', 'scenarios', 'personas', 'creative', 'dynamic'],
        };

        return fake()->randomElements($tagOptions, $this->faker->numberBetween(1, 3));
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Pending,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }

    public function forUserType(TargetUserType $userType): static
    {
        return $this->state(fn (array $attributes) => [
            'target_user_type' => $userType,
        ]);
    }

    public function forItemType(DesireItemType $itemType): static
    {
        return $this->state(fn (array $attributes) => [
            'item_type' => $itemType,
            'category_id' => DesireCategory::where('item_type', $itemType)->inRandomOrder()->first()?->id,
        ]);
    }
}
