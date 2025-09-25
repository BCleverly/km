<div class="min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Cover Photo Section -->
        <div class="relative">
            @if($this->coverPhotoUrl)
                <div class="h-48 sm:h-64 md:h-80 bg-gradient-to-r from-red-500 to-pink-500">
                    <img src="{{ $this->coverPhotoUrl }}" 
                         alt="{{ $this->displayName }}'s cover photo" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>
            @else
                <div class="h-48 sm:h-64 md:h-80 bg-gradient-to-r from-red-500 to-pink-500"></div>
            @endif

            <!-- Profile Picture -->
            <div class="absolute -bottom-12 sm:-bottom-16 left-4 sm:left-6">
                <div class="relative">
                    <img src="{{ $this->profilePictureUrl }}" 
                         alt="{{ $this->displayName }}" 
                         class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white dark:border-gray-900 shadow-lg object-cover">
                    
                    @if($this->isOwnProfile)
                        <!-- Edit Profile Picture Button -->
                        <button 
                            wire:click="toggleEditForm"
                            class="absolute -bottom-1 -right-1 sm:-bottom-2 sm:-right-2 bg-red-600 hover:bg-red-700 text-white p-1.5 sm:p-2 rounded-full shadow-lg transition-colors"
                            title="Edit Profile">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6 pt-16 sm:pt-20 pb-4 sm:pb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <!-- Profile Info -->
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $this->displayName }}
                        </h1>
                        
                        @if($this->about)
                            <p class="text-gray-600 dark:text-gray-300 mb-3 max-w-2xl text-sm sm:text-base">
                                {{ $this->about }}
                            </p>
                        @endif

                        <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Joined {{ $this->joinedDate }}
                            </span>
                            
                            @if($this->profile?->bdsm_role)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $this->profile->bdsm_role->label() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        @if($this->isOwnProfile)
                            <button
                                wire:click="toggleEditForm"
                                class="px-3 py-2 sm:px-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium text-sm sm:text-base cursor-pointer">
                                Edit Profile
                            </button>
                        @else
                            <button class="px-3 py-2 sm:px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm sm:text-base cursor-pointer">
                                Follow
                            </button>
                            <button class="px-3 py-2 sm:px-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm sm:text-base cursor-pointer">
                                Message
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                <div class="grid grid-cols-3 gap-2 sm:gap-4 max-w-md">
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $this->completedTasksCount }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $this->currentStreak }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Streak</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $this->totalPoints }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Points</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 sm:px-6">
                <nav class="flex space-x-4 sm:space-x-8 overflow-x-auto">
                    <button 
                        wire:click="setActiveTab('posts')"
                        @class([
                            'py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer',
                            'border-red-500 text-red-600' => $activeTab === 'posts',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' => $activeTab !== 'posts'
                        ])>
                        Posts
                    </button>
                    <button 
                        wire:click="setActiveTab('about')"
                        @class([
                            'py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer',
                            'border-red-500 text-red-600' => $activeTab === 'about',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' => $activeTab !== 'about'
                        ])>
                        About
                    </button>
                    <button 
                        wire:click="setActiveTab('activity')"
                        @class([
                            'py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer',
                            'border-red-500 text-red-600' => $activeTab === 'activity',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' => $activeTab !== 'activity'
                        ])>
                        Activity
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        @if($activeTab === 'posts')
            <div class="max-w-4xl mx-auto p-2 sm:p-3">
                <!-- Main Feed -->
                <div class="space-y-2">
                        <!-- Status Creation -->
                        @if($this->isOwnProfile)
                            <livewire:status.create-status-with-image />
                        @endif

                    <!-- Status Feed -->
                    <div class="space-y-2">
                        <livewire:status.status-list-with-images :user="$this->user" :limit="10" />
                    </div>
                </div>
            </div>
        @elseif($activeTab === 'about')
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
                                    {{ $this->isOwnProfile ? 'You haven\'t added any information about yourself yet.' : 'No information available.' }}
                                </p>
                            @endif
                            
                            @if($this->profile?->bdsm_role)
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">BDSM Role</h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                        {{ $this->profile->bdsm_role->label() }}
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
        @elseif($activeTab === 'activity')
            <div class="p-4 sm:p-6">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
                        
                        @php
                            $recentActivities = $this->recentActivities;
                        @endphp
                        
                        @if($recentActivities->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentActivities as $activity)
                                    <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-shrink-0">
                                            @if($activity->activity_type === \App\TaskActivityType::Completed)
                                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $activity->activity_type->label() }}: {{ $activity->task->title }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $activity->activity_at->diffForHumans() }}
                                            </p>
                                            
                                            {{-- Completion Image Display --}}
                                            @if($activity->activity_type === \App\TaskActivityType::Completed && $activity->userAssignedTask && $activity->userAssignedTask->has_completion_image)
                                                @php
                                                    $completionImage = $activity->userAssignedTask->getFirstMedia('completion_images');
                                                @endphp
                                                @if($completionImage)
                                                    <div class="mt-3">
                                                        <img src="{{ $completionImage->getUrl('medium') }}" 
                                                             alt="Task completion image" 
                                                             class="w-full max-w-xs rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 cursor-pointer hover:shadow-md transition-shadow"
                                                             onclick="openImageModal('{{ $completionImage->getUrl('large') }}')">
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent activity</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $this->isOwnProfile ? 'You haven\'t completed any tasks yet.' : 'This user hasn\'t completed any tasks yet.' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Edit Profile Modal -->
    @if($this->showEditForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showEditForm') }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                <!-- Modal Content -->
                <div class="inline-block w-full max-w-2xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Profile</h3>
                    </div>
                    
                    <div class="px-6 py-4">
                        <form wire:submit="save" class="space-y-6">
                            <!-- Username Field -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Username
                                </label>
                                <input type="text" 
                                       id="username" 
                                       value="{{ $form->username }}"
                                       wire:model="form.username" 
                                       @class([
                                           'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                           'border-red-500' => $errors->has('form.username')
                                       ])
                                       placeholder="janesmith" 
                                       required>
                                @error('form.username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- About Field -->
                            <div>
                                <label for="about" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    About
                                </label>
                                <textarea id="about" 
                                          wire:model="form.about" 
                                          rows="4" 
                                          @class([
                                              'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                              'border-red-500' => $errors->has('form.about')
                                          ])
                                          placeholder="Tell us about yourself..."></textarea>
                                @error('form.about')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- BDSM Role Field -->
                            <div>
                                <label for="bdsm_role" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    BDSM Role Preference
                                </label>
                                <select id="bdsm_role" 
                                        wire:model="form.bdsm_role" 
                                        @class([
                                            'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                            'border-red-500' => $errors->has('form.bdsm_role')
                                        ])>
                                    <option value="">Select your BDSM role preference (optional)</option>
                                    @foreach($this->getBdsmRoleOptions() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.bdsm_role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Profile Picture Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Profile Picture
                                </label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex items-center justify-center relative">
                                            @if($form->profile_picture)
                                                @if($this->getProfilePicturePreviewUrl())
                                                    <img src="{{ $this->getProfilePicturePreviewUrl() }}" 
                                                         alt="Profile picture preview" 
                                                         class="h-full w-full object-cover">
                                                @endif
                                            @elseif(auth()->user()->profile?->getFirstMedia('profile_pictures'))
                                                <img src="{{ $this->getStoredProfilePictureUrl() }}" 
                                                     alt="{{ auth()->user()->display_name }}" 
                                                     class="h-full w-full object-cover">
                                            @else
                                                <img src="{{ auth()->user()->gravatar_url }}" 
                                                     alt="{{ auth()->user()->display_name }}" 
                                                     class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <label class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors cursor-pointer">
                                            <input type="file" 
                                                   wire:model="form.profile_picture" 
                                                   accept="image/*" 
                                                   class="sr-only">
                                            Change
                                        </label>
                                        @if($form->profile_picture || auth()->user()->profile?->getFirstMedia('profile_pictures'))
                                            <button type="button" 
                                                    wire:click="{{ $form->profile_picture ? '$set(\'form.profile_picture\', null)' : 'removeProfilePicture' }}"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-medium rounded-md transition-colors cursor-pointer">
                                                Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @error('form.profile_picture')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Cover Photo Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                    Cover Photo
                                </label>
                                
                                @if($form->cover_photo)
                                    <div class="relative group">
                                        <div class="h-32 w-full rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600">
                                            @if($this->getCoverPhotoPreviewUrl())
                                                <img src="{{ $this->getCoverPhotoPreviewUrl() }}" 
                                                     alt="Cover photo preview" 
                                                     class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div class="absolute top-2 right-2 flex space-x-2 z-10">
                                            <label class="bg-white/95 hover:bg-white border border-gray-300 rounded-md px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer shadow-lg">
                                                <input type="file" 
                                                       wire:model="form.cover_photo" 
                                                       accept="image/*" 
                                                       class="sr-only">
                                                Change
                                            </label>
                                            <button type="button" 
                                                    wire:click="$set('form.cover_photo', null)"
                                                    class="bg-red-600/95 hover:bg-red-700 text-white px-3 py-1 text-xs font-medium rounded-md transition-colors cursor-pointer shadow-lg">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @elseif(auth()->user()->profile?->getFirstMedia('cover_photos'))
                                    <div class="relative group">
                                        <div class="h-32 w-full rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 relative">
                                            <img src="{{ $this->getStoredCoverPhotoUrl() }}" 
                                                 alt="Cover photo" 
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div class="absolute top-2 right-2 flex space-x-2 z-10">
                                            <label class="bg-white/95 hover:bg-white border border-gray-300 rounded-md px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer shadow-lg">
                                                <input type="file" 
                                                       wire:model="form.cover_photo" 
                                                       accept="image/*" 
                                                       class="sr-only">
                                                Change
                                            </label>
                                            <button type="button" 
                                                    wire:click="removeCoverPhoto"
                                                    class="bg-red-600/95 hover:bg-red-700 text-white px-3 py-1 text-xs font-medium rounded-md transition-colors cursor-pointer shadow-lg">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                                    <span>Upload a file</span>
                                                    <input type="file" 
                                                           wire:model="form.cover_photo" 
                                                           accept="image/*" 
                                                           class="sr-only">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG, GIF up to 10MB
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                
                                @error('form.cover_photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </form>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="show = false"
                            class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button 
                            type="button"
                            wire:click="save" 
                            wire:loading.attr="disabled" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
