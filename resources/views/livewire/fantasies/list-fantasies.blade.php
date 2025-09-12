<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-4 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Fantasies</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Share and explore fantasies</p>
            </div>
            <a href="{{ route('app.fantasies.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-full font-medium transition-colors text-sm">
                Share
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
                        placeholder="Search fantasies..."
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

    <!-- Fantasies Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        @forelse($this->fantasies as $fantasy)
            <div class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors flex flex-col min-h-[200px]">
                <div class="p-4 flex-1 flex flex-col">
                    <!-- Author Info -->
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            @if($fantasy->is_anonymous)
                                ?
                            @else
                                {{ substr($fantasy->user->display_name, 0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if($fantasy->is_anonymous)
                                    <span class="font-semibold text-gray-900 dark:text-white">Anonymous</span>
                                @else
                                    @if($fantasy->user->profile?->username)
                                        <a href="{{ route('app.profile', $fantasy->user->profile->username) }}" 
                                           class="font-semibold text-gray-900 dark:text-white hover:underline">
                                            {{ $fantasy->user->display_name }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $fantasy->user->display_name }}</span>
                                    @endif
                                @endif
                                
                                @if($fantasy->is_premium)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        Premium
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $fantasy->created_at->diffForHumans() }}</span>
                                <span>•</span>
                                <span>{{ $fantasy->getViewCount() }} views</span>
                                <span>•</span>
                                <span>{{ $fantasy->word_count }} words</span>
                            </div>
                        </div>
                    </div>

                    <!-- Fantasy Content -->
                    <div class="mb-4 flex-1">
                        <p class="text-gray-900 dark:text-white leading-relaxed whitespace-pre-wrap">
                            {{ $fantasy->content }}
                        </p>
                    </div>

                    <!-- Tags -->
                    @if($fantasy->tags->count() > 0)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach($fantasy->tags as $tag)
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
                                model-type="fantasy" 
                                :model-id="$fantasy->id" 
                                :key="'reaction-fantasy-' . $fantasy->id"
                            />
                            
                            <!-- Share Button -->
                            <button class="flex items-center gap-2 text-gray-500 hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                </svg>
                                <span class="text-sm">Share</span>
                            </button>
                        </div>
                        
                        <!-- Report Button -->
                        <button
                            wire:click="reportFantasy({{ $fantasy->id }})"
                            class="text-gray-400 hover:text-red-500 transition-colors p-1"
                            title="Report this fantasy"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-16 w-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No fantasies found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search criteria or be the first to share a fantasy!</p>
                    <a href="{{ route('app.fantasies.create') }}" 
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Share Your First Fantasy
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->fantasies->hasPages())
        <div class="px-4 py-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-center">
                {{ $this->fantasies->links() }}
            </div>
        </div>
    @endif
</div>