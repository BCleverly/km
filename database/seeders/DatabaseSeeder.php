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
        ]);

        // Create test users with different roles
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->profile()->create([
            'username' => 'admin',
            'about' => 'Administrator of the platform',
            'theme_preference' => 'dark',
        ]);
        $admin->assignRole('Admin');

        $moderator = User::factory()->create([
            'name' => 'Moderator User',
            'email' => 'moderator@example.com',
        ]);
        $moderator->profile()->create([
            'username' => 'moderator',
            'about' => 'Content moderator',
            'theme_preference' => 'light',
        ]);
        $moderator->assignRole('Moderator');

        $reviewer = User::factory()->create([
            'name' => 'Reviewer User',
            'email' => 'reviewer@example.com',
        ]);
        $reviewer->profile()->create([
            'username' => 'reviewer',
            'about' => 'Content reviewer',
            'theme_preference' => 'system',
        ]);
        $reviewer->assignRole('Reviewer');

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $user->profile()->create([
            'username' => 'testuser',
            'about' => 'Regular test user',
            'theme_preference' => 'light',
        ]);
        $user->assignRole('User');

        // Create additional regular users
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('User');
        });
    }
}
