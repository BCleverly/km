<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6" x-data="{ showComments: false }">
    <div class="flex items-start space-x-3">
        <img 
            src="{{ $status->user->profile_picture_url }}" 
            alt="{{ $status->user->display_name }}"
            class="w-10 h-10 rounded-full object-cover"
        >
        
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
                <h3 class="font-medium text-gray-900 dark:text-white">
                    {{ $status->user->display_name }}
                </h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                <time 
                    class="text-sm text-gray-500 dark:text-gray-400"
                    title="{{ $this->formattedDate }}"
                >
                    {{ $this->timeAgo }}
                </time>
                
                @if($status->is_public)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Public
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        Private
                    </span>
                @endif
            </div>
            
            <div class="mt-2 status-content">
                <p class="text-gray-900 dark:text-white whitespace-pre-wrap status-text">{{ $status->content }}</p>
            </div>
            
            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Reaction Button -->
                    <livewire:components.reaction-button 
                        :model-type="'status'" 
                        :model-id="$status->id" 
                    />
                    
                    <!-- Comments Button -->
                    <button
                        @click="showComments = !showComments"
                        class="flex items-center space-x-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                        :class="{ 'text-blue-600 dark:text-blue-400': showComments }"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <span class="text-sm">Comments</span>
                        @if($status->approved_comments_count > 0)
                            <span class="text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-1.5 py-0.5 rounded-full">
                                {{ $status->approved_comments_count }}
                            </span>
                        @endif
                    </button>
                </div>
                
                @if($this->canDelete)
                    <div class="flex items-center space-x-2">
                        <button
                            wire:click="deleteStatus"
                            wire:confirm="Are you sure you want to delete this status?"
                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                            title="Delete status"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lazy-loaded Comments Section -->
    <div x-show="showComments" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        <livewire:status.status-comments :status="$status" lazy />
    </div>
</div>