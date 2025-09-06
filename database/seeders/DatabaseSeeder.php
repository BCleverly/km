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

        // Create test users with different roles using proper factory relationships
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

        // Create tasks, rewards, and punishments using proper factory relationships
        $this->createTaskContent();
        
        // Create some assigned tasks with activities using proper relationships
        $this->createAssignedTasksWithActivities();
    }

    /**
     * Create task content (tasks, rewards, punishments)
     */
    private function createTaskContent(): void
    {
        // Create tasks for different user types
        Task::factory(20)
            ->approved()
            ->create();

        Task::factory(5)
            ->pending()
            ->create();

        Task::factory(10)
            ->premium()
            ->approved()
            ->create();

        // Create rewards for different user types
        TaskReward::factory(15)
            ->approved()
            ->create();

        TaskReward::factory(5)
            ->premium()
            ->approved()
            ->create();

        // Create punishments for different user types
        TaskPunishment::factory(12)
            ->approved()
            ->create();

        TaskPunishment::factory(3)
            ->premium()
            ->approved()
            ->create();
    }

    /**
     * Create assigned tasks with activities using proper factory relationships
     */
    private function createAssignedTasksWithActivities(): void
    {
        // Get some users and tasks to work with
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'User');
        })->take(5)->get();

        $tasks = Task::approved()->take(10)->get();
        $rewards = TaskReward::approved()->take(10)->get();
        $punishments = TaskPunishment::approved()->take(10)->get();

        foreach ($users as $user) {
            // Create 2-3 assigned tasks per user
            $assignedTasks = UserAssignedTask::factory()
                ->count(fake()->numberBetween(2, 3))
                ->for($user)
                ->for($tasks->random())
                ->for($rewards->random(), 'potentialReward')
                ->for($punishments->random(), 'potentialPunishment')
                ->assigned()
                ->create();

            // Create some completed tasks
            $completedTasks = UserAssignedTask::factory()
                ->count(fake()->numberBetween(1, 2))
                ->for($user)
                ->for($tasks->random())
                ->for($rewards->random(), 'potentialReward')
                ->for($punishments->random(), 'potentialPunishment')
                ->completed()
                ->create();

            // Create some failed tasks
            $failedTasks = UserAssignedTask::factory()
                ->count(fake()->numberBetween(0, 1))
                ->for($user)
                ->for($tasks->random())
                ->for($rewards->random(), 'potentialReward')
                ->for($punishments->random(), 'potentialPunishment')
                ->failed()
                ->create();

            // Create activities for all assigned tasks using proper relationships
            $allAssignedTasks = $assignedTasks->concat($completedTasks)->concat($failedTasks);
            
            foreach ($allAssignedTasks as $assignedTask) {
                // Create assignment activity
                TaskActivity::factory()
                    ->for($user)
                    ->for($assignedTask->task)
                    ->forAssignedTask($assignedTask)
                    ->ofType(TaskActivityType::Assigned)
                    ->create();

                // Create completion/failure activity based on status
                if ($assignedTask->status === TaskStatus::Completed) {
                    TaskActivity::factory()
                        ->for($user)
                        ->for($assignedTask->task)
                        ->forAssignedTask($assignedTask)
                        ->ofType(TaskActivityType::Completed)
                        ->create();

                    TaskActivity::factory()
                        ->for($user)
                        ->for($assignedTask->task)
                        ->forAssignedTask($assignedTask)
                        ->ofType(TaskActivityType::RewardReceived)
                        ->create();
                } elseif ($assignedTask->status === TaskStatus::Failed) {
                    TaskActivity::factory()
                        ->for($user)
                        ->for($assignedTask->task)
                        ->forAssignedTask($assignedTask)
                        ->ofType(TaskActivityType::Failed)
                        ->create();

                    TaskActivity::factory()
                        ->for($user)
                        ->for($assignedTask->task)
                        ->forAssignedTask($assignedTask)
                        ->ofType(TaskActivityType::PunishmentReceived)
                        ->create();
                }
            }
        }
    }
}
