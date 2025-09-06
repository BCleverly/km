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
            'username' => 'admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('Admin');

        $moderator = User::factory()->create([
            'name' => 'Moderator User',
            'username' => 'moderator',
            'email' => 'moderator@example.com',
        ]);
        $moderator->assignRole('Moderator');

        $reviewer = User::factory()->create([
            'name' => 'Reviewer User',
            'username' => 'reviewer',
            'email' => 'reviewer@example.com',
        ]);
        $reviewer->assignRole('Reviewer');

        $user = User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
        $user->assignRole('User');

        // Create additional regular users
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('User');
        });
    }
}
