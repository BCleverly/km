<?php

use App\ContentStatus;
use App\Enums\BdsmRole;
use App\Livewire\CoupleTasks\SendTask;
use App\Models\Tasks\Task;
use App\Models\User;
use App\TargetUserType;
use Livewire\Livewire;

it('can search and filter available tasks', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    // Create some test tasks
    Task::factory()->create([
        'title' => 'Morning Exercise',
        'description' => 'Complete 30 minutes of exercise',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);
    
    Task::factory()->create([
        'title' => 'Evening Meditation',
        'description' => 'Meditate for 15 minutes',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);
    
    Task::factory()->create([
        'title' => 'House Cleaning',
        'description' => 'Clean the living room',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    // Set task mode to existing
    $component->set('task_mode', 'existing');
    
    // Check that all tasks are available initially
    expect($component->getFilteredTasks())->toHaveCount(3);
    
    // Search for "Morning"
    $component->set('task_search', 'Morning');
    expect($component->getFilteredTasks())->toHaveCount(1);
    expect($component->getFilteredTasks()->first()->title)->toBe('Morning Exercise');
    
    // Search for "Evening"
    $component->set('task_search', 'Evening');
    expect($component->getFilteredTasks())->toHaveCount(1);
    expect($component->getFilteredTasks()->first()->title)->toBe('Evening Meditation');
    
    // Search for something that doesn't exist
    $component->set('task_search', 'NonExistent');
    expect($component->getFilteredTasks())->toHaveCount(0);
    
    // Clear search
    $component->set('task_search', '');
    expect($component->getFilteredTasks())->toHaveCount(3);
});

it('can select a task and populate form fields', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task description',
        'difficulty_level' => 7,
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    // Set task mode to existing and select a task
    $component->set('task_mode', 'existing')
              ->set('selected_task_id', $task->id);
    
    // Check that form fields are populated
    expect($component->title)->toBe('Test Task');
    expect($component->description)->toBe('This is a test task description');
    expect($component->difficulty_level)->toBe(7);
    expect($component->task_search)->toBe('Test Task');
});

it('resets task selection when search changes', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    // Select a task
    $component->set('task_mode', 'existing')
              ->set('selected_task_id', $task->id);
    
    expect($component->selected_task_id)->toBe($task->id);
    
    // Change search - should reset selection
    $component->set('task_search', 'new search');
    
    expect($component->selected_task_id)->toBeNull();
});

it('resets form fields when switching task modes', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    // Fill in some form data in custom mode
    $component->set('task_mode', 'custom')
              ->set('title', 'Custom Task')
              ->set('description', 'Custom description');
    
    // Switch to existing mode - should reset
    $component->set('task_mode', 'existing');
    
    expect($component->selected_task_id)->toBeNull();
    expect($component->task_search)->toBe('');
    
    // Switch back to custom mode - should reset again
    $component->set('task_mode', 'custom');
    
    expect($component->title)->toBe('');
    expect($component->description)->toBe('');
});

it('shows empty state when no tasks match search', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    $component->set('task_mode', 'existing')
              ->set('task_search', 'NonExistentSearch');
    
    $filteredTasks = $component->getFilteredTasks();
    expect($filteredTasks)->toHaveCount(0);
});

it('case insensitive search works correctly', function () {
    $user = User::factory()->create();
    $user->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    Task::factory()->create([
        'title' => 'Morning Exercise Routine',
        'status' => ContentStatus::Approved,
        'target_user_type' => TargetUserType::Any,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(SendTask::class);
    
    $component->set('task_mode', 'existing');
    
    // Test case insensitive search
    $component->set('task_search', 'morning');
    expect($component->getFilteredTasks())->toHaveCount(1);
    
    $component->set('task_search', 'EXERCISE');
    expect($component->getFilteredTasks())->toHaveCount(1);
    
    $component->set('task_search', 'Routine');
    expect($component->getFilteredTasks())->toHaveCount(1);
});