<?php

use App\ContentStatus;
use App\Livewire\Tasks\CreateCustomTaskComponent;
use App\Models\Tasks\Task;
use App\Models\Tasks\TaskReward;
use App\Models\Tasks\TaskPunishment;
use App\Models\User;
use App\TargetUserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render the create custom task component', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->assertSee('Create Custom Task')
        ->assertSee('Task Information')
        ->assertSee('Reward (Optional)')
        ->assertSee('Punishment (Optional)');
});

it('can create a basic task without rewards or punishments', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Task')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 3)
        ->set('durationTime', 2)
        ->set('durationType', 'hours')
        ->set('targetUserType', TargetUserType::Any)
        ->call('submit')
        ->assertSee('Your custom task has been created successfully!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test task description that is long enough to pass validation.',
        'difficulty_level' => 3,
        'duration_time' => 2,
        'duration_type' => 'hours',
        'target_user_type' => TargetUserType::Any->value,
        'user_id' => $this->user->id,
        'status' => ContentStatus::Pending->value,
        'is_premium' => false,
    ]);
});

it('can create a task with a reward', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Task with Reward')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 4)
        ->set('durationTime', 1)
        ->set('durationType', 'days')
        ->set('targetUserType', TargetUserType::Male)
        ->set('includeReward', true)
        ->set('rewardTitle', 'Test Reward')
        ->set('rewardDescription', 'This is a test reward description that is long enough to pass validation.')
        ->set('rewardDifficultyLevel', 4)
        ->call('submit')
        ->assertSee('Your custom task has been created successfully!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task with Reward',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('task_rewards', [
        'title' => 'Test Reward',
        'description' => 'This is a test reward description that is long enough to pass validation.',
        'difficulty_level' => 4,
        'user_id' => $this->user->id,
    ]);
});

it('can create a task with a punishment', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Task with Punishment')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 2)
        ->set('durationTime', 30)
        ->set('durationType', 'minutes')
        ->set('targetUserType', TargetUserType::Female)
        ->set('includePunishment', true)
        ->set('punishmentTitle', 'Test Punishment')
        ->set('punishmentDescription', 'This is a test punishment description that is long enough to pass validation.')
        ->set('punishmentDifficultyLevel', 2)
        ->call('submit')
        ->assertSee('Your custom task has been created successfully!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task with Punishment',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('task_punishments', [
        'title' => 'Test Punishment',
        'description' => 'This is a test punishment description that is long enough to pass validation.',
        'difficulty_level' => 2,
        'user_id' => $this->user->id,
    ]);
});

it('can create a task with both reward and punishment', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Task with Both')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 5)
        ->set('durationTime', 1)
        ->set('durationType', 'weeks')
        ->set('targetUserType', TargetUserType::Couple)
        ->set('includeReward', true)
        ->set('rewardTitle', 'Test Reward')
        ->set('rewardDescription', 'This is a test reward description that is long enough to pass validation.')
        ->set('rewardDifficultyLevel', 5)
        ->set('includePunishment', true)
        ->set('punishmentTitle', 'Test Punishment')
        ->set('punishmentDescription', 'This is a test punishment description that is long enough to pass validation.')
        ->set('punishmentDifficultyLevel', 5)
        ->call('submit')
        ->assertSee('Your custom task has been created successfully!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task with Both',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('task_rewards', [
        'title' => 'Test Reward',
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('task_punishments', [
        'title' => 'Test Punishment',
        'user_id' => $this->user->id,
    ]);
});

it('can create a premium task', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Premium Test Task')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 3)
        ->set('durationTime', 1)
        ->set('durationType', 'hours')
        ->set('targetUserType', TargetUserType::Any)
        ->set('isPremium', true)
        ->call('submit')
        ->assertSee('Your custom task has been created successfully!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'Premium Test Task',
        'is_premium' => true,
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', '')
        ->set('description', '')
        ->call('submit')
        ->assertHasErrors(['title', 'description']);
});

it('validates title minimum length', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'AB')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->call('submit')
        ->assertHasErrors(['title']);
});

it('validates description minimum length', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'Short')
        ->call('submit')
        ->assertHasErrors(['description']);
});

it('validates difficulty level range', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 6)
        ->call('submit')
        ->assertHasErrors(['difficultyLevel']);

    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('difficultyLevel', 0)
        ->call('submit')
        ->assertHasErrors(['difficultyLevel']);
});

it('validates duration time minimum', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('durationTime', 0)
        ->call('submit')
        ->assertHasErrors(['durationTime']);
});

it('validates duration type', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('durationType', 'invalid')
        ->call('submit')
        ->assertHasErrors(['durationType']);
});

it('validates reward fields when reward is included', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('includeReward', true)
        ->set('rewardTitle', '')
        ->set('rewardDescription', '')
        ->call('submit')
        ->assertHasErrors(['rewardTitle', 'rewardDescription']);
});

it('validates punishment fields when punishment is included', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Valid Title')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->set('includePunishment', true)
        ->set('punishmentTitle', '')
        ->set('punishmentDescription', '')
        ->call('submit')
        ->assertHasErrors(['punishmentTitle', 'punishmentDescription']);
});

it('can reset the form', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Title')
        ->set('description', 'Test description')
        ->set('includeReward', true)
        ->set('rewardTitle', 'Test Reward')
        ->call('resetForm')
        ->assertSet('title', '')
        ->assertSet('description', '')
        ->assertSet('includeReward', false)
        ->assertSet('rewardTitle', '');
});

it('syncs difficulty levels when task difficulty changes', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('includeReward', false)
        ->set('includePunishment', false)
        ->set('difficultyLevel', 4)
        ->assertSet('rewardDifficultyLevel', 4)
        ->assertSet('punishmentDifficultyLevel', 4);
});

it('clears reward fields when reward is disabled', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('includeReward', true)
        ->set('rewardTitle', 'Test Reward')
        ->set('rewardDescription', 'Test Description')
        ->set('includeReward', false)
        ->assertSet('rewardTitle', '')
        ->assertSet('rewardDescription', '');
});

it('clears punishment fields when punishment is disabled', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('includePunishment', true)
        ->set('punishmentTitle', 'Test Punishment')
        ->set('punishmentDescription', 'Test Description')
        ->set('includePunishment', false)
        ->assertSet('punishmentTitle', '')
        ->assertSet('punishmentDescription', '');
});

it('shows loading state during submission', function () {
    Livewire::test(CreateCustomTaskComponent::class)
        ->set('title', 'Test Task')
        ->set('description', 'This is a test task description that is long enough to pass validation.')
        ->call('submit')
        ->assertSee('Creating...');
});

it('requires authentication to access', function () {
    auth()->logout();
    
    $this->get('/app/tasks/create')
        ->assertRedirect('/login');
});

it('can access the create task page when authenticated', function () {
    $this->get('/app/tasks/create')
        ->assertOk()
        ->assertSee('Create Custom Task');
});