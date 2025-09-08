<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\Subscriptions\CancelSubscriptionAction;
use App\Enums\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    /**
     * Handle customer subscription created.
     */
    protected function handleCustomerSubscriptionCreated($payload)
    {
        $subscription = $payload['data']['object'];
        $userId = $subscription['metadata']['user_id'] ?? null;
        
        if (!$userId) {
            Log::warning('Stripe webhook: No user_id in subscription metadata', [
                'subscription_id' => $subscription['id'],
            ]);
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            Log::warning('Stripe webhook: User not found', [
                'user_id' => $userId,
                'subscription_id' => $subscription['id'],
            ]);
            return;
        }

        // Update or create subscription record
        $localSubscription = $user->subscriptions()
            ->where('stripe_id', $subscription['id'])
            ->first();

        if (!$localSubscription) {
            $plan = SubscriptionPlan::fromStripePriceId($subscription['items']['data'][0]['price']['id']);
            
            $user->subscriptions()->create([
                'type' => $plan?->value ?? 'monthly',
                'stripe_id' => $subscription['id'],
                'stripe_status' => $subscription['status'],
                'stripe_price' => $subscription['items']['data'][0]['price']['id'],
                'quantity' => $subscription['items']['data'][0]['quantity'] ?? 1,
                'trial_ends_at' => $subscription['trial_end'] ? 
                    \Carbon\Carbon::createFromTimestamp($subscription['trial_end']) : null,
                'ends_at' => $subscription['current_period_end'] ? 
                    \Carbon\Carbon::createFromTimestamp($subscription['current_period_end']) : null,
            ]);
        }

        Log::info('Stripe webhook: Subscription created', [
            'user_id' => $userId,
            'subscription_id' => $subscription['id'],
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Handle customer subscription updated.
     */
    protected function handleCustomerSubscriptionUpdated($payload)
    {
        $subscription = $payload['data']['object'];
        
        $localSubscription = Subscription::where('stripe_id', $subscription['id'])->first();
        
        if (!$localSubscription) {
            Log::warning('Stripe webhook: Local subscription not found for update', [
                'subscription_id' => $subscription['id'],
            ]);
            return;
        }

        $localSubscription->update([
            'stripe_status' => $subscription['status'],
            'stripe_price' => $subscription['items']['data'][0]['price']['id'],
            'quantity' => $subscription['items']['data'][0]['quantity'] ?? 1,
            'trial_ends_at' => $subscription['trial_end'] ? 
                \Carbon\Carbon::createFromTimestamp($subscription['trial_end']) : null,
            'ends_at' => $subscription['current_period_end'] ? 
                \Carbon\Carbon::createFromTimestamp($subscription['current_period_end']) : null,
        ]);

        Log::info('Stripe webhook: Subscription updated', [
            'subscription_id' => $subscription['id'],
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Handle customer subscription deleted.
     */
    protected function handleCustomerSubscriptionDeleted($payload)
    {
        $subscription = $payload['data']['object'];
        
        $localSubscription = Subscription::where('stripe_id', $subscription['id'])->first();
        
        if (!$localSubscription) {
            Log::warning('Stripe webhook: Local subscription not found for deletion', [
                'subscription_id' => $subscription['id'],
            ]);
            return;
        }

        $localSubscription->update([
            'stripe_status' => 'canceled',
            'ends_at' => now(),
        ]);

        Log::info('Stripe webhook: Subscription deleted', [
            'subscription_id' => $subscription['id'],
        ]);
    }

    /**
     * Handle invoice payment succeeded.
     */
    protected function handleInvoicePaymentSucceeded($payload)
    {
        $invoice = $payload['data']['object'];
        $subscriptionId = $invoice['subscription'];
        
        if (!$subscriptionId) {
            return; // Not a subscription invoice
        }

        $localSubscription = Subscription::where('stripe_id', $subscriptionId)->first();
        
        if (!$localSubscription) {
            Log::warning('Stripe webhook: Local subscription not found for payment success', [
                'subscription_id' => $subscriptionId,
                'invoice_id' => $invoice['id'],
            ]);
            return;
        }

        // Update subscription status to active
        $localSubscription->update([
            'stripe_status' => 'active',
        ]);

        Log::info('Stripe webhook: Payment succeeded', [
            'subscription_id' => $subscriptionId,
            'invoice_id' => $invoice['id'],
            'amount' => $invoice['amount_paid'],
        ]);
    }

    /**
     * Handle invoice payment failed.
     */
    protected function handleInvoicePaymentFailed($payload)
    {
        $invoice = $payload['data']['object'];
        $subscriptionId = $invoice['subscription'];
        
        if (!$subscriptionId) {
            return; // Not a subscription invoice
        }

        $localSubscription = Subscription::where('stripe_id', $subscriptionId)->first();
        
        if (!$localSubscription) {
            Log::warning('Stripe webhook: Local subscription not found for payment failure', [
                'subscription_id' => $subscriptionId,
                'invoice_id' => $invoice['id'],
            ]);
            return;
        }

        // Update subscription status to past_due
        $localSubscription->update([
            'stripe_status' => 'past_due',
        ]);

        Log::warning('Stripe webhook: Payment failed', [
            'subscription_id' => $subscriptionId,
            'invoice_id' => $invoice['id'],
            'amount' => $invoice['amount_due'],
        ]);
    }

    /**
     * Handle customer subscription trial will end.
     */
    protected function handleCustomerSubscriptionTrialWillEnd($payload)
    {
        $subscription = $payload['data']['object'];
        
        $localSubscription = Subscription::where('stripe_id', $subscription['id'])->first();
        
        if (!$localSubscription) {
            Log::warning('Stripe webhook: Local subscription not found for trial ending', [
                'subscription_id' => $subscription['id'],
            ]);
            return;
        }

        $user = $localSubscription->user;
        
        // Send notification to user about trial ending
        // You can implement this notification as needed
        Log::info('Stripe webhook: Trial will end', [
            'subscription_id' => $subscription['id'],
            'user_id' => $user->id,
            'trial_end' => $subscription['trial_end'],
        ]);
    }

    /**
     * Handle webhook events that are not explicitly handled.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? 'unknown';

        Log::info('Stripe webhook received', [
            'type' => $eventType,
            'id' => $payload['id'] ?? null,
        ]);

        return parent::handleWebhook($request);
    }
}