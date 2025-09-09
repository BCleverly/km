<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Models\Tag;
use App\ContentStatus;
use App\TargetUserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductionSeeder extends Seeder
{
    /**
     * Seed the application's database with production data.
     * 
     * This seeder is designed for production environments and includes:
     * - Roles and permissions system
     * - Initial tasks and outcomes from JSON data
     * - System user for imported content
     * 
     * It does NOT create test users or development data.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting ProductionSeeder...');
        $this->command->info('This seeder will create roles, permissions, and initial content data.');
        $this->command->newLine();

        // Step 1: Seed roles and permissions
        $this->command->info('📋 Creating roles and permissions...');
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
        $this->command->info('✅ Roles and permissions created successfully');

        // Step 2: Import initial content data
        $this->command->info('📦 Importing initial tasks and outcomes...');
        $this->call([
            TaskDataSeeder::class,
        ]);
        $this->command->info('✅ Initial content data imported successfully');

        // Step 3: Display summary
        $this->displaySummary();

        $this->command->newLine();
        $this->command->info('🎉 ProductionSeeder completed successfully!');
        $this->command->newLine();
        
        $this->displayNextSteps();
    }

    /**
     * Display a summary of what was created
     */
    private function displaySummary(): void
    {
        $this->command->newLine();
        $this->command->info('📊 Summary:');
        
        // Count roles
        $roleCount = \Spatie\Permission\Models\Role::count();
        $this->command->line("   • {$roleCount} roles created");
        
        // Count permissions
        $permissionCount = \Spatie\Permission\Models\Permission::count();
        $this->command->line("   • {$permissionCount} permissions created");
        
        // Count tasks
        $taskCount = Task::count();
        $this->command->line("   • {$taskCount} tasks imported");
        
        // Count outcomes
        $outcomeCount = Outcome::count();
        $this->command->line("   • {$outcomeCount} outcomes imported");
        
        // Count tags
        $tagCount = Tag::count();
        $this->command->line("   • {$tagCount} tags created");
        
        // Count system user
        $systemUser = User::where('email', 'system@kinkmaster.com')->first();
        if ($systemUser) {
            $this->command->line("   • System user created for imported content");
        }
    }

    /**
     * Display next steps for production setup
     */
    private function displayNextSteps(): void
    {
        $this->command->info('📝 Next Steps for Production Setup:');
        $this->command->newLine();
        
        $this->command->line('1. 👤 Create your admin user account:');
        $this->command->line('   • Register through the application or create via artisan command');
        $this->command->newLine();
        
        $this->command->line('2. 🔐 Assign Admin role to your user:');
        $this->command->line('   • Use: php artisan tinker');
        $this->command->line('   • Then: $user = User::find(1); $user->assignRole("Admin");');
        $this->command->newLine();
        
        $this->command->line('3. ⚙️  Configure application settings:');
        $this->command->line('   • Update .env file with production values');
        $this->command->line('   • Configure Stripe keys for payments');
        $this->command->line('   • Set up email configuration');
        $this->command->newLine();
        
        $this->command->line('4. 🔍 Verify setup:');
        $this->command->line('   • Test user registration and login');
        $this->command->line('   • Verify admin panel access');
        $this->command->line('   • Check task assignment functionality');
        $this->command->newLine();
        
        $this->command->info('💡 Tip: Run this seeder with: php artisan db:seed --class=ProductionSeeder');
    }
}