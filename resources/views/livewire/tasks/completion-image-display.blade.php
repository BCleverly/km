@if($assignedTask->has_completion_image && $assignedTask->completion_image_url)
    <div class="mt-3">
        {{-- Completion Image --}}
        <div class="relative group">
            <img src="{{ $assignedTask->completion_image_thumb_url }}" 
                 alt="Task completion image" 
                 class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:opacity-80 transition-opacity"
                 wire:click="toggleFullSize">
            
            {{-- Access Level Badge --}}
            @php
                $user = $assignedTask->user;
                $isLifetime = $user->hasLifetimeSubscription();
                
                if ($isLifetime) {
                    $badgeColor = 'bg-emerald-500';
                    $icon = 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z';
                } else {
                    $badgeColor = 'bg-purple-500';
                    $icon = 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z';
                }
            @endphp
            
            <div class="absolute -top-1 -right-1 w-5 h-5 {{ $badgeColor }} rounded-full flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
        </div>

        {{-- Completion Note --}}
        @if($assignedTask->completion_note)
            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 italic">
                "{{ Str::limit($assignedTask->completion_note, 100) }}"
            </p>
        @endif

        {{-- Full Size Modal --}}
        @if($showFullSize)
            <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4"
                 wire:click="toggleFullSize">
                <div class="relative max-w-4xl max-h-full">
                    <img src="{{ $assignedTask->completion_image_large_url }}" 
                         alt="Task completion image" 
                         class="max-w-full max-h-full object-contain rounded-lg">
                    
                    {{-- Close Button --}}
                    <button wire:click="toggleFullSize" 
                            class="absolute top-4 right-4 text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif
