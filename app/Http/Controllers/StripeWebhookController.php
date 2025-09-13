<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class StripeWebhookController extends CashierWebhookController
{
    /**
     * Handle customer subscription created.
     */
    protected function handleCustomerSubscriptionCreated(array $payload): Response
    {
        $subscription = $payload['data']['object'];
        $user = User::where('stripe_id', $subscription['customer'])->first();

        if ($user) {
            $subscriptionService = app(SubscriptionService::class);
            
            // Determine the plan from the subscription
            $plan = $this->determinePlanFromSubscription($subscription);
            if ($plan) {
                $subscriptionService->handleSubscriptionCreated(
                    $user,
                    $subscription['id'],
                    $plan
                );
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle customer subscription updated.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $subscription = $payload['data']['object'];
        $user = User::where('stripe_id', $subscription['customer'])->first();

        if ($user) {
            $subscriptionService = app(SubscriptionService::class);
            
            // Determine the plan from the subscription
            $plan = $this->determinePlanFromSubscription($subscription);
            if ($plan) {
                $user->updateSubscriptionPlan($plan);
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle customer subscription deleted.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $subscription = $payload['data']['object'];
        $user = User::where('stripe_id', $subscription['customer'])->first();

        if ($user) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->handleSubscriptionCancelled(
                $user,
                $subscription['id']
            );
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment succeeded.
     */
    protected function handleInvoicePaymentSucceeded(array $payload): Response
    {
        $invoice = $payload['data']['object'];
        $user = User::where('stripe_id', $invoice['customer'])->first();

        if ($user && isset($invoice['subscription'])) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->handleSubscriptionRenewed(
                $user,
                $invoice['subscription']
            );
        }

        return $this->successMethod();
    }

    /**
     * Handle invoice payment failed.
     */
    protected function handleInvoicePaymentFailed(array $payload): Response
    {
        $invoice = $payload['data']['object'];
        $user = User::where('stripe_id', $invoice['customer'])->first();

        if ($user && isset($invoice['subscription'])) {
            $subscriptionService = app(SubscriptionService::class);
            $subscriptionService->handlePaymentFailed(
                $user,
                $invoice['subscription']
            );
        }

        return $this->successMethod();
    }

    /**
     * Determine the subscription plan from Stripe subscription data.
     */
    private function determinePlanFromSubscription(array $subscription): ?SubscriptionPlan
    {
        if (!isset($subscription['items']['data'][0]['price']['id'])) {
            return null;
        }

        $priceId = $subscription['items']['data'][0]['price']['id'];
        
        return SubscriptionPlan::fromStripePriceId($priceId);
    }
}
