<?php

namespace Database\Seeders;

use App\Enums\DesireItemType;
use App\Models\DesireCategory;
use Illuminate\Database\Seeder;

class DesireCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Fetishes
            ['name' => 'Bondage & Restraint', 'description' => 'Various forms of physical restraint and bondage play', 'item_type' => DesireItemType::Fetish, 'sort_order' => 1],
            ['name' => 'Sensory Play', 'description' => 'Exploring different sensations and sensory experiences', 'item_type' => DesireItemType::Fetish, 'sort_order' => 2],
            ['name' => 'Power Exchange', 'description' => 'Dominance and submission dynamics', 'item_type' => DesireItemType::Fetish, 'sort_order' => 3],
            ['name' => 'Body Worship', 'description' => 'Adoration and worship of specific body parts', 'item_type' => DesireItemType::Fetish, 'sort_order' => 4],
            ['name' => 'Clothing & Lingerie', 'description' => 'Specific clothing, lingerie, or costume preferences', 'item_type' => DesireItemType::Fetish, 'sort_order' => 5],

            // Fantasies
            ['name' => 'Romantic Fantasies', 'description' => 'Intimate and romantic scenarios', 'item_type' => DesireItemType::Fantasy, 'sort_order' => 1],
            ['name' => 'Adventure Fantasies', 'description' => 'Exciting and adventurous scenarios', 'item_type' => DesireItemType::Fantasy, 'sort_order' => 2],
            ['name' => 'Power Fantasies', 'description' => 'Scenarios involving power dynamics', 'item_type' => DesireItemType::Fantasy, 'sort_order' => 3],
            ['name' => 'Public Fantasies', 'description' => 'Scenarios involving public or semi-public settings', 'item_type' => DesireItemType::Fantasy, 'sort_order' => 4],
            ['name' => 'Taboo Fantasies', 'description' => 'Exploring boundaries and taboo scenarios', 'item_type' => DesireItemType::Fantasy, 'sort_order' => 5],

            // Kinks
            ['name' => 'Light Kinks', 'description' => 'Mild and beginner-friendly kinks', 'item_type' => DesireItemType::Kink, 'sort_order' => 1],
            ['name' => 'Moderate Kinks', 'description' => 'Intermediate level kinks and activities', 'item_type' => DesireItemType::Kink, 'sort_order' => 2],
            ['name' => 'Advanced Kinks', 'description' => 'More intense and advanced kink activities', 'item_type' => DesireItemType::Kink, 'sort_order' => 3],
            ['name' => 'Edge Play', 'description' => 'High-risk, high-reward activities requiring experience', 'item_type' => DesireItemType::Kink, 'sort_order' => 4],

            // Toys
            ['name' => 'Vibrators & Massagers', 'description' => 'Various types of vibrators and massaging devices', 'item_type' => DesireItemType::Toy, 'sort_order' => 1],
            ['name' => 'Bondage Gear', 'description' => 'Restraints, ropes, and bondage equipment', 'item_type' => DesireItemType::Toy, 'sort_order' => 2],
            ['name' => 'Impact Toys', 'description' => 'Paddles, floggers, and other impact play tools', 'item_type' => DesireItemType::Toy, 'sort_order' => 3],
            ['name' => 'Sensory Toys', 'description' => 'Blindfolds, feathers, ice, and sensory play items', 'item_type' => DesireItemType::Toy, 'sort_order' => 4],
            ['name' => 'Penetration Toys', 'description' => 'Dildos, plugs, and penetration devices', 'item_type' => DesireItemType::Toy, 'sort_order' => 5],

            // Activities
            ['name' => 'Intimate Activities', 'description' => 'Close and intimate activities', 'item_type' => DesireItemType::Activity, 'sort_order' => 1],
            ['name' => 'Playful Activities', 'description' => 'Fun and playful activities', 'item_type' => DesireItemType::Activity, 'sort_order' => 2],
            ['name' => 'Challenging Activities', 'description' => 'More demanding and challenging activities', 'item_type' => DesireItemType::Activity, 'sort_order' => 3],
            ['name' => 'Social Activities', 'description' => 'Activities involving others or social settings', 'item_type' => DesireItemType::Activity, 'sort_order' => 4],

            // Roleplay
            ['name' => 'Character Roleplay', 'description' => 'Taking on different characters or personas', 'item_type' => DesireItemType::Roleplay, 'sort_order' => 1],
            ['name' => 'Scenario Roleplay', 'description' => 'Acting out specific scenarios or situations', 'item_type' => DesireItemType::Roleplay, 'sort_order' => 2],
            ['name' => 'Professional Roleplay', 'description' => 'Roleplay involving professional settings', 'item_type' => DesireItemType::Roleplay, 'sort_order' => 3],
            ['name' => 'Fantasy Roleplay', 'description' => 'Fantasy-based roleplay scenarios', 'item_type' => DesireItemType::Roleplay, 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            DesireCategory::firstOrCreate(
                ['name' => $category['name'], 'item_type' => $category['item_type']],
                $category
            );
        }
    }
}
