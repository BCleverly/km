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
            ]
        );
        $coupleUser2->assignRole('User');

        // Link the couple users
        $coupleUser1->update(['partner_id' => $coupleUser2->id]);
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
