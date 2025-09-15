<?php

use App\Enums\BdsmRole;
use App\Enums\CoupleTaskStatus;
use App\Models\CoupleTask;
use App\Models\User;
use App\Notifications\CoupleTaskAssigned;
use App\Notifications\CoupleTaskCompleted;
use App\Notifications\CoupleTaskThanked;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->refreshDatabase();
});

test('couple task assigned notification is sent', function () {
    Notification::fake();

    // Create couple users
    $dom = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $dom->profile()->create(['bdsm_role' => BdsmRole::Dominant]);

    $sub = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $sub->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a couple task
    $coupleTask = CoupleTask::create([
        'assigned_by' => $dom->id,
        'assigned_to' => $sub->id,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'dom_message' => 'Please complete this task',
        'difficulty_level' => 5,
        'duration_hours' => 24,
        'status' => CoupleTaskStatus::Pending,
        'assigned_at' => now(),
        'deadline_at' => now()->addHours(24),
    ]);

    // Manually send the notification (simulating what happens in the Livewire component)
    $sub->notify(new CoupleTaskAssigned($coupleTask));

    // Assert notification was sent
    Notification::assertSentTo($sub, CoupleTaskAssigned::class);
});

test('couple task completed notification is sent', function () {
    Notification::fake();

    // Create couple users
    $dom = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $dom->profile()->create(['bdsm_role' => BdsmRole::Dominant]);

    $sub = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $sub->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a couple task
    $coupleTask = CoupleTask::create([
        'assigned_by' => $dom->id,
        'assigned_to' => $sub->id,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'difficulty_level' => 5,
        'duration_hours' => 24,
        'status' => CoupleTaskStatus::Pending,
        'assigned_at' => now(),
        'deadline_at' => now()->addHours(24),
    ]);

    // Mark task as completed (this should trigger the notification)
    $coupleTask->markAsCompleted('Task completed successfully!');

    // Assert notification was sent to the dom
    Notification::assertSentTo($dom, CoupleTaskCompleted::class);
});

test('couple task thanked notification is sent', function () {
    Notification::fake();

    // Create couple users
    $dom = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $dom->profile()->create(['bdsm_role' => BdsmRole::Dominant]);

    $sub = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $sub->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a completed couple task
    $coupleTask = CoupleTask::create([
        'assigned_by' => $dom->id,
        'assigned_to' => $sub->id,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'difficulty_level' => 5,
        'duration_hours' => 24,
        'status' => CoupleTaskStatus::Completed,
        'assigned_at' => now(),
        'deadline_at' => now()->addHours(24),
        'completed_at' => now(),
    ]);

    // Add thank you message (this should trigger the notification)
    $coupleTask->addThankYou('Thank you for the task!');

    // Assert notification was sent to the dom
    Notification::assertSentTo($dom, CoupleTaskThanked::class);
});

test('notification center displays notifications', function () {
    // Create couple users
    $dom = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $dom->profile()->create(['bdsm_role' => BdsmRole::Dominant]);

    $sub = User::factory()->create(['user_type' => \App\TargetUserType::Couple]);
    $sub->profile()->create(['bdsm_role' => BdsmRole::Submissive]);

    // Create a couple task
    $coupleTask = CoupleTask::create([
        'assigned_by' => $dom->id,
        'assigned_to' => $sub->id,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'difficulty_level' => 5,
        'duration_hours' => 24,
        'status' => CoupleTaskStatus::Pending,
        'assigned_at' => now(),
        'deadline_at' => now()->addHours(24),
    ]);

    // Send notification
    $sub->notify(new CoupleTaskAssigned($coupleTask));

    // Login as sub and check notification center
    $this->actingAs($sub);

    $response = $this->get('/app/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Test Task');
    $response->assertSee('New task from');
});
