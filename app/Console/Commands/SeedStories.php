<?php

namespace App\Console\Commands;

use Database\Seeders\StorySeeder;
use Illuminate\Console\Command;

class SeedStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:stories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with story content';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Seeding stories...');
        
        $seeder = new StorySeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Stories seeded successfully!');
        
        return Command::SUCCESS;
    }
}