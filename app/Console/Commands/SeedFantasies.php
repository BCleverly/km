<?php

namespace App\Console\Commands;

use Database\Seeders\FantasySeeder;
use Illuminate\Console\Command;

class SeedFantasies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:fantasies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with fantasy content';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Seeding fantasies...');
        
        $seeder = new FantasySeeder();
        $seeder->setCommand($this);
        $seeder->run();
        
        $this->info('Fantasies seeded successfully!');
        
        return Command::SUCCESS;
    }
}