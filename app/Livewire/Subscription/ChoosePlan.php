<?php

declare(strict_types=1);

namespace App\Livewire\Subscription;

use App\Enums\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChoosePlan extends Component
{
    public ?SubscriptionPlan $selectedPlan = null;
    public bool $isLoading = false;

    public function mount(): void
    {
        $user = Auth::user();
        
        // If user has lifetime subscription, redirect to dashboard
        if ($user->hasLifetimeSubscription()) {
            $this->redirect(route('app.dashboard'), navigate: true);
        }
        
        // If user is on trial and hasn't selected a plan yet, allow them to choose
        // If user has a paid subscription, allow them to upgrade/downgrade
    }

    public function selectPlan(int $planValue): void
    {
        $this->selectedPlan = SubscriptionPlan::from($planValue);
    }

    public function subscribe(): void
    {
        if (!$this->selectedPlan) {
            $this->addError('plan', 'Please select a subscription plan.');
            return;
        }

        $user = Auth::user();
        $currentPlan = $user->subscription_plan;

        // Don't allow subscribing to the same plan
        if ($currentPlan === $this->selectedPlan) {
            $this->addError('plan', 'You are already subscribed to this plan.');
            return;
        }

        $this->isLoading = true;

        try {
            $subscriptionService = app(SubscriptionService::class);
            
            // Handle different subscription scenarios
            if ($user->subscription_plan->isPaid()) {
                // User is upgrading/downgrading existing subscription
                $checkoutUrl = $subscriptionService->createCheckoutSession(
                    $user,
                    $this->selectedPlan
                );
            } else {
                // User is starting a new subscription (from trial or free)
                $checkoutUrl = $subscriptionService->createCheckoutSession(
                    $user,
                    $this->selectedPlan
                );
            }

            $this->redirect($checkoutUrl);
        } catch (\Exception $e) {
            $this->isLoading = false;
            $this->addError('subscription', 'Failed to process subscription. Please try again.');
        }
    }

    public function getCurrentPlanProperty(): SubscriptionPlan
    {
        return Auth::user()?->subscription_plan ?? SubscriptionPlan::Free;
    }

    public function getCurrentPlanStatusProperty(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'is_on_trial' => false,
                'trial_ends_at' => null,
                'has_paid_subscription' => false,
                'subscription_ends_at' => null,
                'is_lifetime' => false,
            ];
        }
        
        return [
            'is_on_trial' => $user->isOnTrial(),
            'trial_ends_at' => $user->trial_ends_at,
            'has_paid_subscription' => $user->subscription_plan->isPaid(),
            'subscription_ends_at' => $user->subscription_ends_at,
            'is_lifetime' => $user->hasLifetimeSubscription(),
        ];
    }

    public function getPlansProperty(): array
    {
        $currentPlan = $this->currentPlan;
        $currentPlanStatus = $this->currentPlanStatus;
        
        return [
            SubscriptionPlan::Solo->value => [
                'plan' => SubscriptionPlan::Solo,
                'name' => 'Solo',
                'price' => SubscriptionPlan::Solo->priceFormatted(),
                'description' => 'Perfect for individuals',
                'features' => SubscriptionPlan::Solo->features(),
                'popular' => false,
                'is_current' => $currentPlan === SubscriptionPlan::Solo,
                'is_upgrade' => $currentPlan->value < SubscriptionPlan::Solo->value,
                'is_downgrade' => $currentPlan->value > SubscriptionPlan::Solo->value,
            ],
            SubscriptionPlan::Premium->value => [
                'plan' => SubscriptionPlan::Premium,
                'name' => 'Premium',
                'price' => SubscriptionPlan::Premium->priceFormatted(),
                'description' => 'Most popular choice',
                'features' => SubscriptionPlan::Premium->features(),
                'popular' => true,
                'is_current' => $currentPlan === SubscriptionPlan::Premium,
                'is_upgrade' => $currentPlan->value < SubscriptionPlan::Premium->value,
                'is_downgrade' => $currentPlan->value > SubscriptionPlan::Premium->value,
            ],
            SubscriptionPlan::Couple->value => [
                'plan' => SubscriptionPlan::Couple,
                'name' => 'Couple',
                'price' => SubscriptionPlan::Couple->priceFormatted(),
                'description' => 'For couples to share',
                'features' => SubscriptionPlan::Couple->features(),
                'popular' => false,
                'is_current' => $currentPlan === SubscriptionPlan::Couple,
                'is_upgrade' => $currentPlan->value < SubscriptionPlan::Couple->value,
                'is_downgrade' => $currentPlan->value > SubscriptionPlan::Couple->value,
            ],
            SubscriptionPlan::Lifetime->value => [
                'plan' => SubscriptionPlan::Lifetime,
                'name' => 'Lifetime',
                'price' => SubscriptionPlan::Lifetime->priceFormatted(),
                'description' => 'One-time payment',
                'features' => SubscriptionPlan::Lifetime->features(),
                'popular' => false,
                'is_current' => $currentPlan === SubscriptionPlan::Lifetime,
                'is_upgrade' => $currentPlan->value < SubscriptionPlan::Lifetime->value,
                'is_downgrade' => false, // Can't downgrade from lifetime
            ],
        ];
    }

    public function render()
    {
        return view('livewire.subscription.choose-plan')
            ->layout('components.layouts.app', [
                'title' => 'Choose Your Plan - Kink Master',
            ]);
    }
}
