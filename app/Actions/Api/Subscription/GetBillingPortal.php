<?php

declare(strict_types=1);

namespace App\Actions\Api\Subscription;

use App\Services\SubscriptionService;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;

class GetBillingPortal
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        if (!$user->subscription()) {
            return [
                'success' => false,
                'message' => 'No active subscription found',
            ];
        }

        try {
            $subscriptionService = app(SubscriptionService::class);
            $billingUrl = $subscriptionService->getBillingPortalUrl($user);

            return [
                'success' => true,
                'billing_portal_url' => $billingUrl,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create billing portal URL: ' . $e->getMessage(),
            ];
        }
    }
}