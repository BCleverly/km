<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Homepage extends Component
{
    public bool $showMobileMenu = false;

    public function toggleMobileMenu(): void
    {
        $this->showMobileMenu = !$this->showMobileMenu;
    }

    public function render()
    {
        return view('livewire.homepage')
            ->layout('components.layouts.guest', [
                'title' => 'Kink Master - Your Ultimate Task & Reward Community'
            ]);
    }
}
