<div>
    <!-- Filters -->
    <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search {{ strtolower($this->contentTypes[$contentType]) }}..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                    >
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex items-center gap-3">
                <!-- Content Type -->
                <select
                    wire:model.live="contentType"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                >
                    @foreach($this->contentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <!-- User Type Filter -->
                <select
                    wire:model.live="userType"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                >
                    <option value="">All Types</option>
                    @foreach($this->userTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <!-- Difficulty Filter -->
                <select
                    wire:model.live="difficulty"
                    class="px-3 py-2 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                >
                    <option value="">All Levels</option>
                    @foreach($this->difficultyLevels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <!-- Premium Filter -->
                <label class="flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="showPremium"
                        class="sr-only"
                    >
                    <div class="relative">
                        <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 rounded-full shadow-inner transition-colors duration-200 {{ $showPremium ? 'bg-blue-600' : '' }}"></div>
                        <div class="absolute w-4 h-4 bg-white rounded-full shadow top-1 left-1 transition-transform duration-200 {{ $showPremium ? 'transform translate-x-4' : '' }}"></div>
                    </div>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Premium</span>
                </label>

                @if($search || $userType || $difficulty || $showPremium)
                    <button
                        wire:click="clearFilters"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                    >
                        Clear
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Tasks Feed -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-0">
        @forelse($this->content as $item)
            <div class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors flex flex-col min-h-[200px]">
                <div class="p-4 flex-1 flex flex-col">
                    <!-- Author Info -->
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($item->author->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $item->author->name ?? 'Anonymous' }}</span>
                                
                                @if($item->is_premium)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Premium
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $item->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>{{ ucfirst($contentType) }}</span>
                                <span>•</span>
                                <span>{{ $item->target_user_type->label() }}</span>
                                <span>•</span>
                                <span>Level {{ $item->difficulty_level }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Task Content -->
                    <div class="mb-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $item->title }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ $item->description }}
                        </p>
                    </div>

                    <!-- Duration (for tasks only) -->
                    @if($contentType === 'tasks')
                        <div class="mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $item->duration_display }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if($item->tags->count() > 0)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->tags as $tag)
                                    <span @class([
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' => $tag->type === 'target_kink',
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' => $tag->type === 'required_accessories',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => !in_array($tag->type, ['target_kink', 'required_accessories'])
                                    ])>
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Outcomes (for tasks only) -->
                    @if($contentType === 'tasks' && $item->recommendedOutcomes->count() > 0)
                        <div class="mb-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Suggested outcomes:</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->recommendedOutcomes->take(3) as $outcome)
                                    <span @class([
                                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $outcome->isIntendedAsReward(),
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $outcome->isIntendedAsPunishment()
                                    ])>
                                        {{ $outcome->title }}
                                    </span>
                                @endforeach
                                @if($item->recommendedOutcomes->count() > 3)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        +{{ $item->recommendedOutcomes->count() - 3 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions Footer - Always at bottom -->
                <div class="px-4 pb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <!-- Reaction Button -->
                            <livewire:components.reaction-button 
                                :model-type="$contentType" 
                                :model-id="$item->id" 
                                :key="'reaction-' . $contentType . '-' . $item->id"
                            />
                            
                            <!-- Share Button -->
                            <button class="flex items-center gap-2 text-gray-500 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                </svg>
                                <span class="text-sm">Share</span>
                            </button>
                        </div>
                        
                        <!-- Type Badge -->
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
        @empty
            <div class="col-span-full text-center py-16">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-16 w-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No {{ strtolower($this->contentTypes[$contentType]) }} found</h3>
                    <p class="text-sm">Try adjusting your search criteria or clear the filters.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->content->hasPages())
        <div class="px-4 py-6">
            <div class="flex justify-center">
                {{ $this->content->links() }}
            </div>
        </div>
    @endif
</div>
