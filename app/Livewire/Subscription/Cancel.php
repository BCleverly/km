<?php

declare(strict_types=1);

namespace App\Livewire\Subscription;

use Livewire\Component;

class Cancel extends Component
{
    public function render()
    {
        return view('livewire.subscription.cancel')
            ->layout('components.layouts.app', [
                'title' => 'Subscription Cancelled - Kink Master',
            ]);
    }
}
