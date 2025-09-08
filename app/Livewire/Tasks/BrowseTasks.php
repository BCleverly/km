<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\TargetUserType;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BrowseTasks extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $userType = null;

    #[Url]
    public ?int $difficulty = null;

    #[Url]
    public bool $showPremium = false;

    #[Url]
    public string $contentType = 'tasks'; // 'tasks' or 'outcomes'

    #[Computed]
    public function content(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if ($this->contentType === 'outcomes') {
            return $this->getOutcomes();
        }
        
        return $this->getTasks();
    }

    private function getTasks(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Task::with(['author', 'recommendedOutcomes', 'tags'])
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

    private function getOutcomes(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Outcome::with(['author', 'tags'])
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
    public function contentTypes(): array
    {
        return [
            'tasks' => 'Tasks',
            'outcomes' => 'Outcomes',
        ];
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

    public function updatedShowPremium(): void
    {
        $this->resetPage();
    }

    public function updatedContentType(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->userType = null;
        $this->difficulty = null;
        $this->showPremium = false;
        $this->contentType = 'tasks';
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.tasks.browse-tasks');
    }
}
