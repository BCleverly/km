<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:create-user 
                            {--name= : The name of the admin user}
                            {--email= : The email of the admin user}
                            {--password= : The password for the admin user}
                            {--interactive : Run in interactive mode}';

    /**
     * The console command description.
     */
    protected $description = 'Create an admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔐 Creating Admin User');
        $this->newLine();

        // Check if we should run in interactive mode
        if ($this->option('interactive') || (! $this->option('name') && ! $this->option('email'))) {
            return $this->interactiveMode();
        }

        // Non-interactive mode
        return $this->nonInteractiveMode();
    }

    /**
     * Run in interactive mode
     */
    private function interactiveMode(): int
    {
        $this->info('Please provide the following information:');
        $this->newLine();

        $name = $this->ask('Full Name');
        $email = $this->ask('Email Address');
        $password = $this->secret('Password (min 8 characters)');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  • {$error}");
            }

            return Command::FAILURE;
        }

        return $this->createUser($name, $email, $password);
    }

    /**
     * Run in non-interactive mode
     */
    private function nonInteractiveMode(): int
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');

        if (! $name || ! $email || ! $password) {
            $this->error('Missing required options. Use --name, --email, and --password or run with --interactive');

            return Command::FAILURE;
        }

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("  • {$error}");
            }

            return Command::FAILURE;
        }

        return $this->createUser($name, $email, $password);
    }

    /**
     * Create the admin user
     */
    private function createUser(string $name, string $email, string $password): int
    {
        try {
            // Create the user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);

            // Create user profile
            $user->profile()->create([
                'username' => $this->generateUsername($name),
                'about' => 'Administrator of the platform',
            ]);

            // Assign admin role
            $user->assignRole('Admin');

            $this->info('✅ Admin user created successfully!');
            $this->newLine();
            $this->info('User Details:');
            $this->line("  • Name: {$user->name}");
            $this->line("  • Email: {$user->email}");
            $this->line("  • Username: {$user->profile->username}");
            $this->line('  • Role: Admin');
            $this->newLine();
            $this->info('You can now log in to the application with these credentials.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to create admin user: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Generate a username from the full name
     */
    private function generateUsername(string $name): string
    {
        $username = strtolower(str_replace(' ', '', $name));

        // Ensure uniqueness
        $originalUsername = $username;
        $counter = 1;

        while (User::whereHas('profile', function ($query) use ($username) {
            $query->where('username', $username);
        })->exists()) {
            $username = $originalUsername.$counter;
            $counter++;
        }

        return $username;
    }
}
