<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Tasks Dashboard
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-300">
            Complete tasks to earn rewards and discover new experiences.
        </p>
    </div>

    {{-- Current Task Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-8">
        @if($activeTask)
            {{-- Active Task Display --}}
            <div class="max-w-2xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Active Task</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Assigned {{ $activeTask->assigned_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                            Level {{ $activeTask->task->difficulty_level }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                        {{ $activeTask->task->title }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ $activeTask->task->description }}
                    </p>
                </div>

                {{-- Potential Outcomes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    {{-- Potential Reward --}}
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <span class="text-sm font-medium text-green-800 dark:text-green-300">Reward</span>
                        </div>
                        <h4 class="font-medium text-green-900 dark:text-green-100 mb-1">
                            {{ $activeTask->potentialReward?->title ?? 'Random Reward' }}
                        </h4>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            {{ $activeTask->potentialReward?->description ?? 'Complete the task to find out!' }}
                        </p>
                    </div>

                    {{-- Potential Punishment --}}
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-sm font-medium text-red-800 dark:text-red-300">Punishment</span>
                        </div>
                        <h4 class="font-medium text-red-900 dark:text-red-100 mb-1">
                            {{ $activeTask->potentialPunishment?->title ?? 'Random Punishment' }}
                        </h4>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            {{ $activeTask->potentialPunishment?->description ?? 'Fail the task to find out!' }}
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <button 
                        wire:click="completeTask"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md cursor-pointer"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Complete Task
                        </span>
                    </button>
                    <button 
                        wire:click="failTask"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md cursor-pointer"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Fail Task
                        </span>
                    </button>
                </div>
            </div>
        @else
            {{-- No Active Task - Show Options --}}
            <div class="text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
                    No Active Task
                </h2>
                
                <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
                    Ready for a new challenge? Choose how you'd like to get your next task.
                </p>
                
                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button 
                        wire:click="assignRandomTask"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md cursor-pointer"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Get Random Task
                        </span>
                    </button>
                    
                    <button 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md cursor-pointer"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            Create Custom Task
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Tasks Completed --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tasks Completed</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $streakStats['total_completed_tasks'] }}</p>
                </div>
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Completion Rate</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $streakStats['completion_rate'] }}%</p>
                </div>
            </div>
        </div>

        {{-- Current Streak --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Current Streak</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $streakStats['current_streak'] }} days</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
        </div>
        <div class="p-6">
            @if($recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-start space-x-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            {{-- Activity Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                    @if($activity->activity_type->color() === 'green') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($activity->activity_type->color() === 'red') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($activity->activity_type->color() === 'blue') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($activity->activity_type->color() === 'yellow') bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($activity->activity_type->color() === 'orange') bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($activity->activity_type->color() === 'purple') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                    @else bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ $activity->activity_type->icon() }}
                                </div>
                            </div>

                            {{-- Activity Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity->title }}
                                    </h4>
                                    <time class="text-xs text-gray-500 dark:text-gray-400" datetime="{{ $activity->activity_at->toISOString() }}">
                                        {{ $activity->activity_at->diffForHumans() }}
                                    </time>
</div>

                                @if($activity->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $activity->description }}
                                    </p>
                                @endif

                                {{-- Task Link --}}
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $activity->task->title }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- View All Activities Link --}}
                <div class="mt-6 text-center">
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium cursor-pointer transition-colors duration-200">
                        View all activity →
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No activity yet</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Complete your first task to see your activity history here.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>