<div x-data="{
    showUpload: false,
    uploading: false,
    progress: 0,
    canUpload: @js($canUploadImage),
    showModalOrComplete() {
       if (this.canUpload) {
        this.showUpload = true;
       } else {
        $wire.completeTask();
       }
    },
    completeTask() {
        this.showUpload = false;
        $wire.completeTask();
    }
}" class="space-y-6">
    {{-- Regular Task Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    @if($this->activeTask)
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    
                </div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->activeTask->task->title }}</h2>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Complete by: {{ $this->activeTask->task->calculateDeadline() }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Reward</h3>
                    </div>
                    <p class="text-sm font-medium text-green-900 dark:text-green-100">{{ $this->activeTask->potentialReward->title }}</p>
                    <p class="text-xs text-green-700 dark:text-green-300">{{ $this->activeTask->potentialReward->description }}</p>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Punishment</h3>
                    </div>
                    <p class="text-sm font-medium text-red-900 dark:text-red-100">{{ $this->activeTask->potentialPunishment->title }}</p>
                    <p class="text-xs text-red-700 dark:text-red-300">{{ $this->activeTask->potentialPunishment->description }}</p>
                </div>
            </div>

            <div x-show="!showUpload" class="flex flex-col sm:flex-row gap-3">
                <button @click="showModalOrComplete" 
                        wire:loading.attr="disabled"
                        wire:target="completeTask,failTask"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    I done it!
                </button>
                <button wire:click="failTask" 
                        wire:loading.attr="disabled"
                        wire:target="failTask"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="failTask">I was naughty!</span>
                    <span wire:loading wire:target="failTask" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Failing...
                    </span>
                </button>
            </div>

            {{-- Image Upload Form --}}
            <div x-show="showUpload" x-transition class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Upload completion image (optional)</h4>
                <input type="file"
                       id="completionImage"
                       wire:model="completionImage"
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900 dark:file:text-purple-300 mb-3">
                
                @if ($completionImage)
                    <img src="{{ $completionImage->temporaryUrl() }}" alt="" class="w-full max-w-xs mx-auto rounded-lg mb-3">
                @endif
                
                <div class="flex gap-3">
                    <button @click="showUpload = false" 
                            wire:loading.attr="disabled"
                            wire:target="completeTask"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Cancel
                    </button>
                    <button @click="completeTask()" 
                            wire:loading.attr="disabled"
                            wire:target="completeTask"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <span wire:loading.remove wire:target="completeTask">Complete!</span>
                        <span wire:loading wire:target="completeTask" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Completing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="text-center space-y-4">
            <div class="flex items-center justify- mx-auto space-x-4 ">
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                
            </div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">No active task</h2>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="assignRandomTask" 
                        wire:loading.attr="disabled"
                        wire:target="assignRandomTask"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="assignRandomTask">Surprise me</span>
                    <span wire:loading wire:target="assignRandomTask" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Finding task...
                    </span>
                </button>
                <a href="{{ route('app.tasks.create') }}" wire:navigate
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center cursor-pointer">
                    I'll decide
                </a>
            </div>
        </div>
    @endif
    </div>

    {{-- Couple Task Section --}}
    @if($this->canUseCoupleTasks)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Couple Tasks</h2>
            </div>

            @if($this->activeCoupleTask)
                {{-- Active Couple Task --}}
                <div class="space-y-4">
                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg font-medium text-purple-900 dark:text-purple-100">{{ $this->activeCoupleTask->title }}</h3>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    @if($this->isSubmissive)
                                        From your dominant partner
                                    @else
                                        From {{ $this->activeCoupleTask->assignedBy->display_name }}
                                    @endif
                                </span>
                            </div>
                        
                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-3">{{ $this->activeCoupleTask->description }}</p>
                        
                        @if($this->activeCoupleTask->dom_message)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 mb-3 border border-purple-200 dark:border-purple-700">
                                <p class="text-xs font-medium text-purple-600 dark:text-purple-400 mb-1">
                                    @if($this->isSubmissive)
                                        Personal message from your dominant partner:
                                    @else
                                        Personal message from your partner:
                                    @endif
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300 italic">"{{ $this->activeCoupleTask->dom_message }}"</p>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-sm text-purple-600 dark:text-purple-400 mb-4">
                            <span>Difficulty: {{ $this->activeCoupleTask->difficulty_level }}/10</span>
                            <span>Due: {{ $this->activeCoupleTask->deadline_at->format('M j, g:i A') }}</span>
                        </div>

                        <div class="flex gap-3">
                            <a href="{{ route('app.couple-tasks.my-tasks') }}" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                View & Complete
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- No Active Couple Task --}}
                <div class="text-center space-y-4">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                @if($this->isDominant)
                                    No tasks assigned to your submissive partner
                                @elseif($this->isSubmissive)
                                    No tasks from your dominant partner
                                @else
                                    No couple tasks
                                @endif
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                @if($this->isDominant)
                                    Assign a task to your submissive partner or check for completed tasks.
                                @elseif($this->isSubmissive)
                                    Your dominant partner hasn't assigned you any tasks yet.
                                @else
                                    Couple tasks are available for partners.
                                @endif
                            </p>
                        </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        @if($this->isDominant)
                            <a href="{{ route('app.couple-tasks.send') }}" 
                               class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                Assign Task
                            </a>
                        @endif
                        
                        <a href="{{ route('app.couple-tasks.my-tasks') }}" 
                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                            View All Tasks
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
