<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Tasks\Task;
use App\Models\Tasks\UserAssignedTask;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\TaskStatus;
use App\TargetUserType;
use App\ContentStatus;

it('can get user tasks', function () {
    $user = User::factory()->create();
    
    // Create some assigned tasks
    $task1 = Task::factory()->create([
        'title' => 'Morning Exercise',
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);
    
    $task2 = Task::factory()->create([
        'title' => 'Evening Meditation',
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task1->id,
        'status' => TaskStatus::Completed,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task2->id,
        'status' => TaskStatus::Assigned,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'tasks' => [
                '*' => [
                    'id',
                    'status',
                    'status_label',
                    'assigned_at',
                    'task' => [
                        'id',
                        'title',
                        'description',
                        'difficulty_level',
                        'target_user_type',
                    ],
                ],
            ],
            'pagination',
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('tasks'))->toHaveCount(2);
});

it('can get active task', function () {
    $user = User::factory()->create();
    
    $task = Task::factory()->create([
        'title' => 'Active Task',
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks/active');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'has_active_task',
            'task' => [
                'id',
                'status',
                'task' => [
                    'id',
                    'title',
                    'description',
                ],
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('has_active_task'))->toBeTrue();
    expect($response->json('task.task.title'))->toBe('Active Task');
});

it('returns no active task when none exists', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks/active');

    $response->assertSuccessful()
        ->assertJson([
            'success' => true,
            'has_active_task' => false,
            'message' => 'No active task',
            'task' => null,
        ]);
});

it('can complete a task', function () {
    $user = User::factory()->create();
    
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    $reward = TaskReward::factory()->create([
        'title' => 'Test Reward',
        'description' => 'A test reward',
    ]);

    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
        'potential_reward_id' => $reward->id,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks/complete', [
            'completion_note' => 'Task completed successfully',
        ]);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'task' => [
                'id',
                'status',
                'completed_at',
                'completion_note',
                'task' => [
                    'id',
                    'title',
                ],
                'outcome' => [
                    'id',
                    'title',
                    'type',
                ],
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('task.status'))->toBe(TaskStatus::Completed->value);
    expect($response->json('task.outcome.title'))->toBe('Test Reward');
});

it('cannot complete task without active task', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks/complete', [
            'completion_note' => 'Task completed successfully',
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'No active task to complete',
        ]);
});

it('can get task statistics', function () {
    $user = User::factory()->create();
    
    // Create some tasks for statistics
    $task = Task::factory()->create([
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Completed,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Failed,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks/stats');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'stats' => [
                'summary' => [
                    'total_assigned',
                    'completed',
                    'failed',
                    'active',
                    'completion_rate',
                    'current_streak',
                    'longest_streak',
                ],
                'streaks',
                'active_outcomes',
                'daily_limits',
            ],
        ]);

    expect($response->json('success'))->toBeTrue();
    expect($response->json('stats.summary.total_assigned'))->toBe(2);
    expect($response->json('stats.summary.completed'))->toBe(1);
    expect($response->json('stats.summary.failed'))->toBe(1);
});

it('validates task completion data', function () {
    $user = User::factory()->create();
    
    $task = Task::factory()->create([
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/tasks/complete', [
            'completion_note' => str_repeat('a', 1001), // Too long
        ]);

    $response->assertSuccessful()
        ->assertJson([
            'success' => false,
            'message' => 'Validation failed',
        ])
        ->assertJsonStructure([
            'errors' => ['completion_note'],
        ]);
});

it('filters tasks by status', function () {
    $user = User::factory()->create();
    
    $task = Task::factory()->create([
        'target_user_type' => TargetUserType::Male,
        'status' => ContentStatus::Approved,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Completed,
    ]);

    UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Failed,
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/tasks?status=completed');

    $response->assertSuccessful();
    expect($response->json('tasks'))->toHaveCount(1);
    expect($response->json('tasks.0.status'))->toBe(TaskStatus::Completed->value);
});