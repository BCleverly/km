<?php

declare(strict_types=1);

use App\Actions\Tasks\CompleteTask;
use App\Models\Tasks\Task;
use App\Models\Tasks\UserAssignedTask;
use App\Models\User;
use App\TaskStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
});

it('allows premium users to upload completion images', function () {
    // Create a premium user
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'premium',
        'stripe_id' => 'sub_test',
        'stripe_status' => 'active',
    ]);

    // Create a task and assign it to the user
    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    // Create a fake image
    $image = UploadedFile::fake()->image('completion.jpg', 800, 600);

    // Complete the task with an image
    $result = CompleteTask::run($user, $image, 'Great task completion!');

    expect($result['success'])->toBeTrue();
    
    // Check that the task was marked as completed with image
    $assignedTask->refresh();
    expect($assignedTask->status)->toBe(TaskStatus::Completed);
    expect($assignedTask->has_completion_image)->toBeTrue();
    expect($assignedTask->completion_note)->toBe('Great task completion!');
    
    // Check that the image was stored
    expect($assignedTask->getFirstMedia('completion_images'))->not->toBeNull();
});

it('prevents free users from uploading completion images', function () {
    // Create a free user
    $user = User::factory()->create();

    // Create a task and assign it to the user
    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    // Create a fake image
    $image = UploadedFile::fake()->image('completion.jpg', 800, 600);

    // Complete the task with an image (should be ignored)
    $result = CompleteTask::run($user, $image, 'Great task completion!');

    expect($result['success'])->toBeTrue();
    
    // Check that the task was completed but without image
    $assignedTask->refresh();
    expect($assignedTask->status)->toBe(TaskStatus::Completed);
    expect($assignedTask->has_completion_image)->toBeFalse();
    expect($assignedTask->completion_note)->toBe('Great task completion!');
    
    // Check that no image was stored
    expect($assignedTask->getFirstMedia('completion_images'))->toBeNull();
});

it('allows admin users to upload completion images', function () {
    // Create the Admin role first
    \Spatie\Permission\Models\Role::create(['name' => 'Admin']);
    
    // Create an admin user
    $user = User::factory()->create();
    $user->assignRole('Admin');

    // Create a task and assign it to the user
    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    // Create a fake image
    $image = UploadedFile::fake()->image('completion.jpg', 800, 600);

    // Complete the task with an image
    $result = CompleteTask::run($user, $image, 'Admin task completion!');

    expect($result['success'])->toBeTrue();
    
    // Check that the task was marked as completed with image
    $assignedTask->refresh();
    expect($assignedTask->status)->toBe(TaskStatus::Completed);
    expect($assignedTask->has_completion_image)->toBeTrue();
    expect($assignedTask->completion_note)->toBe('Admin task completion!');
    
    // Check that the image was stored
    expect($assignedTask->getFirstMedia('completion_images'))->not->toBeNull();
});

it('allows lifetime subscription users to upload completion images', function () {
    // Create a lifetime subscription user
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'lifetime',
        'stripe_id' => 'sub_lifetime',
        'stripe_status' => 'active',
    ]);

    // Create a task and assign it to the user
    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    // Create a fake image
    $image = UploadedFile::fake()->image('completion.jpg', 800, 600);

    // Complete the task with an image
    $result = CompleteTask::run($user, $image, 'Lifetime user completion!');

    expect($result['success'])->toBeTrue();
    
    // Check that the task was marked as completed with image
    $assignedTask->refresh();
    expect($assignedTask->status)->toBe(TaskStatus::Completed);
    expect($assignedTask->has_completion_image)->toBeTrue();
    expect($assignedTask->completion_note)->toBe('Lifetime user completion!');
    
    // Check that the image was stored
    expect($assignedTask->getFirstMedia('completion_images'))->not->toBeNull();
});

it('can display completion images in the dashboard', function () {
    // Create a premium user with a completed task that has an image
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'premium',
        'stripe_id' => 'sub_test',
        'stripe_status' => 'active',
    ]);

    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Completed,
        'has_completion_image' => true,
        'completion_note' => 'Great completion!',
    ]);

    // Add a fake image to the assigned task
    $image = UploadedFile::fake()->image('completion.jpg', 800, 600);
    $assignedTask->addMedia($image)->toMediaCollection('completion_images');

    // Test the completion image display component
    Livewire::actingAs($user)
        ->test('tasks.completion-image-display', ['assignedTask' => $assignedTask])
        ->assertSee('completion.jpg')
        ->assertSee('Great completion!');
});

it('validates image upload in the completion component', function () {
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'premium',
        'stripe_id' => 'sub_test',
        'stripe_status' => 'active',
    ]);

    $task = Task::factory()->create();
    $assignedTask = UserAssignedTask::factory()->create([
        'user_id' => $user->id,
        'task_id' => $task->id,
        'status' => TaskStatus::Assigned,
    ]);

    // Test with invalid file type
    $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

    Livewire::actingAs($user)
        ->test('tasks.complete-task-with-image', ['assignedTask' => $assignedTask])
        ->set('completionImage', $invalidFile)
        ->call('completeTask')
        ->assertHasErrors(['completionImage']);

    // Test with valid image
    $validImage = UploadedFile::fake()->image('completion.jpg', 800, 600);

    Livewire::actingAs($user)
        ->test('tasks.complete-task-with-image', ['assignedTask' => $assignedTask])
        ->set('completionImage', $validImage)
        ->set('completionNote', 'Great task!')
        ->call('completeTask')
        ->assertHasNoErrors();
});
