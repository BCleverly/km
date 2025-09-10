<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\Models\Fantasy;
use App\Models\Story;
use Illuminate\Database\Eloquent\Model;
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

    public function getModel(): ?Model
    {
        try {
            return match ($this->modelType) {
                'task', 'tasks' => Task::find($this->modelId),
                'outcome', 'outcomes' => Outcome::find($this->modelId),
                'fantasy', 'fantasies' => Fantasy::find($this->modelId),
                'story', 'stories' => Story::find($this->modelId),
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

        $summary = $model->reactionSummary();

        return collect($summary)->map(function ($count, $type) {
            return [
                'type' => $type,
                'count' => $count,
            ];
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

        return $model->reacted(auth()->user());
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

        $this->dispatch('show-notification', [
            'message' => 'Reaction removed',
            'type' => 'success',
        ]);

        $this->dispatch('close-reaction-modal');
    }

    public function render()
    {
        return view('livewire.components.reaction-button');
    }
}
