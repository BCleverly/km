<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\ContentStatus;
use App\Livewire\Forms\CreateCustomTaskForm;
use App\Livewire\Forms\CreateCustomOutcomeForm;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\TargetUserType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TaskCommunityDashboard extends Component
{
    use WithPagination;

    public CreateCustomTaskForm $taskForm;

    public CreateCustomOutcomeForm $outcomeForm;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $userType = null;

    #[Url]
    public ?int $difficulty = null;

    #[Url]
    public ?int $status = null;

    #[Url]
    public bool $showPremium = false;

    #[Computed]
    public function tasks(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Task::with(['author', 'recommendedOutcomes'])
            ->approved()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->userType, function ($query) {
                $query->where('target_user_type', $this->userType);
            })
            ->when($this->difficulty, function ($query) {
                $query->where('difficulty_level', $this->difficulty);
            })
            ->when(!$this->showPremium, function ($query) {
                $query->where('is_premium', false);
            });

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    #[Computed]
    public function outcomes(): Collection
    {
        return Outcome::approved()
            ->when($this->userType, function ($query) {
                $query->where('target_user_type', $this->userType);
            })
            ->when($this->difficulty, function ($query) {
                $query->where('difficulty_level', $this->difficulty);
            })
            ->when(!$this->showPremium, function ($query) {
                $query->where('is_premium', false);
            })
            ->orderBy('intended_type')
            ->orderBy('title')
            ->get();
    }

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

    #[Computed]
    public function statusOptions(): array
    {
        return collect(ContentStatus::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedUserType(): void
    {
        $this->resetPage();
    }

    public function updatedDifficulty(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedShowPremium(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->userType = null;
        $this->difficulty = null;
        $this->status = null;
        $this->showPremium = false;
        $this->resetPage();
    }

    public function submitTask(): void
    {
        $this->taskForm->submit();
        
        session()->flash('message', 'Your task has been submitted for review!');
        $this->taskForm->resetForm();
    }

    public function submitOutcome(): void
    {
        $this->outcomeForm->submit();
        
        session()->flash('message', 'Your outcome has been submitted for review!');
        $this->outcomeForm->resetForm();
    }

    public function render(): View
    {
        return view('livewire.tasks.task-community-dashboard');
    }
}
