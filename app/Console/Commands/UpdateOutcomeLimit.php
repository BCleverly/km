<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateOutcomeLimit extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'outcomes:limit {limit : The new maximum number of active outcomes per user}';

    /**
     * The console command description.
     */
    protected $description = 'Update the maximum number of active outcomes allowed per user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $newLimit = (int) $this->argument('limit');
        
        if ($newLimit < 1) {
            $this->error('The limit must be at least 1.');
            return 1;
        }
        
        if ($newLimit > 10) {
            $this->error('The limit cannot exceed 10 for performance reasons.');
            return 1;
        }
        
        $currentLimit = config('app.tasks.max_active_outcomes', 2);
        
        $this->info("Current limit: {$currentLimit}");
        $this->info("New limit: {$newLimit}");
        
        if ($this->confirm('Are you sure you want to update the outcome limit?')) {
            // Update the .env file
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);
            
            if (strpos($envContent, 'MAX_ACTIVE_OUTCOMES=') !== false) {
                // Update existing entry
                $envContent = preg_replace(
                    '/MAX_ACTIVE_OUTCOMES=\d+/',
                    "MAX_ACTIVE_OUTCOMES={$newLimit}",
                    $envContent
                );
            } else {
                // Add new entry
                $envContent .= "\nMAX_ACTIVE_OUTCOMES={$newLimit}\n";
            }
            
            file_put_contents($envFile, $envContent);
            
            $this->info("✅ Outcome limit updated to {$newLimit}!");
            $this->info("💡 You may need to restart your application for the changes to take effect.");
            
            return 0;
        }
        
        $this->info('Operation cancelled.');
        return 0;
    }
}