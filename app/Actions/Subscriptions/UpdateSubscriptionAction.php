<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSubscriptionAction
{
    use AsAction;

    /**
     * Update a user's subscription to a different plan.
     */
    public function handle(User $user, SubscriptionPlan $newPlan): array
    {
        try {
            $currentSubscription = $user->activeSubscription();
            
            if (!$currentSubscription) {
                return [
                    'success' => false,
                    'message' => 'No active subscription found.',
                    'subscription' => null,
                ];
            }

            $currentPlan = $currentSubscription->plan;
            
            if (!$currentPlan) {
                return [
                    'success' => false,
                    'message' => 'Invalid current subscription.',
                    'subscription' => null,
                ];
            }

            // Don't allow downgrading from lifetime
            if ($currentPlan === SubscriptionPlan::LIFETIME) {
                return [
                    'success' => false,
                    'message' => 'Cannot change from lifetime subscription.',
                    'subscription' => $currentSubscription,
                ];
            }

            // Don't allow changing to the same plan
            if ($currentPlan === $newPlan) {
                return [
                    'success' => false,
                    'message' => 'Already subscribed to this plan.',
                    'subscription' => $currentSubscription,
                ];
            }

            // Handle different update scenarios
            if ($newPlan === SubscriptionPlan::LIFETIME) {
                return $this->upgradeToLifetime($user, $currentSubscription, $newPlan);
            }

            return $this->changeRecurringPlan($user, $currentSubscription, $newPlan);

        } catch (\Exception $e) {
            Log::error('Failed to update subscription', [
                'user_id' => $user->id,
                'new_plan' => $newPlan->value,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update subscription. Please try again.',
                'subscription' => null,
            ];
        }
    }

    /**
     * Change to a different recurring plan.
     */
    private function changeRecurringPlan(User $user, $currentSubscription, SubscriptionPlan $newPlan): array
    {
        $newStripePriceId = $newPlan->stripePriceId();
        
        if (!$newStripePriceId) {
            return [
                'success' => false,
                'message' => 'Invalid subscription plan.',
                'subscription' => null,
            ];
        }

        // Update the subscription in Stripe
        $stripeSubscription = $user->subscription($currentSubscription->type);
        
        if (!$stripeSubscription) {
            return [
                'success' => false,
                'message' => 'Stripe subscription not found.',
                'subscription' => null,
            ];
        }

        $stripeSubscription->swap($newStripePriceId);

        // Update local subscription record
        $currentSubscription->update([
            'stripe_price' => $newStripePriceId,
            'type' => $newPlan->value,
        ]);

        return [
            'success' => true,
            'message' => 'Subscription updated successfully.',
            'subscription' => $currentSubscription->fresh(),
        ];
    }

    /**
     * Upgrade to lifetime subscription.
     */
    private function upgradeToLifetime(User $user, $currentSubscription, SubscriptionPlan $newPlan): array
    {
        $lifetimePrice = $newPlan->price();
        $currentPlan = $currentSubscription->plan;
        
        if (!$currentPlan) {
            return [
                'success' => false,
                'message' => 'Invalid current subscription.',
                'subscription' => null,
            ];
        }

        // Calculate prorated refund for remaining time
        $refundAmount = $this->calculateProratedRefund($currentSubscription, $currentPlan);
        
        // Charge the full lifetime price
        $paymentMethod = $user->defaultPaymentMethod();
        
        if (!$paymentMethod) {
            return [
                'success' => false,
                'message' => 'No payment method on file.',
                'subscription' => null,
            ];
        }

        $paymentIntent = $user->charge($lifetimePrice, $paymentMethod->id, [
            'description' => "Upgrade to Lifetime subscription",
            'metadata' => [
                'subscription_plan' => $newPlan->value,
                'type' => 'lifetime_upgrade',
                'previous_plan' => $currentPlan->value,
                'refund_amount' => $refundAmount,
            ],
        ]);

        if ($paymentIntent->status === 'succeeded') {
            // Cancel the current subscription
            $currentSubscription->cancel();
            
            // Process refund if applicable
            if ($refundAmount > 0) {
                $this->processRefund($currentSubscription, $refundAmount);
            }

            // Create lifetime subscription
            $lifetimeSubscription = $user->subscriptions()->create([
                'type' => $newPlan->value,
                'stripe_id' => 'lifetime_' . $user->id . '_' . time(),
                'stripe_status' => 'active',
                'stripe_price' => $newPlan->stripePriceId(),
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ]);

            return [
                'success' => true,
                'message' => 'Successfully upgraded to lifetime subscription.',
                'subscription' => $lifetimeSubscription,
            ];
        }

        return [
            'success' => false,
            'message' => 'Payment failed. Please try again.',
            'subscription' => null,
        ];
    }

    /**
     * Calculate prorated refund amount for remaining subscription time.
     */
    private function calculateProratedRefund($subscription, SubscriptionPlan $currentPlan): int
    {
        if (!$subscription->ends_at || $subscription->ends_at->isPast()) {
            return 0;
        }

        $totalDays = $subscription->created_at->diffInDays($subscription->ends_at);
        $remainingDays = now()->diffInDays($subscription->ends_at);
        
        if ($totalDays <= 0 || $remainingDays <= 0) {
            return 0;
        }

        $dailyRate = $currentPlan->price() / $totalDays;
        return (int) round($dailyRate * $remainingDays);
    }

    /**
     * Process refund for the current subscription.
     */
    private function processRefund($subscription, int $amount): void
    {
        try {
            // Get the latest invoice
            $invoices = $subscription->user->invoices();
            $latestInvoice = $invoices->first();
            
            if ($latestInvoice && $amount > 0) {
                $latestInvoice->refund($amount, [
                    'reason' => 'requested_by_customer',
                    'metadata' => [
                        'subscription_id' => $subscription->id,
                        'type' => 'prorated_refund',
                    ],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process refund', [
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'error' => $e->getMessage(),
            ]);
        }
    }
}