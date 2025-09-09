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
                        <!-- Thumbs up emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::thumbsUp() }}</span>
                    @elseif($reaction['type'] == 'dislike')
                        <!-- Thumbs down emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::thumbsDown() }}</span>
                    @elseif($reaction['type'] == 'blush')
                        <!-- Blush emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::smilingFaceWithSmilingEyes() }}</span>
                    @elseif($reaction['type'] == 'eggplant')
                        <!-- Eggplant emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::eggplant() }}</span>
                    @elseif($reaction['type'] == 'heart')
                        <!-- Heart emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::redHeart() }}</span>
                    @elseif($reaction['type'] == 'drool')
                        <!-- Drool emoji -->
                        <span class="text-xl">{{ \Spatie\Emoji\Emoji::droolingFace() }}</span>
                    @endif
                    <span class="text-xs">{{ $reaction['count'] }}</span>
                </span>
            @endforeach
        </button>
    @else
        <!-- Show thumbs up button when no reactions -->
        <button 
            @click="showModal = true"
            title="Like"
            class="flex items-center gap-1 px-3 py-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        >
            <span class="text-xl">{{ \Spatie\Emoji\Emoji::thumbsUp() }}</span>
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
        class="absolute bottom-full right-0 mb-2 z-[9999] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl p-3 w-auto"
        style="display: none;"
    >
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <!-- Thumbs Up -->
                <button 
                    wire:click="addReaction('like')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Like"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'like' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::thumbsUp() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Thumbs Down -->
                <button 
                    wire:click="addReaction('dislike')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Dislike"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'dislike' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::thumbsDown() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Blush -->
                <button 
                    wire:click="addReaction('blush')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Blush"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'blush' ? 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::smilingFaceWithSmilingEyes() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Eggplant -->
                <button 
                    wire:click="addReaction('eggplant')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Eggplant"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'eggplant' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::eggplant() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Heart -->
                <button 
                    wire:click="addReaction('heart')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Heart"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'heart' ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::redHeart() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>

                <!-- Drool -->
                <button 
                    wire:click="addReaction('drool')"
                    wire:loading.attr="disabled"
                    wire:target="addReaction"
                    title="Drool"
                    class="flex items-center justify-center w-10 h-10 text-sm rounded-lg transition-colors {{ $userReaction && $userReaction->type == 'drool' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="addReaction" class="text-xl">{{ \Spatie\Emoji\Emoji::droolingFace() }}</span>
                    <svg wire:loading wire:target="addReaction" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>
</div>