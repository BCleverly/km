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
        $activeReward = $user->getCurrentActiveReward();
        $activePunishment = $user->getCurrentActivePunishment();
        $activeOutcomeCount = $user->getActiveOutcomeCount();
        $maxActiveOutcomes = $user->getMaxActiveOutcomes();
        $remainingSlots = $user->getRemainingOutcomeSlots();
        
        return view('livewire.tasks.dashboard', [
            'recentActivities' => $recentActivities,
            'activeTask' => $activeTask,
            'streakStats' => $streakStats,
            'activeReward' => $activeReward,
            'activePunishment' => $activePunishment,
            'activeOutcomeCount' => $activeOutcomeCount,
            'maxActiveOutcomes' => $maxActiveOutcomes,
            'remainingSlots' => $remainingSlots,
        ]);
    }

    /**
     * Assign a random task to the user
     */
    public function assignRandomTask()
    {
        $user = auth()->user();
        
        // Check if user has reached their outcome limit
        if ($user->hasReachedOutcomeLimit()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'message' => 'You have reached your maximum of ' . $user->getMaxActiveOutcomes() . ' active outcomes. Complete or let some expire to get new tasks.'
            ]);
            return;
        }
        
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
     * Show the completion modal with image upload
     */
    public function showCompletionModal()
    {
        $this->dispatch('show-completion-modal');
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

    /**
     * Mark an outcome as completed
     */
    public function completeOutcome($outcomeId)
    {
        $user = auth()->user();
        $outcome = $user->outcomes()->find($outcomeId);
        
        if (!$outcome) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Outcome not found'
            ]);
            return;
        }

        if ($outcome->status !== 'active') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'This outcome is not active'
            ]);
            return;
        }

        $outcome->markAsCompleted();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => ucfirst($outcome->outcome_type_label) . ' marked as completed!'
        ]);
    }
}