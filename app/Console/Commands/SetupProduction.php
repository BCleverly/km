<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupProduction extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'setup:production 
                            {--skip-admin : Skip admin user creation}
                            {--admin-name= : Admin user name}
                            {--admin-email= : Admin user email}
                            {--admin-password= : Admin user password}';

    /**
     * The console command description.
     */
    protected $description = 'Complete production setup including database seeding and admin user creation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Production Setup');
        $this->newLine();
        $this->info('This will set up your Kink Master application for production use.');
        $this->newLine();

        // Step 1: Run migrations
        $this->info('📊 Step 1: Running database migrations...');
        if ($this->runMigrations() !== Command::SUCCESS) {
            return Command::FAILURE;
        }

        // Step 2: Run production seeder
        $this->info('🌱 Step 2: Seeding production data...');
        if ($this->runProductionSeeder() !== Command::SUCCESS) {
            return Command::FAILURE;
        }

        // Step 3: Create admin user (unless skipped)
        if (!$this->option('skip-admin')) {
            $this->info('👤 Step 3: Creating admin user...');
            if ($this->createAdminUser() !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        } else {
            $this->info('⏭️  Step 3: Skipping admin user creation (--skip-admin flag used)');
        }

        // Step 4: Display completion message
        $this->displayCompletionMessage();

        return Command::SUCCESS;
    }

    /**
     * Run database migrations
     */
    private function runMigrations(): int
    {
        try {
            $this->line('   Running migrations...');
            Artisan::call('migrate', ['--force' => true]);
            
            if (Artisan::output()) {
                $this->line('   ' . trim(Artisan::output()));
            }
            
            $this->info('   ✅ Migrations completed successfully');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('   ❌ Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Run the production seeder
     */
    private function runProductionSeeder(): int
    {
        try {
            $this->line('   Seeding roles, permissions, and initial content...');
            Artisan::call('db:seed', [
                '--class' => 'ProductionSeeder',
                '--force' => true
            ]);
            
            if (Artisan::output()) {
                $this->line('   ' . trim(Artisan::output()));
            }
            
            $this->info('   ✅ Production data seeded successfully');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('   ❌ Seeding failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Create admin user
     */
    private function createAdminUser(): int
    {
        try {
            $adminName = $this->option('admin-name');
            $adminEmail = $this->option('admin-email');
            $adminPassword = $this->option('admin-password');

            // If no options provided, run in interactive mode
            if (!$adminName || !$adminEmail || !$adminPassword) {
                $this->line('   Running in interactive mode...');
                Artisan::call('admin:create-user', ['--interactive' => true]);
            } else {
                $this->line('   Creating admin user with provided credentials...');
                Artisan::call('admin:create-user', [
                    '--name' => $adminName,
                    '--email' => $adminEmail,
                    '--password' => $adminPassword,
                ]);
            }
            
            if (Artisan::output()) {
                $this->line('   ' . trim(Artisan::output()));
            }
            
            $this->info('   ✅ Admin user created successfully');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('   ❌ Admin user creation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display completion message with next steps
     */
    private function displayCompletionMessage(): void
    {
        $this->newLine();
        $this->info('🎉 Production Setup Complete!');
        $this->newLine();
        
        $this->info('✅ What was set up:');
        $this->line('   • Database migrations applied');
        $this->line('   • Roles and permissions created');
        $this->line('   • Initial tasks and outcomes imported');
        $this->line('   • System user created for imported content');
        
        if (!$this->option('skip-admin')) {
            $this->line('   • Admin user created and configured');
        }
        
        $this->newLine();
        $this->info('📝 Next Steps:');
        $this->newLine();
        
        $this->line('1. 🔧 Configure your environment:');
        $this->line('   • Update .env file with production values');
        $this->line('   • Set APP_ENV=production');
        $this->line('   • Configure database connection');
        $this->line('   • Set up Stripe keys for payments');
        $this->line('   • Configure email settings');
        $this->newLine();
        
        $this->line('2. 🔐 Security setup:');
        $this->line('   • Generate application key: php artisan key:generate');
        $this->line('   • Set secure session and cookie settings');
        $this->line('   • Configure HTTPS in production');
        $this->newLine();
        
        $this->line('3. 🚀 Deploy and test:');
        $this->line('   • Deploy to your production server');
        $this->line('   • Test user registration and login');
        $this->line('   • Verify admin panel access');
        $this->line('   • Test task assignment functionality');
        $this->newLine();
        
        if ($this->option('skip-admin')) {
            $this->warn('⚠️  Remember to create an admin user:');
            $this->line('   php artisan admin:create-user --interactive');
            $this->newLine();
        }
        
        $this->info('💡 For more help, check the documentation or run individual commands:');
        $this->line('   • php artisan db:seed --class=ProductionSeeder');
        $this->line('   • php artisan admin:create-user --interactive');
        $this->newLine();
        
        $this->info('🎯 Your Kink Master application is ready for production!');
    }
}