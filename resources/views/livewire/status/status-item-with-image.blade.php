<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 sm:p-3">
    <div class="flex items-start space-x-2">
        <!-- Profile Picture -->
        <img 
            src="{{ $status->user->profile_picture_url }}" 
            alt="{{ $status->user->display_name }}"
            class="w-10 h-10 rounded-full object-cover flex-shrink-0"
        >
        
        <div class="flex-1 min-w-0">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $status->user->display_name }}
                    </h4>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $this->timeAgo }}
                    </span>
                </div>
                
                @if($this->canDelete)
                    <div class="flex items-center space-x-2">
                        <button
                            wire:click="deleteStatus"
                            class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer"
                            title="Delete status">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Content -->
            @if(!empty(trim($status->content)))
                <div class="mb-2 status-content">
                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap status-text">
                        {{ $status->content }}
                    </p>
                </div>
            @endif

            <!-- Image -->
            @if($status->hasImage() && $this->statusImageUrl)
                <div class="mb-2">
                    <div class="relative group cursor-pointer" wire:click="toggleImageModal">
                        <img src="{{ $this->statusImageUrl }}" 
                             alt="Status image" 
                             class="w-full h-auto rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow object-cover">
                        
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 rounded-lg transition-colors flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-2 border-t border-gray-100 dark:border-gray-700 gap-2">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Like Button -->
                    <button class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="text-sm">Like</span>
                    </button>

                    <!-- Comment Button -->
                    <button 
                        wire:click="toggleComments"
                        class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span class="text-sm">Comment</span>
                    </button>

                </div>

                <!-- Timestamp -->
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $this->formattedDate }}
                </div>
            </div>

            <!-- Comments Section -->
            @if($showComments)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Comments are currently disabled</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    @if($showImageModal && $this->statusImageLargeUrl)
        <div class="fixed inset-0 z-50 overflow-hidden" 
             x-data="{ show: @entangle('showImageModal') }" 
             x-show="show" 
             x-cloak
             @keydown.escape.window="show = false">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-20" @click="show = false"></div>

            <!-- Modal Content -->
            <div class="relative flex h-full">
                <!-- Desktop Layout: Split Screen -->
                <div class="hidden lg:flex w-full h-full">
                    <!-- Left Side: Image (2/3) -->
                    <div class="w-2/3 flex items-center justify-center bg-transparent">
                        <div class="relative w-full h-full flex items-center justify-center">
                            <!-- Close Button -->
                            <button @click="show = false" 
                                    class="absolute top-4 right-4 z-10 p-2 text-white bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-all cursor-pointer">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            
                            <!-- Image -->
                            <img :src="'{{ $this->statusImageLargeUrl }}'" 
                                 alt="Status image" 
                                 class="max-w-full max-h-full object-contain">
                        </div>
                    </div>

                    <!-- Right Side: Comments & Reactions (1/3) -->
                    <div class="w-1/3 bg-white dark:bg-gray-800 flex flex-col">
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $status->user->profile_picture_url }}" 
                                     alt="{{ $status->user->display_name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $status->user->display_name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->timeAgo }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        @if(!empty(trim($status->content)))
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $status->content }}</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-6">
                                <!-- Like Button -->
                                <button class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    <span class="font-medium">Like</span>
                                </button>

                                <!-- Comment Button -->
                                <button class="flex items-center space-x-2 text-gray-500 hover:text-blue-500 transition-colors cursor-pointer">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    <span class="font-medium">Comment</span>
                                </button>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="flex-1 p-4">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <p class="text-sm">Comments are currently disabled</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout: Full Screen Image -->
                <div class="lg:hidden w-full h-full flex items-center justify-center bg-transparent">
                    <div class="relative w-full h-full flex items-center justify-center">
                        <!-- Close Button -->
                        <button @click="show = false" 
                                class="absolute top-4 right-4 z-10 p-2 text-white bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-all cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        
                        <!-- Image -->
                        <img :src="'{{ $this->statusImageLargeUrl }}'" 
                             alt="Status image" 
                             class="max-w-full max-h-full object-contain">
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
