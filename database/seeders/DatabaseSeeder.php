<?php

namespace Database\Seeders;

use App\Models\User;
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
            TaskDataSeeder::class, // Creates tags and tasks
            FantasySeeder::class,  // Uses tags from TaskDataSeeder
            StorySeeder::class,    // Uses tags from TaskDataSeeder
        ]);

        // Create test users with different roles using proper factory relationships
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => bcrypt('password'),
            ]
        );
        $admin->profile()->firstOrCreate(
            ['user_id' => $admin->id],
            [
                'username' => 'admin',
                'about' => 'Administrator of the platform',
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
            ]
        );
        $user->assignRole('User');

        // Create test users for each subscription level
        if (app()->isLocal()) {
            $this->createTestUsers();
        }

        // Ensure all users have profiles with usernames
        $this->ensureAllUsersHaveProfiles();
    }

    /**
     * Create test users for each subscription level
     */
    private function createTestUsers(): void
    {
        // Free User (on trial)
        $freeUser = User::firstOrCreate(
            ['email' => 'free@example.com'],
            [
                'name' => 'Free User',
                'email' => 'free@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Male,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Free,
                'trial_ends_at' => now()->addDays(7), // Trial expires in 7 days
                'has_used_trial' => true,
            ]
        );
        $freeUser->profile()->firstOrCreate(
            ['user_id' => $freeUser->id],
            [
                'username' => 'freeuser',
                'about' => 'Free tier user on trial',
            ]
        );
        $freeUser->assignRole('User');

        // Solo User
        $soloUser = User::firstOrCreate(
            ['email' => 'solo@example.com'],
            [
                'name' => 'Solo User',
                'email' => 'solo@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Male,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Solo,
                'has_used_trial' => true,
            ]
        );
        $soloUser->profile()->firstOrCreate(
            ['user_id' => $soloUser->id],
            [
                'username' => 'solouser',
                'about' => 'Solo subscription user',
            ]
        );
        $soloUser->assignRole('User');

        // Premium User
        $premiumUser = User::firstOrCreate(
            ['email' => 'premium@example.com'],
            [
                'name' => 'Premium User',
                'email' => 'premium@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Female,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Premium,
                'has_used_trial' => true,
            ]
        );
        $premiumUser->profile()->firstOrCreate(
            ['user_id' => $premiumUser->id],
            [
                'username' => 'premiumuser',
                'about' => 'Premium subscription user',
            ]
        );
        $premiumUser->assignRole('User');

        // Couple Users
        $coupleUser1 = User::firstOrCreate(
            ['email' => 'couple1@example.com'],
            [
                'name' => 'Couple User 1',
                'email' => 'couple1@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Couple,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Couple,
                'has_used_trial' => true,
            ]
        );
        $coupleUser1->profile()->firstOrCreate(
            ['user_id' => $coupleUser1->id],
            [
                'username' => 'couple1',
                'about' => 'First half of couple account',
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
                'subscription_plan' => \App\Enums\SubscriptionPlan::Couple,
                'partner_id' => $coupleUser1->id,
                'has_used_trial' => true,
            ]
        );
        $coupleUser2->profile()->firstOrCreate(
            ['user_id' => $coupleUser2->id],
            [
                'username' => 'couple2',
                'about' => 'Second half of couple account',
            ]
        );
        $coupleUser2->assignRole('User');

        // Link the couple users
        $coupleUser1->update(['partner_id' => $coupleUser2->id]);

        // Lifetime User
        $lifetimeUser = User::firstOrCreate(
            ['email' => 'lifetime@example.com'],
            [
                'name' => 'Lifetime User',
                'email' => 'lifetime@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Male,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Lifetime,
                'has_used_trial' => true,
            ]
        );
        $lifetimeUser->profile()->firstOrCreate(
            ['user_id' => $lifetimeUser->id],
            [
                'username' => 'lifetimeuser',
                'about' => 'Lifetime subscription user',
            ]
        );
        $lifetimeUser->assignRole('User');

        // Expired Trial User (needs to choose subscription)
        $expiredTrialUser = User::firstOrCreate(
            ['email' => 'expired@example.com'],
            [
                'name' => 'Expired Trial User',
                'email' => 'expired@example.com',
                'password' => bcrypt('password'),
                'user_type' => \App\TargetUserType::Female,
                'subscription_plan' => \App\Enums\SubscriptionPlan::Free,
                'trial_ends_at' => now()->subDays(1), // Trial expired yesterday
                'has_used_trial' => true,
            ]
        );
        $expiredTrialUser->profile()->firstOrCreate(
            ['user_id' => $expiredTrialUser->id],
            [
                'username' => 'expireduser',
                'about' => 'User with expired trial',
            ]
        );
        $expiredTrialUser->assignRole('User');
    }

    /**
     * Ensure all users have profiles with usernames
     */
    private function ensureAllUsersHaveProfiles(): void
    {
        $this->command->info('Ensuring all users have profiles with usernames...');

        $usersWithoutProfiles = User::whereDoesntHave('profile')->get();
        
        if ($usersWithoutProfiles->isEmpty()) {
            $this->command->info('All users already have profiles.');
            return;
        }

        $this->command->info("Found {$usersWithoutProfiles->count()} users without profiles. Creating profiles...");

        foreach ($usersWithoutProfiles as $user) {
            $username = $this->generateUniqueUsername($user);
            
            $user->profile()->create([
                'username' => $username,
                'about' => $this->generateAboutText($user),
            ]);

            $this->command->info("Created profile for user: {$user->name} ({$user->email}) with username: {$username}");
        }

        $this->command->info('All users now have profiles with usernames.');
    }

    /**
     * Generate a unique username for a user
     */
    private function generateUniqueUsername(User $user): string
    {
        // Try to use the user's name first
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->name));
        
        // If name is empty or too short, use email prefix
        if (empty($baseUsername) || strlen($baseUsername) < 3) {
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $user->email)[0]));
        }
        
        // Ensure minimum length
        if (strlen($baseUsername) < 3) {
            $baseUsername = 'user' . $user->id;
        }

        // Check if username is unique
        $username = $baseUsername;
        $counter = 1;
        
        while (\App\Models\Profile::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate appropriate about text for a user
     */
    private function generateAboutText(User $user): string
    {
        // Check if this is a system user
        if (str_contains($user->email, 'system@') || str_contains($user->email, '@kinkmaster.com')) {
            return 'System account for imported content';
        }

        // Check if this is an admin/moderator
        if ($user->hasRole(['Admin', 'Moderator', 'Reviewer'])) {
            return ucfirst(strtolower($user->getRoleNames()->first())) . ' account';
        }

        // Default for regular users
        return 'Welcome to my profile!';
    }
}
