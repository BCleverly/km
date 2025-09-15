<div class="min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                My Partner Tasks
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Tasks assigned by your dominant partner. Don't disappoint them!
            </p>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Overdue Tasks -->
        @if($overdueTasks->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-red-600 dark:text-red-400 mb-4">⚠️ Overdue Tasks</h2>
                <div class="space-y-4">
                    @foreach($overdueTasks as $task)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-red-900 dark:text-red-100">{{ $task->title }}</h3>
                                    <p class="text-red-700 dark:text-red-300 mt-1">{{ $task->description }}</p>
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                        Assigned by: {{ $task->assignedBy->display_name }} • 
                                        Due: {{ $task->deadline_at->diffForHumans() }}
                                    </p>
                                </div>
                                <button wire:click="selectTask({{ $task->id }})" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Complete Now
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Active Tasks -->
        @if($activeTasks->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📋 Active Tasks</h2>
                <div class="grid gap-4">
                    @foreach($activeTasks as $task)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $task->title }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            {{ $task->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 mb-3">{{ $task->description }}</p>
                                    
                                    @if($task->dom_message)
                                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-3">
                                            <p class="text-sm text-red-800 dark:text-red-200">
                                                <strong>Message from {{ $task->assignedBy->display_name }}:</strong><br>
                                                {{ $task->dom_message }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span>Assigned by: {{ $task->assignedBy->display_name }}</span>
                                        <span>Difficulty: Level {{ $task->difficulty_level }}</span>
                                        <span>Due: {{ $task->deadline_at->diffForHumans() }}</span>
                                        @if($task->reward)
                                            <span class="text-green-600 dark:text-green-400">Reward: {{ $task->reward->title }}</span>
                                        @endif
                                        @if($task->punishment)
                                            <span class="text-red-600 dark:text-red-400">Punishment: {{ $task->punishment->title }}</span>
                                        @endif
                                    </div>
                                </div>
                                <button wire:click="selectTask({{ $task->id }})" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    Complete Task
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Completed Tasks -->
        @if($completedTasks->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">✅ Recent Completed Tasks</h2>
                <div class="space-y-4">
                    @foreach($completedTasks as $task)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $task->title }}</h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $task->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $task->description }}</p>
                                    
                                    @if($task->completion_notes)
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                            <strong>Completion Notes:</strong> {{ $task->completion_notes }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span>Assigned by: {{ $task->assignedBy->display_name }}</span>
                                        <span>Completed: {{ $task->completed_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                @if($task->canBeThanked())
                                    <button wire:click="selectTask({{ $task->id }})" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Thank Partner
                                    </button>
                                @elseif($task->thanked_at)
                                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                                        Thanked {{ $task->thanked_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- No Tasks Message -->
        @if($activeTasks->count() === 0 && $completedTasks->count() === 0 && $overdueTasks->count() === 0)
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No tasks yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your partner hasn't assigned you any tasks yet.</p>
            </div>
        @endif
    </div>

    <!-- Include the task modal -->
    @include('livewire.couple-tasks.task-modal')
</div>