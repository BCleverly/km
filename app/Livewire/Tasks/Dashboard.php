<?php

declare(strict_types=1);

namespace App\Livewire\Tasks;

use App\Actions\Tasks\AssignRandomTask;
use App\Actions\Tasks\CompleteTask;
use App\Actions\Tasks\FailTask;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tasks Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $recentActivities = $user->recentTaskActivities(5)->get();
        $activeTask = $user->stats()->getActiveTask();
        $streakStats = $user->stats()->getStreakStats();
        
        return view('livewire.tasks.dashboard', [
            'recentActivities' => $recentActivities,
            'activeTask' => $activeTask,
            'streakStats' => $streakStats,
        ]);
    }

    /**
     * Assign a random task to the user
     */
    public function assignRandomTask()
    {
        $user = auth()->user();
        $result = AssignRandomTask::run($user);
        
        $this->dispatch('notify', [
            'type' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ]);
    }

    /**
     * Complete the current active task
     */
    public function completeTask()
    {
        $user = auth()->user();
        $result = CompleteTask::run($user);
        
        $this->dispatch('notify', [
            'type' => $result['success'] ? 'success' : 'error',
            'message' => $result['message']
        ]);
    }

    /**
     * Fail the current active task
     */
    public function failTask()
    {
        $user = auth()->user();
        $result = FailTask::run($user);
        
        $this->dispatch('notify', [
            'type' => $result['success'] ? 'warning' : 'error',
            'message' => $result['message']
        ]);
    }
}