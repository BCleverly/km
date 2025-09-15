<?php

use App\Enums\BdsmRole;
use App\Enums\CoupleTaskStatus;
use App\Enums\SubscriptionPlan;
use App\Models\CoupleTask;
use App\Models\Tasks\TaskOutcome;
use App\Models\User;
use App\TargetUserType;

beforeEach(function () {
    $this->user = User::factory()->create([
        'user_type' => TargetUserType::Couple,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);
    
    $this->user->profile()->create([
        'username' => 'testuser',
        'bdsm_role' => BdsmRole::Dominant,
    ]);
    
    $this->partner = User::factory()->create([
        'user_type' => TargetUserType::Couple,
        'partner_id' => $this->user->id,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);
    
    $this->partner->profile()->create([
        'username' => 'testpartner',
        'bdsm_role' => BdsmRole::Submissive,
    ]);
    
    $this->user->update(['partner_id' => $this->partner->id]);
});

it('can create a couple task', function () {
    $this->actingAs($this->user);
    
    $coupleTask = CoupleTask::create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'title' => 'Test Task',
        'description' => 'This is a test task',
        'dom_message' => 'Don\'t disappoint me!',
        'difficulty_level' => 5,
        'duration_hours' => 24,
        'status' => CoupleTaskStatus::Pending,
        'assigned_at' => now(),
        'deadline_at' => now()->addHours(24),
    ]);
    
    expect($coupleTask)->toBeInstanceOf(CoupleTask::class);
    expect($coupleTask->title)->toBe('Test Task');
    expect($coupleTask->status)->toBe(CoupleTaskStatus::Pending);
    expect($coupleTask->assignedBy->id)->toBe($this->user->id);
    expect($coupleTask->assignedTo->id)->toBe($this->partner->id);
});

it('can mark a task as completed', function () {
    $coupleTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Pending,
    ]);
    
    $coupleTask->markAsCompleted('Task completed successfully!');
    
    expect($coupleTask->fresh()->status)->toBe(CoupleTaskStatus::Completed);
    expect($coupleTask->fresh()->completion_notes)->toBe('Task completed successfully!');
    expect($coupleTask->fresh()->completed_at)->not->toBeNull();
});

it('can add a thank you message', function () {
    $coupleTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Completed,
        'completed_at' => now(),
    ]);
    
    $coupleTask->addThankYou('Thank you for the task, Master!');
    
    expect($coupleTask->fresh()->thank_you_message)->toBe('Thank you for the task, Master!');
    expect($coupleTask->fresh()->thanked_at)->not->toBeNull();
});

it('checks if user can assign couple tasks', function () {
    // Couple user should be able to assign tasks
    expect($this->user->canAssignCoupleTasks())->toBeTrue();
    
    // Partner should be able to receive tasks
    expect($this->partner->canReceiveCoupleTasks())->toBeTrue();
    
    // Regular user should not be able to assign tasks
    $regularUser = User::factory()->create([
        'user_type' => TargetUserType::Male,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);
    
    expect($regularUser->canAssignCoupleTasks())->toBeFalse();
});

it('checks if user can receive couple tasks', function () {
    // Partner should be able to receive tasks
    expect($this->partner->canReceiveCoupleTasks())->toBeTrue();
    
    // Regular user should not be able to receive tasks
    $regularUser = User::factory()->create([
        'user_type' => TargetUserType::Male,
        'subscription_plan' => SubscriptionPlan::Free,
    ]);
    
    expect($regularUser->canReceiveCoupleTasks())->toBeFalse();
});

it('can get couple partner', function () {
    expect($this->user->getCouplePartner()->id)->toBe($this->partner->id);
    expect($this->partner->getCouplePartner()->id)->toBe($this->user->id);
});

it('can check if task is overdue', function () {
    $overdueTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Pending,
        'deadline_at' => now()->subHours(1),
    ]);
    
    $activeTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Pending,
        'deadline_at' => now()->addHours(1),
    ]);
    
    expect($overdueTask->isOverdue())->toBeTrue();
    expect($activeTask->isOverdue())->toBeFalse();
});

it('can check if task can be completed', function () {
    $pendingTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Pending,
    ]);
    
    $completedTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Completed,
    ]);
    
    expect($pendingTask->canBeCompleted())->toBeTrue();
    expect($completedTask->canBeCompleted())->toBeFalse();
});

it('can check if task can be thanked', function () {
    $completedTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Completed,
        'completed_at' => now(),
    ]);
    
    $thankedTask = CoupleTask::factory()->create([
        'assigned_by' => $this->user->id,
        'assigned_to' => $this->partner->id,
        'status' => CoupleTaskStatus::Completed,
        'completed_at' => now(),
        'thanked_at' => now(),
    ]);
    
    expect($completedTask->canBeThanked())->toBeTrue();
    expect($thankedTask->canBeThanked())->toBeFalse();
});
