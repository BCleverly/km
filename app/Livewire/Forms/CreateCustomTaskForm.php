<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\ContentStatus;
use App\Models\Models\Tag;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\TargetUserType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateCustomTaskForm extends Form
{
    // Task Details
    #[Validate('required|string|min:3|max:255')]
    public string $title = '';

    #[Validate('required|string|min:10|max:2000')]
    public string $description = '';

    #[Validate('required|integer|min:1|max:6')]
    public int $difficultyLevel = 3;

    #[Validate('required|integer|min:1|max:999')]
    public int $durationTime = 1;

    #[Validate('required|in:minutes,hours,days,weeks')]
    public string $durationType = 'hours';

    #[Validate('required')]
    public TargetUserType $targetUserType = TargetUserType::Any;

    // Tag properties - organized by type
    public array $tags = [];


    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:2000',
            'difficultyLevel' => 'required|integer|min:1|max:6',
            'durationTime' => 'required|integer|min:1|max:999',
            'durationType' => 'required|in:minutes,hours,days,weeks',
            'targetUserType' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.min' => 'Task title must be at least 3 characters.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.required' => 'Task description is required.',
            'description.min' => 'Task description must be at least 10 characters.',
            'description.max' => 'Task description cannot exceed 2000 characters.',
            'difficultyLevel.required' => 'Difficulty level is required.',
            'difficultyLevel.min' => 'Difficulty level must be at least 1.',
            'difficultyLevel.max' => 'Difficulty level cannot exceed 6.',
            'durationTime.required' => 'Duration time is required.',
            'durationTime.min' => 'Duration time must be at least 1.',
            'durationTime.max' => 'Duration time cannot exceed 999.',
            'durationType.required' => 'Duration type is required.',
            'durationType.in' => 'Duration type must be one of: minutes, hours, days, weeks.',
            'targetUserType.required' => 'Target user type is required.',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $user = Auth::user();
        if (!$user) {
            throw new \Exception('You must be logged in to submit a task.');
        }

        // Create the task
        $task = Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'difficulty_level' => $this->difficultyLevel,
            'duration_time' => $this->durationTime,
            'duration_type' => $this->durationType,
            'target_user_type' => $this->targetUserType,
            'user_id' => $user->id,
            'status' => ContentStatus::Pending,
            'is_premium' => false,
        ]);

        // Attach tags if any are selected
        if (!empty($this->tags)) {
            $tagIds = [];
            foreach ($this->tags as $typeTags) {
                if (is_array($typeTags)) {
                    $tagIds = array_merge($tagIds, array_filter($typeTags));
                }
            }
            if (!empty($tagIds)) {
                $task->syncTags($tagIds);
            }
        }
    }

    public function resetForm(): void
    {
        $this->title = '';
        $this->description = '';
        $this->difficultyLevel = 3;
        $this->durationTime = 1;
        $this->durationType = 'hours';
        $this->targetUserType = TargetUserType::Any;
        $this->tags = [];
    }

    /**
     * Get available tags for each type from config
     */
    public function getAvailableTags(): array
    {
        $tagTypes = config('app.tag_types', []);
        $availableTags = [];

        foreach ($tagTypes as $typeKey => $typeConfig) {
            $availableTags[$typeKey] = [
                'name' => $typeConfig['name'],
                'description' => $typeConfig['description'],
                'required' => $typeConfig['required'] ?? false,
                'tags' => Tag::approved()
                    ->withType($typeKey)
                    ->orderBy('name')
                    ->get()
                    ->mapWithKeys(function (Tag $tag) {
                        return [$tag->id => $tag->name];
                    })
                    ->toArray(),
            ];
        }

        return $availableTags;
    }

    /**
     * Get tag types configuration
     */
    public function getTagTypes(): array
    {
        return config('app.tag_types', []);
    }

    /**
     * Get selected tags (including pending ones) for display
     */
    public function getSelectedTags(): array
    {
        $selectedTags = [];
        
        foreach ($this->tags as $typeKey => $tagIds) {
            if (is_array($tagIds) && !empty($tagIds)) {
                $tags = Tag::whereIn('id', $tagIds)->get();
                $selectedTags[$typeKey] = $tags->map(function (Tag $tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'status' => $tag->status->value,
                        'is_pending' => $tag->status === ContentStatus::Pending,
                    ];
                })->toArray();
            }
        }
        
        return $selectedTags;
    }

    /**
     * Remove a tag from the selection
     */
    public function removeTag(string $type, int $tagId): void
    {
        if (isset($this->tags[$type]) && is_array($this->tags[$type])) {
            $this->tags[$type] = array_filter($this->tags[$type], fn($id) => $id != $tagId);
            $this->tags[$type] = array_values($this->tags[$type]); // Re-index array
        }
    }

    /**
     * Create a new tag for a specific type
     */
    public function createTag(string $type, string $name): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Validate that the type exists in config
        $tagTypes = config('app.tag_types', []);
        if (!isset($tagTypes[$type])) {
            return null;
        }

        // Check if tag already exists
        $existingTag = Tag::where('name->en', $name)
            ->where('type', $type)
            ->first();

        if ($existingTag) {
            return $existingTag->id;
        }

        // Create new tag
        $tag = Tag::create([
            'name' => ['en' => $name],
            'slug' => ['en' => \Str::slug($name)],
            'type' => $type,
            'status' => ContentStatus::Pending,
            'created_by' => $user->id,
        ]);

        return $tag->id;
    }
}
