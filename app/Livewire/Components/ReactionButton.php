<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\Models\Fantasy;
use App\Models\Story;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ReactionButton extends Component
{
    #[Locked]
    public string $modelType;

    #[Locked]
    public int $modelId;

    public function mount(string $modelType, int $modelId): void
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
    }

    /**
     * Generate a unique cache key for this model instance.
     */
    private function getCacheKey(string $suffix = ''): string
    {
        $model = $this->getModel();
        if (!$model) {
            return "reactions_{$this->modelType}_{$this->modelId}_{$suffix}";
        }
        
        return 'reactions_' . get_class($model) . '_' . $model->id . '_' . $suffix;
    }

    /**
     * Clear all cached reaction data for this model instance.
     */
    private function clearReactionCache(): void
    {
        $model = $this->getModel();
        if (!$model) {
            return;
        }

        // Clear summary cache
        Cache::forget($this->getCacheKey('summary'));

        // Clear all user-specific caches for this model
        // We need to clear caches for all users since we don't know which users have cached data
        $cachePrefix = 'reactions_' . get_class($model) . '_' . $model->id . '_user_';
        
        // Get all cache keys that match our pattern and clear them
        // Note: This is a simplified approach. In production, you might want to use cache tags
        // or maintain a list of users who have reacted to this model
        $this->clearCacheByPattern($cachePrefix);
    }

    /**
     * Clear cache entries that match a given pattern.
     * Note: This is a simplified implementation. In production, consider using cache tags.
     */
    private function clearCacheByPattern(string $pattern): void
    {
        // For now, we'll clear the current user's cache if they're authenticated
        if (auth()->check()) {
            $userId = auth()->id();
            Cache::forget($this->getCacheKey("user_{$userId}"));
        }
    }

    public function getModel(): ?Model
    {
        try {
            return match ($this->modelType) {
                'task', 'tasks' => Task::find($this->modelId),
                'outcome', 'outcomes' => Outcome::find($this->modelId),
                'fantasy', 'fantasies' => Fantasy::find($this->modelId),
                'story', 'stories' => Story::find($this->modelId),
                'comment', 'comments' => Comment::find($this->modelId),
                default => null,
            };
        } catch (\Exception $e) {
            \Log::error('ReactionButton: Error finding model', [
                'modelType' => $this->modelType,
                'modelId' => $this->modelId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getReactions()
    {
        $model = $this->getModel();

        if (! $model) {
            return collect();
        }

        $cacheKey = $this->getCacheKey('summary');

        return Cache::flexible($cacheKey, [300, 600], function () use ($model) {
            $summary = $model->reactionSummary();

            return collect($summary)->map(function ($count, $type) {
                return [
                    'type' => $type,
                    'count' => $count,
                ];
            });
        });
    }

    public function getUserReaction()
    {
        if (! auth()->check()) {
            return null;
        }

        $model = $this->getModel();

        if (! $model) {
            return null;
        }

        $userId = auth()->id();
        $cacheKey = $this->getCacheKey("user_{$userId}");

        return Cache::flexible($cacheKey, [300, 600], function () use ($model) {
            return $model->reacted(auth()->user());
        });
    }

    public function addReaction(string $type): void
    {
        if (! auth()->check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to react to content',
                'type' => 'error',
            ]);

            return;
        }

        $model = $this->getModel();

        if (! $model) {
            return;
        }

        // Remove existing reaction if user has one
        $existingReaction = $this->getUserReaction();
        $wasChanging = $existingReaction && $existingReaction->type !== $type;

        if ($existingReaction) {
            $existingReaction->delete();
        }

        // Add new reaction using the package's API
        $user = auth()->user();
        $user->reactTo($model, $type);

        // Clear cached reaction data since we've modified reactions
        $this->clearReactionCache();

        // Provide feedback
        $reactionLabels = [
            'like' => 'liked',
            'dislike' => 'disliked',
            'blush' => 'blushed at',
            'eggplant' => 'reacted with eggplant to',
            'heart' => 'loved',
            'drool' => 'drooled over',
        ];
        
        $reactionType = $reactionLabels[$type] ?? $type;
        $message = $wasChanging ? "Reaction changed to {$reactionType}" : "Content {$reactionType}";

        $this->dispatch('show-notification', [
            'message' => $message,
            'type' => 'success',
        ]);

        $this->dispatch('close-reaction-modal');
        
        // Dispatch reaction event for cache invalidation
        $this->dispatch('reaction-added', [
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
        ]);
    }

    public function removeReaction(): void
    {
        if (! auth()->check()) {
            $this->dispatch('show-notification', [
                'message' => 'Please log in to react to content',
                'type' => 'error',
            ]);

            return;
        }

        $model = $this->getModel();

        if (! $model) {
            return;
        }

        $user = auth()->user();
        $user->removeReactionFrom($model);

        // Clear cached reaction data since we've modified reactions
        $this->clearReactionCache();

        $this->dispatch('show-notification', [
            'message' => 'Reaction removed',
            'type' => 'success',
        ]);

        $this->dispatch('close-reaction-modal');
        
        // Dispatch reaction event for cache invalidation
        $this->dispatch('reaction-removed', [
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
        ]);
    }

    public function render()
    {
        return view('livewire.components.reaction-button');
    }
}
