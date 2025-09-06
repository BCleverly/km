<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.tasks.dashboard')
            ->title('Tasks Dashboard');
    }
}