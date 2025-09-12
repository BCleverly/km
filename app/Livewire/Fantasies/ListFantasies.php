<?php

declare(strict_types=1);

namespace App\Livewire\Fantasies;

use App\Models\Fantasy;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ListFantasies extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public bool $showPremium = false;

    #[Computed]
    public function fantasies(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Fantasy::with(['user.profile', 'reactions', 'tags'])
            ->approved()
            ->when($this->search, function ($query) {
                $query->where('content', 'like', '%' . $this->search . '%');
            })
            ->when(!$this->showPremium, function ($query) {
                $query->where('is_premium', false);
            });

        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function reportFantasy(int $fantasyId): void
    {
        if (!auth()->check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to report content',
                'type' => 'error',
            ]);
            return;
        }

        $fantasy = Fantasy::find($fantasyId);
        
        if (!$fantasy) {
            $this->dispatch('show-notification', [
                'message' => 'Fantasy not found',
                'type' => 'error',
            ]);
            return;
        }

        // Increment report count
        $fantasy->incrementReportCount();

        $this->dispatch('show-notification', [
            'message' => 'Fantasy reported successfully. Our moderation team will review it.',
            'type' => 'success',
        ]);
    }

    public function updatedSearch(): void
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
        $this->showPremium = false;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.fantasies.list-fantasies')
            ->layout('components.layouts.app', [
                'title' => 'Fantasies - Kink Master'
            ]);
    }
}