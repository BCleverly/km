<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\PartnerInvitation;
use Illuminate\Console\Command;

class CleanupExpiredInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired partner invitations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredCount = PartnerInvitation::where('expires_at', '<', now())
            ->whereNull('accepted_at')
            ->count();

        if ($expiredCount === 0) {
            $this->info('No expired invitations found.');

            return self::SUCCESS;
        }

        $deletedCount = PartnerInvitation::where('expires_at', '<', now())
            ->whereNull('accepted_at')
            ->delete();

        $this->info("Cleaned up {$deletedCount} expired invitations.");

        return self::SUCCESS;
    }
}
