@php
    $model = $this->getModel();
    if (!$model) {
        return; // Don't render anything if model is not found
    }
    
    $reactions = $this->getReactions();
    $userReaction = $this->getUserReaction();
    $hasReactions = $reactions->isNotEmpty();
@endphp

    <div class="relative" x-data="{ showModal: false }" @close-reaction-modal.window="showModal = false">

    @if($hasReactions)
        <!-- Show existing reactions -->
        <button 
            @click="showModal = true"
            class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
            @foreach($reactions as $reaction)
                <span class="flex items-center gap-1">
                    @if($reaction['type'] == 'like')
                        <!-- Thumbs up -->
                        <svg class="w-4 h-4 {{ $userReaction && $userReaction->type == 'like' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.818a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                        </svg>
                    @elseif($reaction['type'] == 'dislike')
                        <!-- Thumbs down -->
                        <svg class="w-4 h-4 {{ $userReaction && $userReaction->type == 'dislike' ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v-6zM14 9.667v5.818a2 2 0 01-1.106 1.79l-.05.025A4 4 0 0111.057 18H5.64a2 2 0 01-1.962-1.608l-1.2-6A2 2 0 014.44 8H8V4a2 2 0 012-2 1 1 0 011 1v.667a4 4 0 00.8 2.4l1.4 1.866a4 4 0 01.8 2.4z"/>
                        </svg>
                    @endif
                    <span class="text-xs">{{ $reaction['count'] }}</span>
                </span>
            @endforeach
        </button>
    @else
        <!-- Show thumbs up button when no reactions -->
        <button 
            @click="showModal = true"
            class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V18m-7-8a2 2 0 01-2-2V5a2 2 0 012-2h2.343M11 7v6a2 2 0 102 0V7a2 2 0 10-2 0z"/>
            </svg>
            <span class="text-xs">Like</span>
        </button>
    @endif

    <!-- Reaction Modal -->
    <div 
        x-show="showModal" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="showModal = false"
        class="absolute bottom-full right-0 mb-2 z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl p-3 min-w-[200px]"
        style="display: none;"
    >
        <div class="space-y-2">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">React to this content</h4>
            
            <div class="flex items-center gap-3">
                <!-- Thumbs Up -->
                <button 
                    wire:click="addReaction('like')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'like' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg wire:loading.remove wire:target="addReaction" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.818a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                    </svg>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="addReaction">Like</span>
                    <span wire:loading wire:target="addReaction">Adding...</span>
                </button>

                <!-- Thumbs Down -->
                <button 
                    wire:click="addReaction('dislike')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'dislike' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg wire:loading.remove wire:target="addReaction" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v-6zM14 9.667v5.818a2 2 0 01-1.106 1.79l-.05.025A4 4 0 0111.057 18H5.64a2 2 0 01-1.962-1.608l-1.2-6A2 2 0 014.44 8H8V4a2 2 0 012-2 1 1 0 011 1v.667a4 4 0 00.8 2.4l1.4 1.866a4 4 0 01.8 2.4z"/>
                    </svg>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="addReaction">Dislike</span>
                    <span wire:loading wire:target="addReaction">Adding...</span>
                </button>
            </div>

        </div>
    </div>
</div>