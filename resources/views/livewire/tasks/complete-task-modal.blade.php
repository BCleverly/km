{{-- Complete Task Modal --}}
<div>
    @if($assignedTask)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ showModal: true }" x-show="showModal" x-cloak>
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/50 transition-opacity" 
             x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showModal = false; $wire.call('clearAssignedTask')"></div>

        {{-- Modal --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow-xl transition-all"
                 x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Complete Task
                    </h3>
                    <button @click="showModal = false; $wire.call('clearAssignedTask')" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-6 space-y-6">
                    {{-- Task Information --}}
                    @if($assignedTask)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                {{ $assignedTask->task->title }}
                            </h4>
                            
                            <div class="prose dark:prose-invert max-w-none mb-4">
                                <p class="text-gray-600 dark:text-gray-300">{{ $assignedTask->task->description }}</p>
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Level {{ $assignedTask->task->difficulty_level }}
                                </span>
                                @if($assignedTask->deadline_at)
                                    <span>Due: {{ $assignedTask->deadline_at->format('M j, Y g:i A') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Completion Form --}}
                    <form wire:submit="completeTask" class="space-y-6">
                        {{-- Premium Image Upload Section --}}
                        @if($showImageUpload)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                    
                                    <h4 class="text-sm font-medium text-purple-900 dark:text-purple-100">Premium Feature: Show Off Your Completion</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    {{-- Image Upload --}}
                                    <div>
                                        <label for="completionImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Upload Completion Image (Optional)
                                        </label>
                                        <input type="file" 
                                               id="completionImage" 
                                               wire:model="completionImage" 
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900 dark:file:text-purple-300">
                                        @error('completionImage')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                        
                                        {{-- Image Preview --}}
                                        @if($completionImage)
                                            <div class="mt-3">
                                                <img src="{{ $completionImage->temporaryUrl() }}" 
                                                     alt="Preview" 
                                                     class="max-w-xs rounded-lg shadow-sm border border-gray-200 dark:border-gray-600">
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Completion Note --}}
                                    <div>
                                        <label for="completionNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Completion Note (Optional)
                                        </label>
                                        <textarea id="completionNote" 
                                                  wire:model="completionNote"
                                                  rows="3"
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                                                  placeholder="Share your experience or thoughts about completing this task..."></textarea>
                                        @error('completionNote')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Free User Message --}}
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Upgrade to Premium</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Premium users can upload images to show off their task completions and add completion notes. 
                                    <a href="/subscription" class="text-purple-600 dark:text-purple-400 hover:underline">Upgrade now</a> to unlock this feature!
                                </p>
                            </div>
                        @endif

                        {{-- Error Display --}}
                        @error('general')
                            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <p class="text-sm text-red-800 dark:text-red-200">{{ $message }}</p>
                                </div>
                            </div>
                        @enderror
                    </form>
                </div>

                {{-- Footer --}}
                @if($assignedTask)
                    <div class="flex gap-3 p-6 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <button type="button" 
                                wire:click="completeTask"
                                wire:loading.attr="disabled"
                                wire:target="completeTask"
                                class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="completeTask">I Completed This Task</span>
                            <span wire:loading wire:target="completeTask" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Completing...
                            </span>
                        </button>
                        
                        <button type="button" 
                                wire:click="failTask"
                                wire:loading.attr="disabled"
                                wire:target="completeTask"
                                class="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            I Failed This Task
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>