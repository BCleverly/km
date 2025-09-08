<?php

namespace App\Actions\Subscriptions;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelSubscriptionAction
{
    use AsAction;

    /**
     * Cancel a user's subscription.
     */
    public function handle(User $user, bool $immediately = false): array
    {
        try {
            $subscription = $user->activeSubscription();
            
            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => 'No active subscription found.',
                    'subscription' => null,
                ];
            }

            // Don't allow cancelling lifetime subscriptions
            if ($subscription->plan && $subscription->plan->value === 'lifetime') {
                return [
                    'success' => false,
                    'message' => 'Lifetime subscriptions cannot be cancelled.',
                    'subscription' => $subscription,
                ];
            }

            $stripeSubscription = $user->subscription($subscription->type);
            
            if (!$stripeSubscription) {
                return [
                    'success' => false,
                    'message' => 'Stripe subscription not found.',
                    'subscription' => null,
                ];
            }

            if ($immediately) {
                // Cancel immediately
                $stripeSubscription->cancelNow();
                $subscription->update(['stripe_status' => 'canceled']);
            } else {
                // Cancel at the end of the billing period
                $stripeSubscription->cancel();
                $subscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => $stripeSubscription->ends_at,
                ]);
            }

            return [
                'success' => true,
                'message' => $immediately 
                    ? 'Subscription cancelled immediately.' 
                    : 'Subscription will be cancelled at the end of the billing period.',
                'subscription' => $subscription->fresh(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'user_id' => $user->id,
                'immediately' => $immediately,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to cancel subscription. Please try again.',
                'subscription' => null,
            ];
        }
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resume(User $user): array
    {
        try {
            $subscription = $user->subscriptions()
                ->where('stripe_status', 'canceled')
                ->where('ends_at', '>', now())
                ->first();
            
            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => 'No cancelled subscription found to resume.',
                    'subscription' => null,
                ];
            }

            $stripeSubscription = $user->subscription($subscription->type);
            
            if (!$stripeSubscription) {
                return [
                    'success' => false,
                    'message' => 'Stripe subscription not found.',
                    'subscription' => null,
                ];
            }

            $stripeSubscription->resume();
            $subscription->update([
                'stripe_status' => 'active',
                'ends_at' => null,
            ]);

            return [
                'success' => true,
                'message' => 'Subscription resumed successfully.',
                'subscription' => $subscription->fresh(),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to resume subscription', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to resume subscription. Please try again.',
                'subscription' => null,
            ];
        }
    }
}