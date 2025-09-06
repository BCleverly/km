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

        // Create additional regular users
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('User');
        });
    }

}
