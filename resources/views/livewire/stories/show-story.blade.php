<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('app.stories.index') }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $story->title }}</h1>
        </div>
        
        <!-- Story Meta -->
        <div class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400 mb-4">
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                By {{ $story->user->display_name }}
            </div>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $story->created_at->format('M j, Y') }}
            </div>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                {{ $story->word_count }} words
            </div>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $story->reading_time_minutes }} min read
            </div>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ $story->getViewCount() }} views
            </div>
        </div>

    </div>

    <!-- Story Summary -->
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">Summary</h2>
        <p class="text-blue-800 dark:text-blue-200">{{ $story->summary }}</p>
    </div>

    <!-- Story Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-8">
        <div class="prose prose-lg dark:prose-invert max-w-none">
            {!! nl2br(e($story->content)) !!}
        </div>
    </div>

    <!-- Connected Stories -->
    @if($this->connectedStories->count() > 0 || $this->connectingStories->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Related Stories</h2>
            
            @if($this->connectedStories->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Connected Stories</h3>
                    <div class="space-y-3">
                        @foreach($this->connectedStories as $connectedStory)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $connectedStory->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $connectedStory->summary }}</p>
                                </div>
                                <a href="{{ route('app.stories.show', $connectedStory) }}" 
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                    Read
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($this->connectingStories->count() > 0)
                <div>
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Stories that connect to this one</h3>
                    <div class="space-y-3">
                        @foreach($this->connectingStories as $connectingStory)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $connectingStory->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $connectingStory->summary }}</p>
                                </div>
                                <a href="{{ route('app.stories.show', $connectingStory) }}" 
                                   class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
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
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <livewire:comments.comments-list 
            :model-path="'App\Models\Story:' . $story->id" 
            :key="'comments-story-' . $story->id"
        />
    </div>

    
    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Report Button -->
                <button
                    wire:click="reportStory"
                    class="flex items-center gap-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Report Story
                </button>
            </div>
            
            <!-- Reaction Button -->
            <livewire:components.reaction-button 
                model-type="story" 
                :model-id="$story->id" 
                :key="'reaction-story-' . $story->id"
            />
        </div>
    </div>

</div>