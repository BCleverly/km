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

    <!-- Desire Discovery Card for users with partners or admins -->
    @if(auth()->user()->partner || auth()->user()->hasRole('Admin'))
        <div class="grid grid-cols-12 gap-6 mt-6">
            <div class="col-span-12">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Desire Discovery</h2>
                    </div>

                    <div class="text-center space-y-4">
                        <div class="w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423L16.5 15.75l.394 1.183a2.25 2.25 0 001.423 1.423L19.5 18.75l-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                Explore Desires Together
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                @if(auth()->user()->partner)
                                    Discover new fantasies, fetishes, and kinks with {{ auth()->user()->partner->display_name }}. 
                                    Swipe through items and see your compatibility!
                                @else
                                    Discover new fantasies, fetishes, and kinks. 
                                    Explore the community content and manage desire items as an admin.
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('app.desire-discovery.explore') }}" 
                               class="flex-1 bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                Start Exploring
                            </a>
                            
                            <a href="{{ route('app.desire-discovery.compatibility') }}" 
                               class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                View Compatibility
                            </a>
                            
                            <a href="{{ route('app.desire-discovery.history') }}" 
                               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center">
                                Historical Review
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>