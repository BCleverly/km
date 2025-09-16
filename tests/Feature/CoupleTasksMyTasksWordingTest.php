<?php

use App\Enums\BdsmRole;
use App\Livewire\CoupleTasks\MyTasks;
use App\Models\User;
use Livewire\Livewire;

it('shows correct wording for submissive users', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'bdsm_role' => BdsmRole::Submissive,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(MyTasks::class);

    expect($component->getPageTitle())->toBe('Tasks from My Dominant - Kink Master');
    expect($component->getPageDescription())->toBe('Tasks assigned by your dominant partner. Don\'t disappoint them!');
    expect($component->getNoTasksMessage())->toBe('Your dominant hasn\'t assigned you any tasks yet.');
});

it('shows correct wording for dominant users', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'bdsm_role' => BdsmRole::Dominant,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(MyTasks::class);

    expect($component->getPageTitle())->toBe('Tasks for My Submissive - Kink Master');
    expect($component->getPageDescription())->toBe('Tasks you\'ve assigned to your submissive partner.');
    expect($component->getNoTasksMessage())->toBe('You haven\'t assigned any tasks to your submissive yet.');
});

it('shows correct wording for switch users', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'bdsm_role' => BdsmRole::Switch,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(MyTasks::class);

    expect($component->getPageTitle())->toBe('My Partner Tasks - Kink Master');
    expect($component->getPageDescription())->toBe('Tasks in your relationship.');
    expect($component->getNoTasksMessage())->toBe('No tasks have been assigned yet.');
});

it('shows default wording for users without BDSM role', function () {
    $user = User::factory()->create();
    $user->profile()->create([
        'bdsm_role' => null,
    ]);

    $this->actingAs($user);

    $component = Livewire::test(MyTasks::class);

    expect($component->getPageTitle())->toBe('My Partner Tasks - Kink Master');
    expect($component->getPageDescription())->toBe('Tasks in your relationship.');
    expect($component->getNoTasksMessage())->toBe('No tasks have been assigned yet.');
});

it('retrieves correct tasks for dominant users', function () {
    $dominant = User::factory()->create();
    $dominant->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    $submissive = User::factory()->create();
    $submissive->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a task assigned BY the dominant TO the submissive
    $task = \App\Models\CoupleTask::factory()->create([
        'assigned_by' => $dominant->id,
        'assigned_to' => $submissive->id,
    ]);

    $this->actingAs($dominant);

    $component = Livewire::test(MyTasks::class);
    
    // Dominant should see tasks they've assigned
    expect($component->getActiveTasks())->toHaveCount(1);
    expect($component->getActiveTasks()->first()->id)->toBe($task->id);
});

it('retrieves correct tasks for submissive users', function () {
    $dominant = User::factory()->create();
    $dominant->profile()->create(['bdsm_role' => BdsmRole::Dominant]);
    
    $submissive = User::factory()->create();
    $submissive->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a task assigned BY the dominant TO the submissive
    $task = \App\Models\CoupleTask::factory()->create([
        'assigned_by' => $dominant->id,
        'assigned_to' => $submissive->id,
    ]);

    $this->actingAs($submissive);

    $component = Livewire::test(MyTasks::class);
    
    // Submissive should see tasks assigned to them
    expect($component->getActiveTasks())->toHaveCount(1);
    expect($component->getActiveTasks()->first()->id)->toBe($task->id);
});