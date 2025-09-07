<!-- Filters -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Search
            </label>
            <input
                type="text"
                id="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search tasks and descriptions..."
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
        </div>

        <!-- User Type Filter -->
        <div>
            <label for="userType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                User Type
            </label>
            <select
                id="userType"
                wire:model.live="userType"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
                <option value="">All Types</option>
                @foreach($this->userTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Difficulty Filter -->
        <div>
            <label for="difficulty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Difficulty
            </label>
            <select
                id="difficulty"
                wire:model.live="difficulty"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
                <option value="">All Levels</option>
                @foreach($this->difficultyLevels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Premium Filter -->
        <div class="flex items-end">
            <label class="flex items-center">
                <input
                    type="checkbox"
                    wire:model.live="showPremium"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                >
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show Premium</span>
            </label>
        </div>
    </div>

    <!-- Clear Filters -->
    <div class="mt-4 flex justify-end">
        <button
            wire:click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
        >
            Clear all filters
        </button>
    </div>
</div>

<!-- Tasks Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @forelse($this->tasks as $task)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <!-- Task Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $task->title }}
                        </h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $task->target_user_type->label() }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Level {{ $task->difficulty_level }}
                            </span>
                            @if($task->is_premium)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Premium
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Task Description -->
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                    {{ $task->description }}
                </p>

                <!-- Duration -->
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    <span class="font-medium">Duration:</span> {{ $task->duration_display }}
                </div>

                <!-- Outcomes -->
                @if($task->recommendedOutcomes->count() > 0)
                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Outcomes:</h4>
                        <div class="space-y-1">
                            @foreach($task->recommendedOutcomes as $outcome)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $outcome->title }}</span>
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $outcome->isIntendedAsReward(),
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $outcome->isIntendedAsPunishment()
                                    ])>
                                        {{ $outcome->intended_type_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Author -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>By {{ $task->author->name ?? 'Anonymous' }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium mb-2">No tasks found</h3>
                <p>Try adjusting your filters or search terms.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($this->tasks->hasPages())
    <div class="flex justify-center">
        {{ $this->tasks->links() }}
    </div>
@endif
