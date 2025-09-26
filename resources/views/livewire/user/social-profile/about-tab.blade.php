<div class="p-4 sm:p-6">
    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- About Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">About {{ $this->displayName }}</h3>
                @if($this->about)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                        {{ $this->about }}
                    </p>
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">
                        {{ $isOwnProfile ? 'You haven\'t added any information about yourself yet.' : 'No information available.' }}
                    </p>
                @endif
                
                @if($profile?->bdsm_role)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">BDSM Role</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                            {{ $profile->bdsm_role->label() }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Stats Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Statistics</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Tasks Completed</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total completed tasks</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->completedTasksCount }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Current Streak</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Days in a row</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->currentStreak }}</span>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Total Points</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Earned points</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->totalPoints }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>