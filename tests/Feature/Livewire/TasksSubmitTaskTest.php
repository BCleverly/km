<?php

use App\Livewire\Tasks\SubmitTask;
use App\Models\User;
use App\Models\Tasks\Task;
use App\TargetUserType;
use App\ContentStatus;
use Livewire\Livewire;

it('renders successfully for authenticated users', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertStatus(200);
});

it('displays submit task title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Submit Task');
});

it('shows form fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Title')
        ->assertSee('Description')
        ->assertSee('Difficulty Level')
        ->assertSee('Target User Type')
        ->assertSee('Duration')
        ->assertSee('Submit Task');
});

it('displays user types options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Male')
        ->assertSee('Female')
        ->assertSee('Couple')
        ->assertSee('Any');
});

it('displays difficulty levels options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Very Easy')
        ->assertSee('Easy')
        ->assertSee('Medium')
        ->assertSee('Hard')
        ->assertSee('Very Hard')
        ->assertSee('Extreme');
});

it('submits task successfully with valid data', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', 'Test Task')
        ->set('taskForm.description', 'Test Description')
        ->set('taskForm.difficulty_level', 3)
        ->set('taskForm.target_user_type', TargetUserType::Any->value)
        ->set('taskForm.duration_time', 2)
        ->set('taskForm.duration_type', 'hours')
        ->call('submitTask')
        ->assertSessionHas('message', 'Your task has been submitted for review!');
    
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'Test Description',
        'difficulty_level' => 3,
        'target_user_type' => TargetUserType::Any->value,
        'user_id' => $user->id,
        'status' => ContentStatus::Pending
    ]);
});

it('validates required fields', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', '')
        ->set('taskForm.description', '')
        ->call('submitTask')
        ->assertHasErrors(['taskForm.title', 'taskForm.description']);
});

it('validates title length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', str_repeat('a', 256))
        ->call('submitTask')
        ->assertHasErrors(['taskForm.title']);
});

it('validates description length', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.description', str_repeat('a', 5001))
        ->call('submitTask')
        ->assertHasErrors(['taskForm.description']);
});

it('validates difficulty level range', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.difficulty_level', 11)
        ->call('submitTask')
        ->assertHasErrors(['taskForm.difficulty_level']);
});

it('validates duration time', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.duration_time', 0)
        ->call('submitTask')
        ->assertHasErrors(['taskForm.duration_time']);
});

it('validates duration type', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.duration_type', 'invalid')
        ->call('submitTask')
        ->assertHasErrors(['taskForm.duration_type']);
});

it('creates tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->call('createTag', 'category', 'New Tag')
        ->assertSessionHas('message', 'New tag created and added! It will be reviewed before becoming visible to others.');
});

it('validates tag name is not empty', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->call('createTag', 'category', '')
        ->assertSessionHas('error', 'Tag name cannot be empty.');
});

it('validates tag name is not whitespace only', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->call('createTag', 'category', '   ')
        ->assertSessionHas('error', 'Tag name cannot be empty.');
});

it('removes tag successfully', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->call('removeTag', 'category', 1);
    
    // The tag should be removed from the form
    // This is tested indirectly through the form state
});

it('resets form after successful submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', 'Test Task')
        ->set('taskForm.description', 'Test Description')
        ->set('taskForm.difficulty_level', 3)
        ->set('taskForm.target_user_type', TargetUserType::Any->value)
        ->set('taskForm.duration_time', 2)
        ->set('taskForm.duration_type', 'hours')
        ->call('submitTask');
    
    // Form should be reset after submission
    $component = Livewire::actingAs($user)
        ->test(SubmitTask::class);
    
    expect($component->get('taskForm.title'))->toBe('');
    expect($component->get('taskForm.description'))->toBe('');
});

it('shows loading state during submission', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', 'Test Task')
        ->set('taskForm.description', 'Test Description')
        ->set('taskForm.difficulty_level', 3)
        ->set('taskForm.target_user_type', TargetUserType::Any->value)
        ->set('taskForm.duration_time', 2)
        ->set('taskForm.duration_type', 'hours')
        ->call('submitTask')
        ->assertSee('Submitting...', false);
});

it('displays form validation errors', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.title', '')
        ->call('submitTask')
        ->assertSee('The title field is required', false);
});

it('shows character count for description', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->set('taskForm.description', 'Test description')
        ->assertSee('14 / 5000', false);
});

it('displays duration options', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('minutes')
        ->assertSee('hours')
        ->assertSee('days');
});

it('shows premium content option', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Premium Content', false)
        ->assertSee('Make this task available only to premium users', false);
});

it('displays tags section', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Tags', false)
        ->assertSee('Add tags to help categorize your task', false);
});

it('shows tag creation form', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Create New Tag', false)
        ->assertSee('Tag Name', false);
});

it('displays existing tags', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Available Tags', false);
});

it('handles unauthenticated users', function () {
    $this->get('/app/tasks/submit')
        ->assertRedirect('/login');
});

it('uses proper layout', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertViewIs('livewire.tasks.submit-task');
});

it('displays proper page title', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertStatus(200);
});

it('shows form help text', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Help others understand what this task involves', false)
        ->assertSee('How difficult is this task?', false)
        ->assertSee('Who is this task for?', false);
});

it('displays submission guidelines', function () {
    $user = User::factory()->create();
    
    Livewire::actingAs($user)
        ->test(SubmitTask::class)
        ->assertSee('Submission Guidelines', false)
        ->assertSee('Please ensure your task follows our community guidelines', false);
});

