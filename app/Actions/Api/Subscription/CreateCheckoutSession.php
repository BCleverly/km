<?php

declare(strict_types=1);

namespace App\Actions\Api\Subscription;

use App\Enums\SubscriptionPlan;
use App\Services\SubscriptionService;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateCheckoutSession
{
    use AsAction;

    public function handle(Request $request): array
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'plan' => ['required', 'integer', 'in:1,2,3,4'], // Solo, Premium, Couple, Lifetime
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ];
        }

        $plan = SubscriptionPlan::from($request->plan);

        // Check if user can upgrade to this plan
        $subscriptionService = app(SubscriptionService::class);
        if (!$subscriptionService->canUpgradeTo($user, $plan)) {
            return [
                'success' => false,
                'message' => 'Cannot upgrade to this plan',
            ];
        }

        try {
            $checkoutUrl = $subscriptionService->createCheckoutSession($user, $plan);

            return [
                'success' => true,
                'checkout_url' => $checkoutUrl,
                'plan' => [
                    'value' => $plan->value,
                    'label' => $plan->label(),
                    'price_formatted' => $plan->priceFormatted(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create checkout session: ' . $e->getMessage(),
            ];
        }
    }
}