<?php

namespace Database\Seeders;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users to create statuses for
        $users = User::take(5)->get();

        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create some sample statuses
        $sampleStatuses = [
            'Just completed an amazing task! Feeling accomplished! 🎉',
            'The community here is so supportive. Love being part of this!',
            'New to this platform but already loving the experience.',
            'Task completed successfully! Ready for the next challenge.',
            'This platform has really helped me stay motivated.',
            'Sharing my progress with everyone. We\'re all in this together!',
            'Another day, another task completed. Consistency is key!',
            'The rewards system here is fantastic. Highly recommend!',
            'Feeling proud of my progress this week.',
            'Thank you to everyone who has been supportive!',
        ];

        foreach ($users as $user) {
            // Create 2-4 statuses per user
            $statusCount = rand(2, 4);
            
            for ($i = 0; $i < $statusCount; $i++) {
                Status::factory()->create([
                    'user_id' => $user->id,
                    'content' => $sampleStatuses[array_rand($sampleStatuses)],
                    'is_public' => rand(0, 100) < 85, // 85% chance of being public
                    'created_at' => now()->subDays(rand(0, 7)), // Random time in last week
                ]);
            }
        }

        $this->command->info('Created sample statuses for users.');
    }
}