<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="{ activeTab: 'posts' }">
        <div class="max-w-6xl mx-auto">
        <!-- Enhanced Cover Photo Section -->
        <div class="relative overflow-hidden">
            @if($this->coverPhotoUrl)
                <div class="h-56 sm:h-72 md:h-96 bg-gradient-to-br from-red-500 via-pink-500 to-purple-600">
                    <img src="{{ $this->coverPhotoUrl }}" 
                         alt="{{ $this->displayName }}'s cover photo" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
                </div>
            @else
                <div class="h-56 sm:h-72 md:h-96 bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/90 via-pink-500/90 to-purple-600/90"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center text-white/80">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium">{{ $this->isOwnProfile ? 'Add a cover photo' : 'No cover photo' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Enhanced Profile Picture -->
            <div class="absolute -bottom-16 sm:-bottom-20 left-6 sm:left-8">
                <div class="relative group">
                    <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full border-4 border-white dark:border-gray-900 shadow-2xl overflow-hidden bg-white dark:bg-gray-800">
                        <img src="{{ $this->profilePictureUrl }}" 
                             alt="{{ $this->displayName }}" 
                             class="w-full h-full object-cover">
                    </div>
                    
                    @if($this->isOwnProfile)
                        <!-- Enhanced Edit Profile Picture Button -->
                        <button 
                            wire:click="toggleEditForm"
                            class="absolute -bottom-2 -right-2 sm:-bottom-3 sm:-right-3 bg-red-600 hover:bg-red-700 text-white p-2 sm:p-2.5 rounded-full shadow-xl transition-all duration-200 hover:scale-105 group-hover:scale-110"
                            title="Edit Profile">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enhanced Profile Header -->
        <div class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
            <div class="px-6 sm:px-8 pt-20 sm:pt-24 pb-6 sm:pb-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                    <!-- Enhanced Profile Info -->
                    <div class="flex-1 space-y-4">
                        <div class="space-y-2">
                            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                                {{ $this->displayName }}
                            </h1>
                            
                            @if($this->about)
                                <p class="text-gray-600 dark:text-gray-300 max-w-2xl text-base sm:text-lg leading-relaxed">
                                    {{ $this->about }}
                                </p>
                            @elseif($this->isOwnProfile)
                                <p class="text-gray-500 dark:text-gray-400 italic text-sm">
                                    Add a bio to tell others about yourself
                                </p>
                            @endif
                        </div>

                        <!-- Enhanced Info Badges -->
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Joined {{ $this->joinedDate }}</span>
                            </div>
                            
                            @if($this->profile?->bdsm_role)
                                <div class="flex items-center gap-2 px-3 py-2 bg-red-100 dark:bg-red-900/20 rounded-full">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-red-700 dark:text-red-300">{{ $this->profile->bdsm_role->label() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="flex items-center gap-3">
                        @if($this->isOwnProfile)
                            <button
                                wire:click="toggleEditForm"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 font-medium text-sm shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Profile
                            </button>
                        @else
                            <button class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium text-sm shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                Follow
                            </button>
                            <button class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 font-medium text-sm shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                Message
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Enhanced Stats Bar -->
            <div class="px-6 sm:px-8 pb-6 sm:pb-8">
                <div class="grid grid-cols-3 gap-4 sm:gap-6 max-w-lg">
                    <div class="text-center group">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-4 sm:p-6 transition-all duration-200 group-hover:shadow-lg group-hover:scale-105">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $this->completedTasksCount }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Tasks Completed</div>
                        </div>
                    </div>
                    
                    <div class="text-center group">
                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-2xl p-4 sm:p-6 transition-all duration-200 group-hover:shadow-lg group-hover:scale-105">
                            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $this->currentStreak }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Day Streak</div>
                        </div>
                    </div>
                    
                    <div class="text-center group">
                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-2xl p-4 sm:p-6 transition-all duration-200 group-hover:shadow-lg group-hover:scale-105">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <div class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1">{{ $this->totalPoints }}</div>
                            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Points</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-10">
            <div class="px-6 sm:px-8">
                <nav class="flex space-x-1 overflow-x-auto scrollbar-hide">
                    <button 
                        @click="activeTab = 'posts'"
                        :class="{
                            'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800': activeTab === 'posts',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border-transparent': activeTab !== 'posts'
                        }"
                        class="flex items-center gap-2 py-3 px-4 rounded-xl border font-medium text-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Posts
                    </button>
                    <button 
                        @click="activeTab = 'about'"
                        :class="{
                            'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800': activeTab === 'about',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border-transparent': activeTab !== 'about'
                        }"
                        class="flex items-center gap-2 py-3 px-4 rounded-xl border font-medium text-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        About
                    </button>
                    <button 
                        @click="activeTab = 'activity'"
                        :class="{
                            'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800': activeTab === 'activity',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border-transparent': activeTab !== 'activity'
                        }"
                        class="flex items-center gap-2 py-3 px-4 rounded-xl border font-medium text-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Activity
                    </button>
                    <button 
                        @click="activeTab = 'media'"
                        :class="{
                            'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800': activeTab === 'media',
                            'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border-transparent': activeTab !== 'media'
                        }"
                        class="flex items-center gap-2 py-3 px-4 rounded-xl border font-medium text-sm transition-all duration-200 whitespace-nowrap cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Media
                    </button>
                </nav>
            </div>
        </div>

        <!-- Enhanced Main Content with Smooth Animations -->
        <div x-show="activeTab === 'posts'" 
             x-transition:enter="transition ease-out duration-500" 
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4">
            <livewire:user.social-profile.posts-tab :user="$this->user" :is-own-profile="$this->isOwnProfile" />
        </div>

        <div x-show="activeTab === 'about'" 
             x-transition:enter="transition ease-out duration-500" 
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4">
            <livewire:user.social-profile.about-tab :user="$this->user" :profile="$this->profile" :is-own-profile="$this->isOwnProfile" lazy />
        </div>

        <div x-show="activeTab === 'activity'" 
             x-transition:enter="transition ease-out duration-500" 
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4">
            <livewire:user.social-profile.activity-tab :user="$this->user" :is-own-profile="$this->isOwnProfile" lazy />
        </div>

        <div x-show="activeTab === 'media'" 
             x-transition:enter="transition ease-out duration-500" 
             x-transition:enter-start="opacity-0 transform translate-y-4" 
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4">
            <livewire:user.social-profile.media-tab :user="$this->user" :profile="$this->profile" :is-own-profile="$this->isOwnProfile" lazy />
        </div>
    </div>

    <!-- Image Modal -->
    <div x-data="{ showImageModal: false, imageUrl: '' }" x-show="showImageModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75" @click="showImageModal = false"></div>

            <!-- Modal Content -->
            <div class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle">
                <div class="relative">
                    <img :src="imageUrl" alt="Full size image" class="w-full h-auto max-h-[80vh] object-contain">
                    <button @click="showImageModal = false" class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Edit Profile Modal -->
    @if($this->showEditForm)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showEditForm') }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Enhanced Backdrop -->
                <div class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="show = false"></div>

                <!-- Enhanced Modal Content -->
                <div class="inline-block w-full max-w-3xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-2xl sm:my-8 sm:align-middle">
                    <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Profile</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Update your profile information and preferences</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-8 py-6">
                        <form wire:submit="save" class="space-y-8">
                            <!-- Username Field -->
                            <div class="space-y-2">
                                <label for="username" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Username
                                </label>
                                <input type="text" 
                                       id="username" 
                                       value="{{ $form->username }}"
                                       wire:model="form.username" 
                                       @class([
                                           'w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-all duration-200',
                                           'border-red-500 focus:ring-red-500' => $errors->has('form.username')
                                       ])
                                       placeholder="janesmith" 
                                       required>
                                @error('form.username')
                                    <p class="text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- About Field -->
                            <div class="space-y-2">
                                <label for="about" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    About
                                </label>
                                <textarea id="about" 
                                          wire:model="form.about" 
                                          rows="4" 
                                          @class([
                                              'w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-all duration-200 resize-none',
                                              'border-red-500 focus:ring-red-500' => $errors->has('form.about')
                                          ])
                                          placeholder="Tell us about yourself..."></textarea>
                                @error('form.about')
                                    <p class="text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- BDSM Role Field -->
                            <div class="space-y-2">
                                <label for="bdsm_role" class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    BDSM Role Preference
                                </label>
                                <select id="bdsm_role" 
                                        wire:model="form.bdsm_role" 
                                        @class([
                                            'w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white transition-all duration-200',
                                            'border-red-500 focus:ring-red-500' => $errors->has('form.bdsm_role')
                                        ])>
                                    <option value="">Select your BDSM role preference (optional)</option>
                                    @foreach($this->getBdsmRoleOptions() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('form.bdsm_role')
                                    <p class="text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Enhanced Profile Picture Field -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Profile Picture
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex items-center justify-center relative ring-4 ring-gray-100 dark:ring-gray-700">
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
                                    <div class="flex-1 space-y-3">
                                        <div class="flex gap-3">
                                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <input type="file" 
                                                       wire:model="form.profile_picture" 
                                                       accept="image/*" 
                                                       class="sr-only">
                                                Change Photo
                                            </label>
                                            @if($form->profile_picture || auth()->user()->profile?->getFirstMedia('profile_pictures'))
                                                <button type="button" 
                                                        wire:click="{{ $form->profile_picture ? '$set(\'form.profile_picture\', null)' : 'removeProfilePicture' }}"
                                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/30 transition-all duration-200 text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG or GIF. Max size 10MB.</p>
                                    </div>
                                </div>
                                @error('form.profile_picture')
                                    <p class="text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Enhanced Cover Photo Field -->
                            <div class="space-y-4">
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white">
                                    Cover Photo
                                </label>
                                
                                @if($form->cover_photo)
                                    <div class="relative group">
                                        <div class="h-40 w-full rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-600 ring-2 ring-gray-100 dark:ring-gray-700">
                                            @if($this->getCoverPhotoPreviewUrl())
                                                <img src="{{ $this->getCoverPhotoPreviewUrl() }}" 
                                                     alt="Cover photo preview" 
                                                     class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <div class="absolute top-3 right-3 flex space-x-2 z-10">
                                            <label class="inline-flex items-center gap-2 bg-white/95 hover:bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer shadow-lg transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <input type="file" 
                                                       wire:model="form.cover_photo" 
                                                       accept="image/*" 
                                                       class="sr-only">
                                                Change
                                            </label>
                                            <button type="button" 
                                                    wire:click="$set('form.cover_photo', null)"
                                                    class="inline-flex items-center gap-2 bg-red-600/95 hover:bg-red-700 text-white px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 cursor-pointer shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @elseif(auth()->user()->profile?->getFirstMedia('cover_photos'))
                                    <div class="relative group">
                                        <div class="h-40 w-full rounded-xl overflow-hidden bg-gray-200 dark:bg-gray-600 relative ring-2 ring-gray-100 dark:ring-gray-700">
                                            <img src="{{ $this->getStoredCoverPhotoUrl() }}" 
                                                 alt="Cover photo" 
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div class="absolute top-3 right-3 flex space-x-2 z-10">
                                            <label class="inline-flex items-center gap-2 bg-white/95 hover:bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 cursor-pointer shadow-lg transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <input type="file" 
                                                       wire:model="form.cover_photo" 
                                                       accept="image/*" 
                                                       class="sr-only">
                                                Change
                                            </label>
                                            <button type="button" 
                                                    wire:click="removeCoverPhoto"
                                                    class="inline-flex items-center gap-2 bg-red-600/95 hover:bg-red-700 text-white px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 cursor-pointer shadow-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex justify-center px-6 pt-8 pb-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-gray-400 dark:hover:border-gray-500 transition-all duration-200 bg-gray-50 dark:bg-gray-700/50">
                                        <div class="space-y-3 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-lg font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500 px-4 py-2 transition-all duration-200">
                                                    <span>Upload a cover photo</span>
                                                    <input type="file" 
                                                           wire:model="form.cover_photo" 
                                                           accept="image/*" 
                                                           class="sr-only">
                                                </label>
                                                <p class="pl-2">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG, GIF up to 10MB
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                
                                @error('form.cover_photo')
                                    <p class="text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </form>
                    </div>
                    
                    <!-- Enhanced Modal Footer -->
                    <div class="px-8 py-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-end space-x-4">
                        <button 
                            type="button" 
                            @click="show = false"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 font-medium text-sm shadow-sm hover:shadow-md">
                            Cancel
                        </button>
                        <button 
                            type="button"
                            wire:click="save" 
                            wire:loading.attr="disabled" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-sm shadow-sm hover:shadow-md">
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Changes
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
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
    
<style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
</style>

<script>
    document.addEventListener('open-image-modal', function(event) {
        const modal = document.querySelector('[x-data*="showImageModal"]');
        if (modal) {
            modal._x_dataStack[0].showImageModal = true;
            modal._x_dataStack[0].imageUrl = event.detail.url;
        }
    });
</script>
    </div>
</div>
