<div class="min-h-screen" x-data="{ activeTab: '{{ $activeTab }}' }">
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
                        @click="activeTab = 'posts'; $wire.setActiveTab('posts')"
                        :class="{
                            'border-red-500 text-red-600': activeTab === 'posts',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'posts'
                        }"
                        class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer">
                        Posts
                    </button>
                    <button 
                        @click="activeTab = 'about'; $wire.setActiveTab('about')"
                        :class="{
                            'border-red-500 text-red-600': activeTab === 'about',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'about'
                        }"
                        class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer">
                        About
                    </button>
                    <button 
                        @click="activeTab = 'activity'; $wire.setActiveTab('activity')"
                        :class="{
                            'border-red-500 text-red-600': activeTab === 'activity',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'activity'
                        }"
                        class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer">
                        Activity
                    </button>
                    <button 
                        @click="activeTab = 'media'; $wire.setActiveTab('media')"
                        :class="{
                            'border-red-500 text-red-600': activeTab === 'media',
                            'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'media'
                        }"
                        class="py-3 sm:py-4 px-2 sm:px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap cursor-pointer">
                        Media
                    </button>
                </nav>
            </div>
        </div>

        <!-- Main Content with Lazy Loading -->
        <div x-show="activeTab === 'posts'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <livewire:user.social-profile.posts-tab :user="$this->user" :is-own-profile="$this->isOwnProfile" lazy />
        </div>

        <div x-show="activeTab === 'about'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <livewire:user.social-profile.about-tab :user="$this->user" :profile="$this->profile" :is-own-profile="$this->isOwnProfile" lazy />
        </div>

        <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
            <livewire:user.social-profile.activity-tab :user="$this->user" :is-own-profile="$this->isOwnProfile" lazy />
        </div>

        <div x-show="activeTab === 'media'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
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

<script>
    document.addEventListener('open-image-modal', function(event) {
        const modal = document.querySelector('[x-data*="showImageModal"]');
        if (modal) {
            modal._x_dataStack[0].showImageModal = true;
            modal._x_dataStack[0].imageUrl = event.detail.url;
        }
    });
</script>
