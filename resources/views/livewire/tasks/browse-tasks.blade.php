<div>
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- Content Type -->
        <div>
            <label for="contentType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Content Type
            </label>
            <select
                id="contentType"
                wire:model.live="contentType"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            >
                @foreach($this->contentTypes as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <!-- Search -->
        <div class="lg:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Search
            </label>
            <input
                type="text"
                id="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search {{ strtolower($this->contentTypes[$contentType]) }} and descriptions..."
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

    </div>

    <!-- Clear Filters -->
    <div class="mt-4 flex justify-end">
        <button
            wire:click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer"
        >
            Clear all filters
        </button>
    </div>
</div>

<!-- Tasks Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @forelse($this->content as $item)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
            <div class="p-6 flex-1 flex flex-col">
                <!-- Task Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $item->title }}
                        </h3>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $item->target_user_type->label() }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                Level {{ $item->difficulty_level }}
                            </span>
                            @if($item->is_premium)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Premium
                                </span>
                            @endif
                            @if($contentType === 'outcomes')
                                <span @class([
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $item->isIntendedAsReward(),
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $item->isIntendedAsPunishment()
                                ])>
                                    {{ $item->intended_type_label }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content Description -->
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">
                    {{ $item->description }}
                </p>

                <!-- Duration (for tasks only) -->
                @if($contentType === 'tasks')
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <span class="font-medium">Duration:</span> {{ $item->duration_display }}
                    </div>
                @endif

                <!-- Tags -->
                @if($item->tags->count() > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach($item->tags as $tag)
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                    'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' => $tag->type === 'target_kink',
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' => $tag->type === 'required_accessories'
                                ])>
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Outcomes (for tasks only) -->
                @if($contentType === 'tasks' && $item->recommendedOutcomes->count() > 0)
                    <div class="space-y-2">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Suggested outcomes:</h4>
                        <div class="space-y-1">
                            @foreach($item->recommendedOutcomes as $outcome)
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

            </div>
            
            <!-- Author Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($contentType) }}</span>
                    <span class="mx-2">•</span>
                    <span>By {{ $item->author->name ?? 'Anonymous' }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $item->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium mb-2">No {{ strtolower($this->contentTypes[$contentType]) }} found</h3>
                <p>Try adjusting your search criteria or clear the filters.</p>
            </div>
        </div>
    @endforelse
</div>

    <!-- Pagination -->
    @if($this->content->hasPages())
        <div class="flex justify-center">
            {{ $this->content->links() }}
        </div>
    @endif
</div>
