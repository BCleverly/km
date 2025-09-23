<?php

declare(strict_types=1);

namespace App\Livewire\DesireDiscovery;

use App\Enums\DesireItemType;
use App\Enums\DesireResponseType;
use App\Models\DesireItem;
use App\Models\PartnerDesireResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DesireDiscovery extends Component
{
    // Active tab/section
    public string $activeTab = 'explore';

    // Exploration properties
    public ?DesireItem $currentItem = null;

    public int $currentIndex = 0;

    public array $items = [];

    public ?DesireItemType $filterType = null;

    public bool $showOnlyUnresponded = true;

    // Historical review properties
    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    // Submission properties
    public string $title = '';

    public string $description = '';

    public ?DesireItemType $item_type = null;

    public ?int $category_id = null;

    public int $difficulty_level = 5;

    public string $target_user_type = 'any';

    public array $tags = [];

    public string $newTag = '';

    public function mount(): void
    {
        // Set active tab based on current route
        $currentRoute = request()->route()?->getName();
        if ($currentRoute && str_contains($currentRoute, 'submit')) {
            $this->activeTab = 'submit';
        } elseif ($currentRoute && str_contains($currentRoute, 'compatibility')) {
            $this->activeTab = 'compatibility';
        } elseif ($currentRoute && str_contains($currentRoute, 'history')) {
            $this->activeTab = 'history';
        } else {
            $this->activeTab = 'explore';
        }

        $this->loadItems();
        $this->item_type = DesireItemType::Fetish;
        $this->target_user_type = (string) $this->user->user_type->value;
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Load data when switching tabs
        if ($tab === 'explore') {
            $this->loadItems();
        }
    }

    #[Computed]
    public function user(): \App\Models\User
    {
        return Auth::user()->load('profile');
    }

    #[Computed]
    public function partner(): ?\App\Models\User
    {
        return $this->user->partner?->load('profile');
    }

    #[Computed]
    public function hasPartner(): bool
    {
        return $this->partner !== null || $this->user->hasRole('Admin');
    }

    #[Computed]
    public function itemTypes(): array
    {
        return collect(DesireItemType::cases())
            ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
            ->toArray();
    }

    #[Computed]
    public function categories(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\DesireCategory::all();
    }

    // Exploration methods
    public function loadItems(): void
    {
        $query = DesireItem::approved()
            ->forUserType($this->user->user_type)
            ->with(['author.profile', 'category']);

        if ($this->filterType) {
            $query->forItemType($this->filterType);
        }

        if ($this->showOnlyUnresponded) {
            $query->notRespondedByUser($this->user);
        }

        $this->items = $query->get()->toArray();
        $this->currentIndex = 0;
        $this->setCurrentItem();
    }

    public function setCurrentItem(): void
    {
        if (empty($this->items)) {
            $this->currentItem = null;

            return;
        }

        $this->currentItem = DesireItem::find($this->items[$this->currentIndex]['id']);
    }

    public function respond(DesireResponseType $responseType): void
    {
        if (! $this->currentItem) {
            return;
        }

        PartnerDesireResponse::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'partner_id' => $this->partner?->id,
                'desire_item_id' => $this->currentItem->id,
            ],
            [
                'response_type' => $responseType,
            ]
        );

        $this->nextItem();
    }

    public function nextItem(): void
    {
        if ($this->currentIndex < count($this->items) - 1) {
            $this->currentIndex++;
            $this->setCurrentItem();
        } else {
            $this->loadItems();
        }
    }

    // Historical review methods
    #[Computed]
    public function historicalItems(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = DesireItem::approved()
            ->forUserType($this->user->user_type)
            ->with(['author.profile', 'category', 'partnerResponses' => function ($q) {
                $userIds = [$this->user->id];
                if ($this->partner) {
                    $userIds[] = $this->partner->id;
                }
                $q->whereIn('user_id', $userIds);
            }])
            ->whereHas('partnerResponses', function ($q) {
                $q->where('user_id', $this->user->id);
                if ($this->partner) {
                    $q->orWhere('user_id', $this->partner->id);
                }
            });

        if ($this->filterType) {
            $query->forItemType($this->filterType);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    #[Computed]
    public function responseStats(): array
    {
        $userResponses = PartnerDesireResponse::where('user_id', $this->user->id)->count();
        $partnerResponses = $this->partner ? PartnerDesireResponse::where('user_id', $this->partner->id)->count() : 0;
        $totalItems = DesireItem::approved()->forUserType($this->user->user_type)->count();

        return [
            'user_responses' => $userResponses,
            'partner_responses' => $partnerResponses,
            'total_items' => $totalItems,
            'user_percentage' => $totalItems > 0 ? round(($userResponses / $totalItems) * 100, 1) : 0,
            'partner_percentage' => $totalItems > 0 ? round(($partnerResponses / $totalItems) * 100, 1) : 0,
        ];
    }

    // Compatibility methods
    #[Computed]
    public function compatibilityStats(): array
    {
        if (! $this->hasPartner) {
            return [
                'total_items' => 0,
                'both_responded' => 0,
                'matches' => 0,
                'compatibility_percentage' => 0,
                'yes_matches' => 0,
                'maybe_matches' => 0,
                'no_matches' => 0,
            ];
        }

        $totalResponses = PartnerDesireResponse::where('user_id', $this->user->id)
            ->orWhere('partner_id', $this->user->id)
            ->count();

        if ($totalResponses === 0) {
            return [
                'total_items' => 0,
                'both_responded' => 0,
                'matches' => 0,
                'compatibility_percentage' => 0,
                'yes_matches' => 0,
                'maybe_matches' => 0,
                'no_matches' => 0,
            ];
        }

        $query = DesireItem::whereHas('partnerResponses', function ($query) {
            $query->where('user_id', $this->user->id);
        });

        if ($this->partner) {
            $query->whereHas('partnerResponses', function ($query) {
                $query->where('user_id', $this->partner->id);
            });
        }

        $userIds = [$this->user->id];
        if ($this->partner) {
            $userIds[] = $this->partner->id;
        }

        $bothRespondedItems = $query->with(['partnerResponses' => function ($query) use ($userIds) {
            $query->whereIn('user_id', $userIds);
        }])->get();

        $matches = 0;
        $yesMatches = 0;
        $maybeMatches = 0;
        $noMatches = 0;

        foreach ($bothRespondedItems as $item) {
            $userResponse = $item->partnerResponses->where('user_id', $this->user->id)->first();
            $partnerResponse = $this->partner ? $item->partnerResponses->where('user_id', $this->partner->id)->first() : null;

            if ($userResponse && $partnerResponse && $userResponse->response_type === $partnerResponse->response_type) {
                $matches++;

                switch ($userResponse->response_type) {
                    case DesireResponseType::Yes:
                        $yesMatches++;
                        break;
                    case DesireResponseType::Maybe:
                        $maybeMatches++;
                        break;
                    case DesireResponseType::No:
                        $noMatches++;
                        break;
                }
            }
        }

        $compatibilityPercentage = $bothRespondedItems->count() > 0 ? round(($matches / $bothRespondedItems->count()) * 100, 1) : 0;

        return [
            'total_items' => $bothRespondedItems->count(),
            'both_responded' => $bothRespondedItems->count(),
            'matches' => $matches,
            'compatibility_percentage' => $compatibilityPercentage,
            'yes_matches' => $yesMatches,
            'maybe_matches' => $maybeMatches,
            'no_matches' => $noMatches,
        ];
    }

    #[Computed]
    public function compatibilityItems(): \Illuminate\Database\Eloquent\Collection
    {
        if (! $this->hasPartner) {
            return collect();
        }

        $query = DesireItem::whereHas('partnerResponses', function ($q) {
            $q->where('user_id', $this->user->id);
        });

        if ($this->partner) {
            $query->whereHas('partnerResponses', function ($q) {
                $q->where('user_id', $this->partner->id);
            });
        }

        $query->with(['partnerResponses' => function ($q) {
            $userIds = [$this->user->id];
            if ($this->partner) {
                $userIds[] = $this->partner->id;
            }
            $q->whereIn('user_id', $userIds);
        }, 'category']);

        if ($this->filterType) {
            $query->forItemType($this->filterType);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->get();
    }

    // Submission methods
    public function submit(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'item_type' => 'required',
            'difficulty_level' => 'required|integer|min:1|max:10',
            'target_user_type' => 'required',
        ]);

        $desireItem = DesireItem::create([
            'title' => $this->title,
            'description' => $this->description,
            'item_type' => $this->item_type,
            'category_id' => $this->category_id,
            'difficulty_level' => $this->difficulty_level,
            'target_user_type' => \App\TargetUserType::from((int) $this->target_user_type),
            'user_id' => $this->user->id,
            'status' => \App\ContentStatus::Pending,
            'is_premium' => false,
        ]);

        $this->reset(['title', 'description', 'category_id', 'tags', 'newTag']);
        $this->item_type = DesireItemType::Fetish;
        $this->difficulty_level = 5;
        $this->target_user_type = (string) $this->user->user_type->value;

        session()->flash('message', 'Desire item submitted successfully! It will be reviewed before being added to the community.');
    }

    public function addTag(): void
    {
        if ($this->newTag && ! in_array($this->newTag, $this->tags)) {
            $this->tags[] = $this->newTag;
            $this->newTag = '';
        }
    }

    public function removeTag(string $tag): void
    {
        $this->tags = array_values(array_filter($this->tags, fn ($t) => $t !== $tag));
    }

    public function updatedFilterType(): void
    {
        if ($this->activeTab === 'explore') {
            $this->loadItems();
        }
    }

    public function updatedSearch(): void
    {
        // Reset pagination when search changes
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        return view('livewire.desire-discovery.desire-discovery')
            ->layout('components.layouts.app', [
                'title' => 'Desire Discovery',
            ]);
    }
}
