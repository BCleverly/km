<div x-data="{ 
    showAll: false,
    showDropdown: false,
    unreadCount: @js($this->unreadNotifications->count())
}" class="relative">
    {{-- Notification Bell --}}
    <button @click="showDropdown = !showDropdown" 
            class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 rounded-lg">
        <span class="sr-only">View notifications</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                    <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
        
        {{-- Unread Badge --}}
        <span x-cloak
              x-show="unreadCount > 0" 
              x-text="unreadCount > 9 ? '9+' : unreadCount"
              class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
        </span>
    </button>

    {{-- Dropdown --}}
    <div x-cloak 
         x-show="showDropdown" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="showDropdown = false"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
        
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                <div class="flex items-center gap-2">
                    @if($this->unreadNotifications->count() > 0)
                        <button wire:click="markAllAsRead" 
                                class="text-sm text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300">
                            Mark all read
                        </button>
                    @endif
                    <button @click="showAll = !showAll" 
                            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <span x-text="showAll ? 'Show unread' : 'Show all'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="max-h-96 overflow-y-auto">
            <div x-show="!showAll">
                @if($this->unreadNotifications->count() > 0)
                    @foreach($this->unreadNotifications as $notification)
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            {{-- Notification Icon --}}
                            <div class="flex-shrink-0 mt-1">
                                @if($notification->data['type'] === 'couple_task_assigned')
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                @elseif($notification->data['type'] === 'couple_task_completed')
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @elseif($notification->data['type'] === 'couple_task_thanked')
                                    <div class="w-8 h-8 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        @if($notification->data['type'] === 'couple_task_assigned')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                New task from your dominant partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            @if($notification->data['dom_message'])
                                                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1 italic">
                                                    "{{ $notification->data['dom_message'] }}"
                                                </p>
                                            @endif
                                        @elseif($notification->data['type'] === 'couple_task_completed')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Task completed by your submissive partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            @if($notification->data['completion_notes'])
                                                <p class="text-xs text-green-600 dark:text-green-400 mt-1 italic">
                                                    "{{ $notification->data['completion_notes'] }}"
                                                </p>
                                            @endif
                                        @elseif($notification->data['type'] === 'couple_task_thanked')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Thank you from your submissive partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            <p class="text-xs text-pink-600 dark:text-pink-400 mt-1 italic">
                                                "{{ $notification->data['thank_you_message'] }}"
                                            </p>
                                        @else
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 ml-2">
                                        @if($notification->unread_at)
                                            <button wire:click="markAsRead('{{ $notification->id }}')" 
                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="Mark as read">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button wire:click="deleteNotification('{{ $notification->id }}')" 
                                                class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
</div>

                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5V7a7.5 7.5 0 1 1 15 0v10z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No unread notifications
                        </p>
                    </div>
                @endif
            </div>

            {{-- Show All Notifications --}}
            <div x-show="showAll">
                @if($this->allNotifications->count() > 0)
                    @foreach($this->allNotifications as $notification)
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex items-start gap-3">
                            {{-- Notification Icon --}}
                            <div class="flex-shrink-0 mt-1">
                                @if($notification->data['type'] === 'couple_task_assigned')
                                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                @elseif($notification->data['type'] === 'couple_task_completed')
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @elseif($notification->data['type'] === 'couple_task_thanked')
                                    <div class="w-8 h-8 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Notification Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        @if($notification->data['type'] === 'couple_task_assigned')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                New task from your dominant partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            @if($notification->data['dom_message'])
                                                <p class="text-xs text-purple-600 dark:text-purple-400 mt-1 italic">
                                                    "{{ $notification->data['dom_message'] }}"
                                                </p>
                                            @endif
                                        @elseif($notification->data['type'] === 'couple_task_completed')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Task completed by your submissive partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            @if($notification->data['completion_notes'])
                                                <p class="text-xs text-green-600 dark:text-green-400 mt-1 italic">
                                                    "{{ $notification->data['completion_notes'] }}"
                                                </p>
                                            @endif
                                        @elseif($notification->data['type'] === 'couple_task_thanked')
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                Thank you from your submissive partner
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                                "{{ $notification->data['title'] }}"
                                            </p>
                                            <p class="text-xs text-pink-600 dark:text-pink-400 mt-1 italic">
                                                "{{ $notification->data['thank_you_message'] }}"
                                            </p>
                                        @else
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>
                                        @endif
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-1 ml-2">
                                        @if($notification->unread_at)
                                            <button wire:click="markAsRead('{{ $notification->id }}')" 
                                                    class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="Mark as read">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                        @endif
                                        <button wire:click="deleteNotification('{{ $notification->id }}')" 
                                                class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                                                title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-5a7.5 7.5 0 1 0-15 0v5h5l-5 5-5-5h5V7a7.5 7.5 0 1 1 15 0v10z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No notifications yet
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>