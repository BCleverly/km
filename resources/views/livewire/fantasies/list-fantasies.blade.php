<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Fantasies</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Share and explore fantasies from the community</p>
        </div>
        <a href="{{ route('app.fantasies.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            Share Fantasy
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Search Fantasies
                </label>
                <input
                    type="text"
                    id="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search fantasy content..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
            </div>

            <!-- Premium Filter -->
            <div>
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model.live="showPremium"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show Premium</span>
                </label>
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button
                    wire:click="clearFilters"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer"
                >
                    Clear filters
                </button>
            </div>
        </div>
    </div>

    <!-- Fantasies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-4 xl:gap-5 2xl:gap-6 mb-8">
        @forelse($this->fantasies as $fantasy)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                <div class="p-4 xl:p-5 2xl:p-6 flex-1 flex flex-col">
                    <!-- Fantasy Content -->
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-4">
                            {{ $fantasy->content }}
                        </p>
                    </div>

                    <!-- Word Count -->
                    <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        <span class="font-medium">{{ $fantasy->word_count }} words</span>
                    </div>

                    <!-- Premium Badge -->
                    @if($fantasy->is_premium)
                        <div class="mb-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Premium
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Author Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                            <span>By {{ $fantasy->user->display_name }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $fantasy->created_at->diffForHumans() }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $fantasy->getViewCount() }} views</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <!-- Report Button -->
                            <button
                                wire:click="reportFantasy({{ $fantasy->id }})"
                                class="text-red-500 hover:text-red-700 text-xs"
                                title="Report this fantasy"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </button>
                            
                            <!-- Reaction Button -->
                            <livewire:components.reaction-button 
                                model-type="fantasy" 
                                :model-id="$fantasy->id" 
                                :key="'reaction-fantasy-' . $fantasy->id"
                            />
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No fantasies found</h3>
                    <p>Try adjusting your search criteria or be the first to share a fantasy!</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->fantasies->hasPages())
        <div class="flex justify-center">
            {{ $this->fantasies->links() }}
        </div>
    @endif
</div>