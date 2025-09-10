<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskActivity;
use App\TargetUserType;
use App\ContentStatus;
use App\TaskStatus;
use App\TaskActivityType;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Import task data from JSON files
        $this->call([
            TaskDataSeeder::class,
        ]);

        // Seed content (fantasies and stories)
        $this->call([
            FantasySeeder::class,
            StorySeeder::class,
        ]);

        // Create test users with different roles using proper factory relationships
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $admin->profile()->firstOrCreate(
            ['user_id' => $admin->id],
            [
                'username' => 'admin',
                'about' => 'Administrator of the platform',
                'theme_preference' => 'dark',
            ]
        );
        $admin->assignRole('Admin');

        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'email' => 'moderator@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $moderator->profile()->firstOrCreate(
            ['user_id' => $moderator->id],
            [
                'username' => 'moderator',
                'about' => 'Content moderator',
                'theme_preference' => 'light',
            ]
        );
        $moderator->assignRole('Moderator');

        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@example.com'],
            [
                'name' => 'Reviewer User',
                'email' => 'reviewer@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $reviewer->profile()->firstOrCreate(
            ['user_id' => $reviewer->id],
            [
                'username' => 'reviewer',
                'about' => 'Content reviewer',
                'theme_preference' => 'system',
            ]
        );
        $reviewer->assignRole('Reviewer');

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]
        );
        $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'username' => 'testuser',
                'about' => 'Regular test user',
                'theme_preference' => 'light',
            ]
        );
        $user->assignRole('User');

        // Create test users for each subscription level
        $this->createTestUsers();

        // Create additional regular users
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('User');
        });
    }

    /**
     * Create test users for each subscription level
     */
    private function createTestUsers(): void
    {
        // Solo (Free) User
        $soloUser = User::firstOrCreate(
            ['email' => 'solo@example.com'],
            [
                'name' => 'Solo User',
                'email' => 'solo@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Male,
            ]
        );
        $soloUser->profile()->firstOrCreate(
            ['user_id' => $soloUser->id],
            [
                'username' => 'solo',
                'about' => 'Free tier solo user',
                'theme_preference' => 'light',
            ]
        );
        $soloUser->assignRole('User');

        // Couple (Free) Users
        $coupleUser1 = User::firstOrCreate(
            ['email' => 'couple1@example.com'],
            [
                'name' => 'Couple User 1',
                'email' => 'couple1@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Couple,
            ]
        );
        $coupleUser1->profile()->firstOrCreate(
            ['user_id' => $coupleUser1->id],
            [
                'username' => 'couple1',
                'about' => 'First half of couple account',
                'theme_preference' => 'dark',
            ]
        );
        $coupleUser1->assignRole('User');

        $coupleUser2 = User::firstOrCreate(
            ['email' => 'couple2@example.com'],
            [
                'name' => 'Couple User 2',
                'email' => 'couple2@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Couple,
                'partner_id' => $coupleUser1->id,
            ]
        );
        $coupleUser2->profile()->firstOrCreate(
            ['user_id' => $coupleUser2->id],
            [
                'username' => 'couple2',
                'about' => 'Second half of couple account',
                'theme_preference' => 'dark',
            ]
        );
        $coupleUser2->assignRole('User');

        // Link the couple users
        $coupleUser1->update(['partner_id' => $coupleUser2->id]);

        // Premium User
        $premiumUser = User::firstOrCreate(
            ['email' => 'premium@example.com'],
            [
                'name' => 'Premium User',
                'email' => 'premium@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Female,
            ]
        );
        $premiumUser->profile()->firstOrCreate(
            ['user_id' => $premiumUser->id],
            [
                'username' => 'premium',
                'about' => 'Premium subscription user',
                'theme_preference' => 'system',
            ]
        );
        $premiumUser->assignRole('User');
        
        // Create premium subscription
        $premiumUser->subscriptions()->create([
            'type' => 'premium',
            'stripe_id' => 'sub_premium_test',
            'stripe_status' => 'active',
        ]);

        // Lifetime User
        $lifetimeUser = User::firstOrCreate(
            ['email' => 'lifetime@example.com'],
            [
                'name' => 'Lifetime User',
                'email' => 'lifetime@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Any,
            ]
        );
        $lifetimeUser->profile()->firstOrCreate(
            ['user_id' => $lifetimeUser->id],
            [
                'username' => 'lifetime',
                'about' => 'Lifetime subscription user',
                'theme_preference' => 'light',
            ]
        );
        $lifetimeUser->assignRole('User');
        
        // Create lifetime subscription
        $lifetimeUser->subscriptions()->create([
            'type' => 'lifetime',
            'stripe_id' => 'sub_lifetime_test',
            'stripe_status' => 'active',
        ]);
    }

}
