<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Welcome to your kinky dashboard</h1>    
    
    <!-- 12 Column Grid Layout -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Regular Task Widget - 6 columns on desktop, 12 on mobile -->
        <div class="col-span-12 lg:col-span-6">
            <livewire:manage-widget />
        </div>
        
        <!-- Couple Task Widget - 6 columns on desktop, 12 on mobile -->
        <div class="col-span-12 lg:col-span-6">
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
    </div>
</div>