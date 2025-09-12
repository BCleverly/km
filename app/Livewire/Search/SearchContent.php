<?php

declare(strict_types=1);

namespace App\Livewire\Search;

use App\Contracts\SearchStrategyInterface;
use App\Models\Fantasy;
use App\Models\Story;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Models\Tag;
use App\ContentStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Search')]
class SearchContent extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $query = '';

    #[Url]
    public string $type = 'all'; // all, stories, fantasies, tasks, outcomes, tags

    #[Url]
    public bool $premium = false;

    public function mount(?string $q = null): void
    {
        // The #[Url(as: 'q')] attribute should handle this automatically
        // But we'll keep this as a fallback for direct route parameters
        if ($q && empty($this->query)) {
            $this->query = $q;
        }
    }

    #[Computed]
    public function results(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (empty(trim($this->query))) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                15,
                1
            );
        }

        $searchStrategy = app(SearchStrategyInterface::class);
        return $searchStrategy->search($this->query, $this->type, $this->premium);
    }

    #[Computed]
    public function resultCounts(): array
    {
        if (empty(trim($this->query))) {
            return [
                'all' => 0,
                'stories' => 0,
                'fantasies' => 0,
                'tasks' => 0,
                'outcomes' => 0,
                'tags' => 0,
            ];
        }

        $searchStrategy = app(SearchStrategyInterface::class);
        return $searchStrategy->getResultCounts($this->query, $this->premium);
    }

    #[Computed]
    public function recommendedStories()
    {
        $cacheKey = "search.recommended.stories.{$this->premium}";
        
        return Cache::flexible($cacheKey, [900, 1800], function () {
            return Story::with(['user.profile', 'tags'])
                ->approved()
                ->when(!$this->premium, fn($q) => $q->where('is_premium', false))
                ->where('created_at', '>=', now()->subDays(5))
                ->orderByDesc('view_count')
                ->limit(5)
                ->get();
        });
    }

    #[Computed]
    public function recommendedFantasies()
    {
        $cacheKey = "search.recommended.fantasies.{$this->premium}";
        
        return Cache::flexible($cacheKey, [900, 1800], function () {
            return Fantasy::with(['user.profile', 'tags'])
                ->approved()
                ->when(!$this->premium, fn($q) => $q->where('is_premium', false))
                ->where('created_at', '>=', now()->subDays(5))
                ->orderByDesc('view_count')
                ->limit(5)
                ->get();
        });
    }

    #[Computed]
    public function recommendedTasks()
    {
        $cacheKey = "search.recommended.tasks.{$this->premium}";
        
        return Cache::flexible($cacheKey, [900, 1800], function () {
            return Task::with(['author', 'tags', 'assignedTasks'])
                ->approved()
                ->when(!$this->premium, fn($q) => $q->where('is_premium', false))
                ->where('created_at', '>=', now()->subDays(5))
                ->orderByDesc('view_count')
                ->limit(5)
                ->get();
        });
    }

    #[Computed]
    public function popularTasksByCompletion()
    {
        $cacheKey = "search.popular.tasks.completion.{$this->premium}";
        
        return Cache::flexible($cacheKey, [900, 1800], function () {
            return Task::with(['author', 'tags', 'assignedTasks'])
                ->approved()
                ->when(!$this->premium, fn($q) => $q->where('is_premium', false))
                ->where('created_at', '>=', now()->subDays(5))
                ->withCount(['assignedTasks as completed_count' => function ($query) {
                    $query->where('status', \App\TaskStatus::Completed);
                }])
                ->withCount(['assignedTasks as total_assignments'])
                ->having('total_assignments', '>', 0)
                ->orderByRaw('(completed_count / total_assignments) DESC')
                ->limit(5)
                ->get();
        });
    }

    #[Computed]
    public function challengingTasks()
    {
        $cacheKey = "search.challenging.tasks.{$this->premium}";
        
        return Cache::flexible($cacheKey, [900, 1800], function () {
            return Task::with(['author', 'tags', 'assignedTasks'])
                ->approved()
                ->when(!$this->premium, fn($q) => $q->where('is_premium', false))
                ->where('created_at', '>=', now()->subDays(5))
                ->withCount(['assignedTasks as completed_count' => function ($query) {
                    $query->where('status', \App\TaskStatus::Completed);
                }])
                ->withCount(['assignedTasks as total_assignments'])
                ->having('total_assignments', '>', 0)
                ->orderByRaw('(completed_count / total_assignments) ASC')
                ->limit(5)
                ->get();
        });
    }

    public function updatedQuery(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedPremium(): void
    {
        $this->resetPage();
        // Clear recommended content cache when premium filter changes
        $this->clearRecommendedCache();
    }

    public function clearSearch(): void
    {
        $this->query = '';
        $this->type = 'all';
        $this->premium = false;
        $this->resetPage();
        $this->clearRecommendedCache();
    }

    private function clearRecommendedCache(): void
    {
        $cacheKeys = [
            "search.recommended.stories.{$this->premium}",
            "search.recommended.fantasies.{$this->premium}",
            "search.recommended.tasks.{$this->premium}",
            "search.popular.tasks.completion.{$this->premium}",
            "search.challenging.tasks.{$this->premium}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    public function render(): View
    {
        return view('livewire.search.search-content')
            ->layout('components.layouts.app', [
                'title' => 'Search - Kink Master'
            ]);
    }
}