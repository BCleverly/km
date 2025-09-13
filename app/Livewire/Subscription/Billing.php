<?php

declare(strict_types=1);

namespace App\Livewire\Subscription;

use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Billing extends Component
{
    public function openBillingPortal(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $billingUrl = $subscriptionService->getBillingPortalUrl(Auth::user());
        
        $this->redirect($billingUrl);
    }

    public function cancelSubscription(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $cancelled = $subscriptionService->cancelSubscription(Auth::user());
        
        if ($cancelled) {
            session()->flash('success', 'Your subscription has been cancelled. You will continue to have access until the end of your current billing period.');
        } else {
            session()->flash('error', 'Failed to cancel subscription. Please try again or contact support.');
        }
    }

    public function resumeSubscription(): void
    {
        $subscriptionService = app(SubscriptionService::class);
        $resumed = $subscriptionService->resumeSubscription(Auth::user());
        
        if ($resumed) {
            session()->flash('success', 'Your subscription has been resumed.');
        } else {
            session()->flash('error', 'Failed to resume subscription. Please try again or contact support.');
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        return view('livewire.subscription.billing', [
            'user' => $user,
            'subscription' => $user->subscription(),
            'currentPlan' => $user->getCurrentPlan(),
        ])->layout('components.layouts.app', [
            'title' => 'Billing & Subscription - Kink Master',
        ]);
    }
}
