<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\SearchStrategyInterface;
use App\Models\Fantasy;
use App\Models\Story;
use App\Models\Tasks\Task;
use App\Models\Tasks\Outcome;
use App\Models\Models\Tag;
use App\ContentStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class MySqlSearchStrategy implements SearchStrategyInterface
{
    public function search(string $query, string $type = 'all', bool $premium = false): LengthAwarePaginator
    {
        $results = collect();

        switch ($type) {
            case 'stories':
                $results = $this->searchStories($query, $premium);
                break;
            case 'fantasies':
                $results = $this->searchFantasies($query, $premium);
                break;
            case 'tasks':
                $results = $this->searchTasks($query, $premium);
                break;
            case 'outcomes':
                $results = $this->searchOutcomes($query, $premium);
                break;
            case 'tags':
                $results = $this->searchTags($query);
                break;
            case 'all':
            default:
                $results = $this->searchAll($query, $premium);
                break;
        }

        return $this->paginateResults($results);
    }

    public function getResultCounts(string $query, bool $premium = false): array
    {
        return [
            'all' => $this->searchAll($query, $premium)->count(),
            'stories' => $this->searchStories($query, $premium)->count(),
            'fantasies' => $this->searchFantasies($query, $premium)->count(),
            'tasks' => $this->searchTasks($query, $premium)->count(),
            'outcomes' => $this->searchOutcomes($query, $premium)->count(),
            'tags' => $this->searchTags($query)->count(),
        ];
    }

    private function searchAll(string $query, bool $premium = false): SupportCollection
    {
        $results = collect();

        // Search stories
        $stories = $this->searchStories($query, $premium);
        $results = $results->merge($stories->map(fn($story) => [
            'type' => 'story',
            'id' => $story->id,
            'title' => $story->title,
            'content' => $story->summary,
            'url' => route('app.stories.show', $story->slug),
            'author' => $story->user->name,
            'created_at' => $story->created_at,
            'is_premium' => $story->is_premium,
            'tags' => $story->tags,
        ]));

        // Search fantasies
        $fantasies = $this->searchFantasies($query, $premium);
        $results = $results->merge($fantasies->map(fn($fantasy) => [
            'type' => 'fantasy',
            'id' => $fantasy->id,
            'title' => 'Fantasy',
            'content' => $fantasy->content,
            'url' => route('app.fantasies.index'),
            'author' => $fantasy->is_anonymous ? 'Anonymous' : $fantasy->user->name,
            'created_at' => $fantasy->created_at,
            'is_premium' => $fantasy->is_premium,
            'tags' => $fantasy->tags,
        ]));

        // Search tasks
        $tasks = $this->searchTasks($query, $premium);
        $results = $results->merge($tasks->map(fn($task) => [
            'type' => 'task',
            'id' => $task->id,
            'title' => $task->title,
            'content' => $task->description,
            'url' => route('app.tasks.community'),
            'author' => $task->author->name,
            'created_at' => $task->created_at,
            'is_premium' => $task->is_premium,
            'tags' => $task->tags,
        ]));

        // Search outcomes
        $outcomes = $this->searchOutcomes($query, $premium);
        $results = $results->merge($outcomes->map(fn($outcome) => [
            'type' => 'outcome',
            'id' => $outcome->id,
            'title' => $outcome->title,
            'content' => $outcome->description,
            'url' => route('app.tasks.community'),
            'author' => $outcome->author->name,
            'created_at' => $outcome->created_at,
            'is_premium' => $outcome->is_premium,
            'tags' => $outcome->tags,
        ]));

        // Search tags
        $tags = $this->searchTags($query);
        $results = $results->merge($tags->map(fn($tag) => [
            'type' => 'tag',
            'id' => $tag->id,
            'title' => $tag->name,
            'content' => "Tag: {$tag->name}",
            'url' => route('app.tasks.community'),
            'author' => 'System',
            'created_at' => $tag->created_at,
            'is_premium' => false,
            'tags' => collect([$tag]),
        ]));

        return $results->sortByDesc('created_at');
    }

    private function searchStories(string $query, bool $premium = false): Collection
    {
        $searchQuery = Story::with(['user.profile', 'tags'])
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('summary', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', "%{$query}%");
                  });
            });

        if (!$premium) {
            $searchQuery->where('is_premium', false);
        }

        return $searchQuery->get();
    }

    private function searchFantasies(string $query, bool $premium = false): Collection
    {
        $searchQuery = Fantasy::with(['user.profile', 'tags'])
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('content', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', "%{$query}%");
                  });
            });

        if (!$premium) {
            $searchQuery->where('is_premium', false);
        }

        return $searchQuery->get();
    }

    private function searchTasks(string $query, bool $premium = false): Collection
    {
        $searchQuery = Task::with(['author', 'tags'])
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', "%{$query}%");
                  });
            });

        if (!$premium) {
            $searchQuery->where('is_premium', false);
        }

        return $searchQuery->get();
    }

    private function searchOutcomes(string $query, bool $premium = false): Collection
    {
        $searchQuery = Outcome::with(['author', 'tags'])
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', "%{$query}%");
                  });
            });

        if (!$premium) {
            $searchQuery->where('is_premium', false);
        }

        return $searchQuery->get();
    }

    private function searchTags(string $query): Collection
    {
        return Tag::where('name', 'like', "%{$query}%")
            ->get();
    }

    private function paginateResults(SupportCollection $results): LengthAwarePaginator
    {
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $results->slice($offset, $perPage)->values();

        return new ConcreteLengthAwarePaginator(
            $items,
            $results->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }
}
