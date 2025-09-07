<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Tasks\UserAssignedTask;
use App\TaskStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing assigned tasks that don't have deadlines
        $assignedTasksWithoutDeadlines = UserAssignedTask::where('status', TaskStatus::Assigned)
            ->whereNull('deadline_at')
            ->with('task')
            ->get();

        foreach ($assignedTasksWithoutDeadlines as $assignedTask) {
            if ($assignedTask->task && $assignedTask->task->hasValidDuration()) {
                // Calculate deadline based on task duration and when it was assigned
                $deadline = $assignedTask->task->calculateDeadline($assignedTask->assigned_at);
                
                // Update the task with the calculated deadline
                $assignedTask->update(['deadline_at' => $deadline]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need to be reversed as it only adds data
        // and doesn't modify the schema
    }
};