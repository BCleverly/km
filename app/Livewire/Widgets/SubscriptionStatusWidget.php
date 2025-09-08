<?php

namespace App\Livewire\Widgets;

use App\Enums\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionStatusWidget extends Component
{
    public $user;
    public $subscription;
    public $currentPlan;

    public function mount()
    {
        $this->user = Auth::user();
        $this->subscription = $this->user->activeSubscription();
        $this->currentPlan = $this->user->getCurrentPlan();
    }

    public function render()
    {
        return view('livewire.widgets.subscription-status-widget');
    }
}