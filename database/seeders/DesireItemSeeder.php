<?php

namespace Database\Seeders;

use App\Enums\DesireItemType;
use App\Models\DesireItem;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Database\Seeder;

class DesireItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run the main database seeder first.');

            return;
        }

        // Create items for each type
        $this->createItemsForType(DesireItemType::Fetish, 20, $users);
        $this->createItemsForType(DesireItemType::Fantasy, 20, $users);
        $this->createItemsForType(DesireItemType::Kink, 15, $users);
        $this->createItemsForType(DesireItemType::Toy, 15, $users);
        $this->createItemsForType(DesireItemType::Activity, 20, $users);
        $this->createItemsForType(DesireItemType::Roleplay, 15, $users);

        // Create some pending items
        DesireItem::factory()->count(10)->pending()->create();

        // Create some premium items
        DesireItem::factory()->count(15)->premium()->create();

        // Create items for specific user types
        DesireItem::factory()->count(10)->forUserType(TargetUserType::Male)->create();
        DesireItem::factory()->count(10)->forUserType(TargetUserType::Female)->create();
        DesireItem::factory()->count(10)->forUserType(TargetUserType::Couple)->create();

        $this->command->info('Desire items seeded successfully!');
    }

    private function createItemsForType(DesireItemType $itemType, int $count, $users): void
    {
        DesireItem::factory()
            ->count($count)
            ->forItemType($itemType)
            ->create([
                'user_id' => $users->random()->id,
            ]);
    }
}
