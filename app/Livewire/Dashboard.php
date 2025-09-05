<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app', [
                'title' => 'Dashboard - Kink Master'
            ]);
    }
}
