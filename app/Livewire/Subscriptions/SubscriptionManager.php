<?php

namespace App\Livewire\Subscriptions;

use App\Actions\Subscriptions\CancelSubscriptionAction;
use App\Actions\Subscriptions\CreateSubscriptionAction;
use App\Actions\Subscriptions\UpdateSubscriptionAction;
use App\Enums\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionManager extends Component
{
    public User $user;
    public $currentPlan;
    public $subscription;
    public $showUpgradeModal = false;
    public $selectedPlan;
    public $paymentMethodId;
    public $isProcessing = false;

    protected $listeners = ['subscriptionUpdated' => 'refreshSubscription'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->refreshSubscription();
    }

    public function refreshSubscription()
    {
        $this->subscription = $this->user->activeSubscription();
        $this->currentPlan = $this->user->getCurrentPlan();
    }

    public function showUpgradeModal(SubscriptionPlan $plan)
    {
        $this->selectedPlan = $plan;
        $this->showUpgradeModal = true;
    }

    public function hideUpgradeModal()
    {
        $this->showUpgradeModal = false;
        $this->selectedPlan = null;
        $this->paymentMethodId = null;
        $this->isProcessing = false;
    }

    public function createSubscription()
    {
        $this->validate([
            'selectedPlan' => 'required',
            'paymentMethodId' => 'required_if:selectedPlan,lifetime',
        ]);

        $this->isProcessing = true;

        $result = CreateSubscriptionAction::run(
            $this->user,
            $this->selectedPlan,
            $this->paymentMethodId
        );

        if ($result['success']) {
            $this->refreshSubscription();
            $this->hideUpgradeModal();
            
            session()->flash('success', $result['message']);
            $this->dispatch('subscriptionUpdated');
        } else {
            session()->flash('error', $result['message']);
        }

        $this->isProcessing = false;
    }

    public function updateSubscription(SubscriptionPlan $newPlan)
    {
        $this->isProcessing = true;

        $result = UpdateSubscriptionAction::run($this->user, $newPlan);

        if ($result['success']) {
            $this->refreshSubscription();
            session()->flash('success', $result['message']);
            $this->dispatch('subscriptionUpdated');
        } else {
            session()->flash('error', $result['message']);
        }

        $this->isProcessing = false;
    }

    public function cancelSubscription($immediately = false)
    {
        $this->isProcessing = true;

        $result = CancelSubscriptionAction::run($this->user, $immediately);

        if ($result['success']) {
            $this->refreshSubscription();
            session()->flash('success', $result['message']);
            $this->dispatch('subscriptionUpdated');
        } else {
            session()->flash('error', $result['message']);
        }

        $this->isProcessing = false;
    }

    public function resumeSubscription()
    {
        $this->isProcessing = true;

        $result = CancelSubscriptionAction::resume($this->user);

        if ($result['success']) {
            $this->refreshSubscription();
            session()->flash('success', $result['message']);
            $this->dispatch('subscriptionUpdated');
        } else {
            session()->flash('error', $result['message']);
        }

        $this->isProcessing = false;
    }

    public function getAvailablePlansProperty()
    {
        return collect(SubscriptionPlan::cases())
            ->filter(function (SubscriptionPlan $plan) {
                // Don't show current plan
                return $plan !== $this->currentPlan;
            })
            ->values();
    }

    public function canUpgradeTo(SubscriptionPlan $plan): bool
    {
        if (!$this->subscription) {
            return true; // Can upgrade from free
        }

        // Don't allow downgrading from lifetime
        if ($this->currentPlan === SubscriptionPlan::LIFETIME) {
            return false;
        }

        // Don't allow changing to same plan
        if ($this->currentPlan === $plan) {
            return false;
        }

        return true;
    }

    public function render()
    {
        return view('livewire.subscriptions.subscription-manager');
    }
}