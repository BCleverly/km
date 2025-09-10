<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ManageSubscription extends Component
{
    public function render()
    {
        $user = Auth::user();
        
        return view('livewire.subscription.manage-subscription', [
            'user' => $user,
            'subscriptionStatus' => $user->subscription_status,
            'isPartOfCouple' => $user->isPartOfCouple(),
            'partner' => $user->getCouplePartner(),
        ])->layout('components.layouts.app');
    }

    public function subscribe()
    {
        // Redirect to Stripe checkout or subscription page
        // This would typically redirect to a Stripe checkout session
        return redirect()->route('stripe.checkout');
    }

    public function manageBilling()
    {
        // Redirect to Stripe customer portal
        // This would typically redirect to Stripe customer portal
        return redirect()->route('stripe.portal');
    }
}