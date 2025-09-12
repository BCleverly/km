<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-4 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Stories</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Read and share stories from the community</p>
            </div>
            <a href="{{ route('app.stories.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-medium transition-colors text-sm">
                Write Story
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="px-4 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row gap-4">
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
                        placeholder="Search stories..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                    >
                </div>
            </div>

            <!-- Premium Filter -->
            <div class="flex items-center gap-3">
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

                @if($search || $showPremium)
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

    <!-- Stories Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        @forelse($this->stories as $story)
            <div class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors flex flex-col min-h-[200px]">
                <div class="p-4 flex-1 flex flex-col">
                    <!-- Author Info -->
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($story->user->display_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if($story->user->profile?->username)
                                    <a href="{{ route('app.profile', $story->user->profile->username) }}" 
                                       class="font-semibold text-gray-900 dark:text-white hover:underline">
                                        {{ $story->user->display_name }}
                                    </a>
                                @else
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $story->user->display_name }}</span>
                                @endif
                                
                                @if($story->is_premium)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Premium
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $story->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>{{ $story->getViewCount() }} views</span>
                                <span>•</span>
                                <span>{{ $story->word_count }} words</span>
                                <span>•</span>
                                <span>{{ $story->reading_time_minutes }} min read</span>
                            </div>
                        </div>
                    </div>

                    <!-- Story Title -->
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">
                        {{ $story->title }}
                    </h3>

                    <!-- Story Summary -->
                    <div class="mb-4 flex-1">
                        <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-3">
                            {{ $story->summary }}
                        </p>
                    </div>

                    <!-- Tags -->
                    @if($story->tags->count() > 0)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($story->tags as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
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
                                model-type="story" 
                                :model-id="$story->id" 
                                :key="'reaction-story-' . $story->id"
                            />
                            
                            <!-- Read More Button -->
                            <a href="{{ route('app.stories.show', $story) }}" 
                               class="flex items-center gap-2 text-gray-500 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span class="text-sm">Read</span>
                            </a>
                        </div>
                        
                        <!-- Report Button -->
                        <button
                            wire:click="reportStory({{ $story->id }})"
                            class="text-gray-400 hover:text-red-500 transition-colors"
                            title="Report this story"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No stories found</h3>
                    <p class="text-sm">Try adjusting your search criteria or be the first to write a story!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->stories->hasPages())
        <div class="px-4 py-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-center">
                {{ $this->stories->links() }}
            </div>
        </div>
    @endif
</div>