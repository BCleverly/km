<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-4 py-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('app.stories.index') }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $story->title }}</h1>
        </div>
    </div>

    <!-- Story Content -->
    <div class="px-4 py-6">
        <!-- Author Info -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
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
                    <span>{{ $story->created_at->format('M j, Y') }}</span>
                    <span>•</span>
                    <span>{{ $story->word_count }} words</span>
                    <span>•</span>
                    <span>{{ $story->reading_time_minutes }} min read</span>
                    <span>•</span>
                    <span>{{ $story->getViewCount() }} views</span>
                </div>
            </div>
        </div>

        <!-- Story Summary -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
            <h2 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Summary</h2>
            <div class="text-blue-800 dark:text-blue-200 text-sm prose prose-sm dark:prose-invert max-w-none">
                <x-markdown>
                    {{ $story->summary }}
                </x-markdown>
            </div>
        </div>

        <!-- Tags -->
        @if($story->tags->count() > 0)
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($story->tags as $tag)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Story Content -->
        <div class="prose prose-lg dark:prose-invert max-w-none mb-8">
            <!-- <x-markdown>
                {{ $story->content }}
            </x-markdown> -->

            {{ $story->content }}
        </div>

        <!-- Connected Stories -->
        @if($this->connectedStories->count() > 0 || $this->connectingStories->count() > 0)
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Related Stories</h2>
                
                @if($this->connectedStories->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-3">Connected Stories</h3>
                        <div class="space-y-3">
                            @foreach($this->connectedStories as $connectedStory)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $connectedStory->title }}</h4>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2 prose prose-xs dark:prose-invert max-w-none">
                                            <x-markdown>
                                                {{ $connectedStory->summary }}
                                            </x-markdown>
                                        </div>
                                    </div>
                                    <a href="{{ route('app.stories.show', $connectedStory) }}" 
                                       class="ml-3 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium whitespace-nowrap">
                                        Read
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($this->connectingStories->count() > 0)
                    <div>
                        <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-3">Stories that connect to this one</h3>
                        <div class="space-y-3">
                            @foreach($this->connectingStories as $connectingStory)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ $connectingStory->title }}</h4>
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2 prose prose-xs dark:prose-invert max-w-none">
                                            <x-markdown>
                                                {{ $connectingStory->summary }}
                                            </x-markdown>
                                        </div>
                                    </div>
                                    <a href="{{ route('app.stories.show', $connectingStory) }}" 
                                       class="ml-3 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium whitespace-nowrap">
                                        Read
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Comments Section -->
        <div class="mb-8">
            <livewire:comments.comments-list 
                :model-path="'App\Models\Story:' . $story->id" 
                :key="'comments-story-' . $story->id"
            />
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-6">
                <!-- Reaction Button -->
                <livewire:components.reaction-button 
                    model-type="story" 
                    :model-id="$story->id" 
                    :key="'reaction-story-' . $story->id"
                />
            </div>
            
            <!-- Report Button -->
            <button
                wire:click="reportStory"
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