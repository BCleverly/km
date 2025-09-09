<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Community Task Library')] 
class TaskCommunityDashboard extends Component
{

    public function render(): View
    {
        return view('livewire.tasks.task-community-dashboard');
    }
}
