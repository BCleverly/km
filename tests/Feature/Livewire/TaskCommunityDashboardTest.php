<?php

declare(strict_types=1);

use App\ContentStatus;
use App\Livewire\Tasks\TaskCommunityDashboard;
use App\Models\Tasks\Outcome;
use App\Models\Tasks\Task;
use App\Models\User;
use App\TargetUserType;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can render the community dashboard', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->assertStatus(200)
        ->assertSee('Community Task Library')
        ->assertSee('Browse Tasks')
        ->assertSee('Submit Task');
});

it('shows browse tab by default', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->assertSee('Browse Tasks');
});

it('can switch to submit tab', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->assertSee('Submit a New Task');
});

it('displays approved tasks in browse tab', function () {
    $task = Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Test Task',
        'description' => 'Test Description',
        'target_user_type' => TargetUserType::Any,
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->assertSee('Test Task')
        ->assertSee('Test Description');
});

it('does not display pending tasks in browse tab', function () {
    $task = Task::factory()->create([
        'status' => ContentStatus::Pending,
        'title' => 'Pending Task',
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->assertDontSee('Pending Task');
});

it('can filter tasks by search term', function () {
    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Searchable Task',
        'description' => 'This task can be found',
    ]);

    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Other Task',
        'description' => 'This task cannot be found',
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->set('search', 'Searchable')
        ->assertSee('Searchable Task')
        ->assertDontSee('Other Task');
});

it('can filter tasks by user type', function () {
    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Male Task',
        'target_user_type' => TargetUserType::Male,
    ]);

    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Female Task',
        'target_user_type' => TargetUserType::Female,
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->set('userType', TargetUserType::Male->value)
        ->assertSee('Male Task')
        ->assertDontSee('Female Task');
});

it('can filter tasks by difficulty level', function () {
    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Easy Task',
        'difficulty_level' => 1,
    ]);

    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Hard Task',
        'difficulty_level' => 5,
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->set('difficulty', 1)
        ->assertSee('Easy Task')
        ->assertDontSee('Hard Task');
});

it('can toggle premium content filter', function () {
    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Free Task',
        'is_premium' => false,
    ]);

    Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Premium Task',
        'is_premium' => true,
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->assertSee('Free Task')
        ->assertDontSee('Premium Task')
        ->set('showPremium', true)
        ->assertSee('Free Task')
        ->assertSee('Premium Task');
});

it('can clear all filters', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->set('search', 'test')
        ->set('userType', TargetUserType::Male->value)
        ->set('difficulty', 3)
        ->set('showPremium', true)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('userType', null)
        ->assertSet('difficulty', null)
        ->assertSet('showPremium', false);
});

it('can submit a new task for review', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->set('form.title', 'New Community Task')
        ->set('form.description', 'This is a new task for the community to review')
        ->set('form.difficultyLevel', 3)
        ->set('form.durationTime', 2)
        ->set('form.durationType', 'hours')
        ->set('form.targetUserType', TargetUserType::Any)
        ->set('form.rewardTitle', 'Great Reward')
        ->set('form.rewardDescription', 'This is a great reward for completing the task')
        ->set('form.rewardDifficultyLevel', 2)
        ->set('form.punishmentTitle', 'Mild Punishment')
        ->set('form.punishmentDescription', 'This is a mild punishment for not completing the task')
        ->set('form.punishmentDifficultyLevel', 4)
        ->call('submitTask')
        ->assertSee('Your task has been submitted for review!');

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Community Task',
        'description' => 'This is a new task for the community to review',
        'status' => ContentStatus::Pending,
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('outcomes', [
        'title' => 'Great Reward',
        'description' => 'This is a great reward for completing the task',
        'intended_type' => 'reward',
        'status' => ContentStatus::Pending,
        'user_id' => $this->user->id,
    ]);

    $this->assertDatabaseHas('outcomes', [
        'title' => 'Mild Punishment',
        'description' => 'This is a mild punishment for not completing the task',
        'intended_type' => 'punishment',
        'status' => ContentStatus::Pending,
        'user_id' => $this->user->id,
    ]);
});

it('validates required fields when submitting task', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->call('submitTask')
        ->assertHasErrors([
            'form.title' => 'required',
            'form.description' => 'required',
            'form.rewardTitle' => 'required',
            'form.rewardDescription' => 'required',
            'form.punishmentTitle' => 'required',
            'form.punishmentDescription' => 'required',
        ]);
});

it('validates minimum length for text fields', function () {
    Livewire::test(TaskCommunityDashboard::class)
        ->set('form.title', 'ab')
        ->set('form.description', 'short')
        ->set('form.rewardTitle', 'ab')
        ->set('form.rewardDescription', 'short')
        ->set('form.punishmentTitle', 'ab')
        ->set('form.punishmentDescription', 'short')
        ->call('submitTask')
        ->assertHasErrors([
            'form.title' => 'min',
            'form.description' => 'min',
            'form.rewardTitle' => 'min',
            'form.rewardDescription' => 'min',
            'form.punishmentTitle' => 'min',
            'form.punishmentDescription' => 'min',
        ]);
});

it('shows task outcomes in browse view', function () {
    $task = Task::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Task with Outcomes',
    ]);

    $reward = Outcome::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Test Reward',
        'intended_type' => 'reward',
    ]);

    $punishment = Outcome::factory()->create([
        'status' => ContentStatus::Approved,
        'title' => 'Test Punishment',
        'intended_type' => 'punishment',
    ]);

    $task->recommendedOutcomes()->attach([
        $reward->id => ['sort_order' => 1],
        $punishment->id => ['sort_order' => 2],
    ]);

    Livewire::test(TaskCommunityDashboard::class)
        ->assertSee('Test Reward')
        ->assertSee('Test Punishment')
        ->assertSee('Reward')
        ->assertSee('Punishment');
});

it('requires authentication to access', function () {
    auth()->logout();

    $this->get('/app/tasks/community')
        ->assertRedirect('/login');
});
