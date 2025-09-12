<div class="max-w-4xl mx-auto">
    <!-- Search Header -->
    <div class="px-4 py-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="query"
                        placeholder="Search stories, fantasies, tasks, outcomes, and tags..."
                        class="w-full pl-10 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                    >
                </div>
            </div>

            <!-- Filters -->
            <div class="flex items-center gap-3">
                <!-- Content Type Filter -->
                <select
                    wire:model.live="type"
                    class="px-4 py-3 bg-gray-100 dark:bg-gray-800 border-0 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white dark:focus:bg-gray-700 transition-colors"
                >
                    <option value="all">All Content</option>
                    <option value="stories">Stories</option>
                    <option value="fantasies">Fantasies</option>
                    <option value="tasks">Tasks</option>
                    <option value="outcomes">Outcomes</option>
                    <option value="tags">Tags</option>
                </select>

                <!-- Premium Filter -->
                <label class="flex items-center cursor-pointer">
                    <input
                        type="checkbox"
                        wire:model.live="premium"
                        class="sr-only"
                    >
                    <div class="relative">
                        <div class="w-10 h-6 bg-gray-200 dark:bg-gray-700 rounded-full shadow-inner transition-colors duration-200 {{ $premium ? 'bg-blue-600' : '' }}"></div>
                        <div class="absolute w-4 h-4 bg-white rounded-full shadow top-1 left-1 transition-transform duration-200 {{ $premium ? 'transform translate-x-4' : '' }}"></div>
                    </div>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">Include Premium</span>
                </label>

                @if($query)
                    <button
                        wire:click="clearSearch"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                    >
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Result Counts -->
        @if($query)
            <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">Results:</span>
                @foreach($this->resultCounts as $contentType => $count)
                    @if($count > 0)
                        <button
                            wire:click="$set('type', '{{ $contentType }}')"
                            class="px-3 py-1 rounded-full transition-colors {{ $type === $contentType ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            {{ ucfirst($contentType) }} ({{ $count }})
                        </button>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <!-- Search Results -->
    @if($query)
        <div class="py-6">
            @if($this->results->count() > 0)
                <div class="space-y-4">
                    @foreach($this->results as $result)
                        <div class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="p-4">
                                <!-- Result Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ ucfirst($result['type']) }}
                                            </span>
                                            @if($result['is_premium'])
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Premium
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                            <a href="{{ $result['url'] }}" class="hover:underline">
                                                {{ $result['title'] }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                            <span>By {{ $result['author'] }}</span>
                                            <span>•</span>
                                            <span>{{ $result['created_at']->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Result Content -->
                                <div class="mb-3">
                                    <p class="text-gray-600 dark:text-gray-300 line-clamp-3">
                                        {{ Str::limit($result['content'], 200) }}
                                    </p>
                                </div>

                                <!-- Tags -->
                                @if($result['tags']->count() > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($result['tags'] as $tag)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($this->results->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $this->results->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <div class="text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-16 w-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium mb-2">No results found</h3>
                        <p class="text-sm">Try adjusting your search terms or filters.</p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Empty State with Recommended Content -->
        <div class="py-8">
            <!-- Search Prompt -->
            <div class="text-center py-8 mb-12">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-16 w-16 mb-4 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-lg font-medium mb-2">Search across all content</h3>
                    <p class="text-sm">Find stories, fantasies, tasks, outcomes, and tags that match your interests.</p>
                </div>
            </div>

            <!-- Recommended Content -->
            <div class="space-y-12">
                <!-- Popular Stories -->
                @if($this->recommendedStories->count() > 0)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Popular Stories (Past 5 Days)
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->recommendedStories as $story)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-2">
                                            <a href="{{ route('app.stories.show', $story->slug) }}" class="hover:underline">
                                                {{ $story->title }}
                                            </a>
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $story->view_count }} views
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                        {{ $story->summary }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span>By {{ $story->user->display_name ?? $story->user->name }}</span>
                                        <span>{{ $story->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($story->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($story->tags->take(3) as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Popular Fantasies -->
                @if($this->recommendedFantasies->count() > 0)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Popular Fantasies (Past 5 Days)
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->recommendedFantasies as $fantasy)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            Fantasy
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">
                                            {{ $fantasy->view_count }} views
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                        {{ Str::limit($fantasy->content, 150) }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span>By {{ $fantasy->is_anonymous ? 'Anonymous' : ($fantasy->user->display_name ?? $fantasy->user->name) }}</span>
                                        <span>{{ $fantasy->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($fantasy->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($fantasy->tags->take(3) as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Popular Tasks -->
                @if($this->recommendedTasks->count() > 0)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Popular Tasks (Past 5 Days)
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->recommendedTasks as $task)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-2">
                                            {{ $task->title }}
                                        </h3>
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ $task->view_count }} views
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Level {{ $task->difficulty_level }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                        {{ $task->description }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span>By {{ $task->author->display_name ?? $task->author->name }}</span>
                                        <span>{{ $task->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($task->tags->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($task->tags->take(3) as $tag)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- High Success Rate Tasks -->
                @if($this->popularTasksByCompletion->count() > 0)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            High Success Rate Tasks (Past 5 Days)
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->popularTasksByCompletion as $task)
                                @php
                                    $completionRate = $task->total_assignments > 0 ? round(($task->completed_count / $task->total_assignments) * 100) : 0;
                                @endphp
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-2">
                                            {{ $task->title }}
                                        </h3>
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                {{ $completionRate }}% success
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Level {{ $task->difficulty_level }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                        {{ $task->description }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span>By {{ $task->author->display_name ?? $task->author->name }}</span>
                                        <span>{{ $task->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {{ $task->completed_count }}/{{ $task->total_assignments }} completed
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Challenging Tasks -->
                @if($this->challengingTasks->count() > 0)
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            Challenging Tasks (Past 5 Days)
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($this->challengingTasks as $task)
                                @php
                                    $completionRate = $task->total_assignments > 0 ? round(($task->completed_count / $task->total_assignments) * 100) : 0;
                                @endphp
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-2">
                                            {{ $task->title }}
                                        </h3>
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                {{ $completionRate }}% success
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Level {{ $task->difficulty_level }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-3">
                                        {{ $task->description }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <span>By {{ $task->author->display_name ?? $task->author->name }}</span>
                                        <span>{{ $task->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        {{ $task->completed_count }}/{{ $task->total_assignments }} completed
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>