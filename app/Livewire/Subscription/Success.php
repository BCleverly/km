<?php

declare(strict_types=1);

namespace App\Livewire\Subscription;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Success extends Component
{
    public function mount(): void
    {
        // Redirect to dashboard after 3 seconds
        $this->redirect(route('app.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.subscription.success')
            ->layout('components.layouts.app', [
                'title' => 'Subscription Successful - Kink Master',
            ]);
    }
}
