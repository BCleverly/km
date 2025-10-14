<?php

declare(strict_types=1);

namespace App\Actions\Api\Subscription;

use App\Services\SubscriptionService;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class CancelSubscription
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        if (!$user->subscription()) {
            return [
                'success' => false,
                'message' => 'No active subscription to cancel',
            ];
        }

        $subscriptionService = app(SubscriptionService::class);
        $success = $subscriptionService->cancelSubscription($user);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Subscription cancelled successfully',
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to cancel subscription',
        ];
    }
}