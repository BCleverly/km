<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SyncViewCountsJob;
use App\Services\ViewTrackingService;
use Illuminate\Console\Command;

class SyncViewCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'views:sync 
                            {--force : Force sync even if no views are pending}
                            {--stats : Show abuse prevention statistics}';

    /**
     * The console command description.
     */
    protected $description = 'Sync view counts from Redis to database and clear tracking data';

    /**
     * Execute the console command.
     */
    public function handle(ViewTrackingService $viewTrackingService): int
    {
        $this->info('Starting view count synchronization...');

        // Show statistics if requested
        if ($this->option('stats')) {
            $this->showAbuseStats($viewTrackingService);
        }

        // Check if there are any views to sync
        $viewKeys = \Illuminate\Support\Facades\Redis::keys('views:*');
        
        if (empty($viewKeys) && !$this->option('force')) {
            $this->info('No view counts to sync.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($viewKeys) . ' view count entries to sync.');

        // Dispatch the job
        SyncViewCountsJob::dispatch();

        $this->info('View count sync job has been dispatched.');
        $this->info('The job will sync view counts and clear tracking data.');

        return self::SUCCESS;
    }

    /**
     * Show abuse prevention statistics
     */
    private function showAbuseStats(ViewTrackingService $viewTrackingService): void
    {
        $stats = $viewTrackingService->getAbuseStats();

        $this->newLine();
        $this->info('Abuse Prevention Statistics:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Max views per session', $stats['max_views_per_session']],
                ['Max daily views per user', $stats['max_daily_views_per_user']],
                ['View cooldown (minutes)', $stats['view_cooldown_minutes']],
                ['Session duration (hours)', $stats['session_duration_hours']],
                ['Total view keys', $stats['total_view_keys']],
                ['Total session keys', $stats['total_session_keys']],
                ['Total daily limit keys', $stats['total_daily_limit_keys']],
            ]
        );
        $this->newLine();
    }
}