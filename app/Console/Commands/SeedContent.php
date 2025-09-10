<?php

namespace App\Console\Commands;

use Database\Seeders\FantasySeeder;
use Database\Seeders\StorySeeder;
use Illuminate\Console\Command;

class SeedContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:content {--fantasies : Seed only fantasies} {--stories : Seed only stories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with content (fantasies and stories)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $seedFantasies = $this->option('fantasies') || !$this->option('stories');
        $seedStories = $this->option('stories') || !$this->option('fantasies');

        if ($seedFantasies) {
            $this->info('Seeding fantasies...');
            $fantasySeeder = new FantasySeeder();
            $fantasySeeder->setCommand($this);
            $fantasySeeder->run();
            $this->info('Fantasies seeded successfully!');
        }

        if ($seedStories) {
            $this->info('Seeding stories...');
            $storySeeder = new StorySeeder();
            $storySeeder->setCommand($this);
            $storySeeder->run();
            $this->info('Stories seeded successfully!');
        }

        $this->info('Content seeding completed!');
        
        return Command::SUCCESS;
    }
}