<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\ContentStatus;
use App\Livewire\Forms\CreateCustomOutcomeForm;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SubmitOutcome extends Component
{
    public CreateCustomOutcomeForm $outcomeForm;

    #[Computed]
    public function userTypes(): array
    {
        return collect(TargetUserType::cases())
            ->mapWithKeys(fn($type) => [$type->value => $type->label()])
            ->toArray();
    }

    #[Computed]
    public function difficultyLevels(): array
    {
        return [
            1 => 'Very Easy',
            2 => 'Easy',
            3 => 'Medium',
            4 => 'Hard',
            5 => 'Very Hard',
            6 => 'Extreme',
        ];
    }

    public function submitOutcome(): void
    {
        $this->outcomeForm->submit();
        
        session()->flash('message', 'Your outcome has been submitted for review!');
        $this->outcomeForm->resetForm();
    }

    public function render(): View
    {
        return view('livewire.tasks.submit-outcome');
    }
}
