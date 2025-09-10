<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ViewTrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncViewCountsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300; // 5 minutes

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ViewTrackingService $viewTrackingService): void
    {
        Log::info('Starting view count sync job');

        try {
            $syncedCount = $viewTrackingService->syncViewCountsToDatabase();
            
            Log::info('View count sync job completed successfully', [
                'synced_count' => $syncedCount,
            ]);

            // Dispatch a notification job if needed
            if ($syncedCount > 0) {
                Log::info('View counts synced', [
                    'count' => $syncedCount,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('View count sync job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('View count sync job failed permanently', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // You could dispatch a notification here to alert administrators
        // or implement additional fallback mechanisms
    }
}