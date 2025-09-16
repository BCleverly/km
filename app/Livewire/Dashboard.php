<?php

namespace App\Livewire;

use App\Enums\CoupleTaskStatus;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function activeCoupleTask()
    {
        return auth()->user()->coupleTasksReceived()
            ->where('status', CoupleTaskStatus::Pending)
            ->with(['assignedBy', 'reward', 'punishment'])
            ->first();
    }

    #[Computed]
    public function canUseCoupleTasks(): bool
    {
        return auth()->user()->canAssignCoupleTasks() || auth()->user()->canReceiveCoupleTasks();
    }

    #[Computed]
    public function isDominant(): bool
    {
        return auth()->user()->isDominant();
    }

    #[Computed]
    public function isSubmissive(): bool
    {
        return auth()->user()->isSubmissive();
    }

    /**
     * Handle active outcomes refresh event from ManageWidget
     */
    #[On('active-outcomes-refresh')]
    public function handleActiveOutcomesRefresh()
    {
        // Force refresh of the component to update any computed properties
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app', [
                'title' => 'Dashboard - Kink Master'
            ]);
    }
}
