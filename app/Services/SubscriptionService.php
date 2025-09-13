<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionService
{
    /**
     * Create a Stripe checkout session for subscription
     */
    public function createCheckoutSession(User $user, SubscriptionPlan $plan): string
    {
        if (!$plan->stripePriceId()) {
            throw new \InvalidArgumentException("Stripe price ID not configured for plan: {$plan->label()}");
        }

        $checkout = $user->newSubscription('default', $plan->stripePriceId())
            ->checkout([
                'success_url' => route('app.subscription.success'),
                'cancel_url' => route('app.subscription.cancel'),
                'metadata' => [
                    'plan' => $plan->value,
                    'user_id' => $user->id,
                ],
            ]);

        return $checkout->url;
    }

    /**
     * Handle successful subscription creation
     */
    public function handleSubscriptionCreated(User $user, string $stripeSubscriptionId, SubscriptionPlan $plan): void
    {
        // Update user's subscription plan
        $user->updateSubscriptionPlan($plan);

        // Log the subscription creation
        Log::info('Subscription created', [
            'user_id' => $user->id,
            'plan' => $plan->label(),
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);
    }

    /**
     * Handle subscription cancellation
     */
    public function handleSubscriptionCancelled(User $user, string $stripeSubscriptionId): void
    {
        // Update user's subscription plan to Free
        $user->updateSubscriptionPlan(SubscriptionPlan::Free);

        // Log the subscription cancellation
        Log::info('Subscription cancelled', [
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);
    }

    /**
     * Handle subscription renewal
     */
    public function handleSubscriptionRenewed(User $user, string $stripeSubscriptionId): void
    {
        // Log the subscription renewal
        Log::info('Subscription renewed', [
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);
    }

    /**
     * Handle payment failure
     */
    public function handlePaymentFailed(User $user, string $stripeSubscriptionId): void
    {
        // Log the payment failure
        Log::warning('Payment failed', [
            'user_id' => $user->id,
            'stripe_subscription_id' => $stripeSubscriptionId,
        ]);

        // You might want to send an email notification here
        // or implement retry logic
    }

    /**
     * Cancel a user's subscription
     */
    public function cancelSubscription(User $user): bool
    {
        try {
            if ($user->subscription()) {
                $user->subscription()->cancel();
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Resume a cancelled subscription
     */
    public function resumeSubscription(User $user): bool
    {
        try {
            if ($user->subscription() && $user->subscription()->cancelled()) {
                $user->subscription()->resume();
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to resume subscription', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Update subscription plan
     */
    public function updateSubscriptionPlan(User $user, SubscriptionPlan $newPlan): bool
    {
        try {
            if ($user->subscription() && $newPlan->stripePriceId()) {
                $user->subscription()->swap($newPlan->stripePriceId());
                $user->updateSubscriptionPlan($newPlan);
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to update subscription plan', [
                'user_id' => $user->id,
                'new_plan' => $newPlan->label(),
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    /**
     * Get subscription billing portal URL
     */
    public function getBillingPortalUrl(User $user): string
    {
        return $user->billingPortalUrl(route('app.subscription.billing'));
    }

    /**
     * Check if user can upgrade to a specific plan
     */
    public function canUpgradeTo(User $user, SubscriptionPlan $plan): bool
    {
        // Users can always upgrade to a higher tier
        return $user->subscription_plan->value < $plan->value;
    }

    /**
     * Check if user can downgrade to a specific plan
     */
    public function canDowngradeTo(User $user, SubscriptionPlan $plan): bool
    {
        // Users can downgrade to a lower tier
        return $user->subscription_plan->value > $plan->value;
    }
}
