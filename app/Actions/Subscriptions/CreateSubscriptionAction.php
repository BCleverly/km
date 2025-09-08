<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSubscriptionAction
{
    use AsAction;

    /**
     * Create a new subscription for a user.
     */
    public function handle(User $user, SubscriptionPlan $plan, ?string $paymentMethodId = null): array
    {
        try {
            // Check if user already has an active subscription
            if ($user->hasActiveSubscription()) {
                return [
                    'success' => false,
                    'message' => 'User already has an active subscription.',
                    'subscription' => null,
                ];
            }

            // Create Stripe customer if they don't have one
            if (!$user->hasStripeId()) {
                $user->createAsStripeCustomer();
            }

            // Handle different subscription types
            if ($plan === SubscriptionPlan::LIFETIME) {
                return $this->createLifetimeSubscription($user, $plan, $paymentMethodId);
            }

            return $this->createRecurringSubscription($user, $plan, $paymentMethodId);

        } catch (\Exception $e) {
            Log::error('Failed to create subscription', [
                'user_id' => $user->id,
                'plan' => $plan->value,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create subscription. Please try again.',
                'subscription' => null,
            ];
        }
    }

    /**
     * Create a recurring subscription (monthly/couple).
     */
    private function createRecurringSubscription(User $user, SubscriptionPlan $plan, ?string $paymentMethodId = null): array
    {
        $stripePriceId = $plan->stripePriceId();
        
        if (!$stripePriceId) {
            return [
                'success' => false,
                'message' => 'Invalid subscription plan.',
                'subscription' => null,
            ];
        }

        $trialDays = $plan->trialDays();
        
        $subscriptionData = [
            'price' => $stripePriceId,
            'trial_period_days' => $trialDays,
        ];

        if ($paymentMethodId) {
            $subscriptionData['default_payment_method'] = $paymentMethodId;
        }

        $subscription = $user->newSubscription($plan->value, $stripePriceId)
            ->trialDays($trialDays);

        if ($paymentMethodId) {
            $subscription = $subscription->create($paymentMethodId);
        } else {
            $subscription = $subscription->create();
        }

        return [
            'success' => true,
            'message' => 'Subscription created successfully.',
            'subscription' => $subscription,
        ];
    }

    /**
     * Create a lifetime subscription (one-time payment).
     */
    private function createLifetimeSubscription(User $user, SubscriptionPlan $plan, ?string $paymentMethodId = null): array
    {
        $stripePriceId = $plan->stripePriceId();
        
        if (!$stripePriceId) {
            return [
                'success' => false,
                'message' => 'Invalid subscription plan.',
                'subscription' => null,
            ];
        }

        if (!$paymentMethodId) {
            return [
                'success' => false,
                'message' => 'Payment method required for lifetime subscription.',
                'subscription' => null,
            ];
        }

        // Create a one-time payment
        $paymentIntent = $user->charge($plan->price(), $paymentMethodId, [
            'description' => "Lifetime subscription - {$plan->label()}",
            'metadata' => [
                'subscription_plan' => $plan->value,
                'type' => 'lifetime',
            ],
        ]);

        if ($paymentIntent->status === 'succeeded') {
            // Create a subscription record for lifetime access
            $subscription = $user->subscriptions()->create([
                'type' => $plan->value,
                'stripe_id' => 'lifetime_' . $user->id . '_' . time(),
                'stripe_status' => 'active',
                'stripe_price' => $stripePriceId,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null, // Never expires
            ]);

            return [
                'success' => true,
                'message' => 'Lifetime subscription activated successfully.',
                'subscription' => $subscription,
            ];
        }

        return [
            'success' => false,
            'message' => 'Payment failed. Please try again.',
            'subscription' => null,
        ];
    }
}