<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Section - Profile Information -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Profile
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                        This information will be displayed publicly so be careful what you share.
                    </p>
                </div>
            </div>

            <!-- Right Section - Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <form wire:submit="save" class="space-y-6">
                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Username
                            </label>
                            <div class="flex rounded-md shadow-sm">
                                <!-- <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                    kink-master.com/
                                </span> -->
                                <input type="text" 
                                       id="username" 
                                       value="{{ $form->username }}"
                                       wire:model="form.username" 
                                       @class([
                                           'flex-1 min-w-0 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                           'border-red-500' => $errors->has('form.username')
                                       ])
                                       placeholder="janesmith" 
                                       required>
                            </div>
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
                                      placeholder="Write a few sentences about yourself."></textarea>
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
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                This helps match you with compatible tasks and content. You can change this anytime.
                            </p>
                        </div>

                        <!-- Profile Picture Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Profile Picture
                            </label>
                            <div class="flex items-center space-x-4"
                                 x-data="{ 
                                     isDragging: false,
                                     init() {
                                         this.isDragging = false;
                                     },
                                     handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                                     handleDragLeave(e) { e.preventDefault(); this.isDragging = false; },
                                     handleDrop(e) { 
                                         e.preventDefault(); 
                                         this.isDragging = false; 
                                         const files = e.dataTransfer.files; 
                                         if (files.length > 0) { 
                                             @this.set('form.profile_picture', files[0]); 
                                         } 
                                     }
                                 }"
                                 @dragover="handleDragOver"
                                 @dragleave="handleDragLeave"
                                 @drop="handleDrop">
                                <div class="flex-shrink-0">
                                    <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-600 flex items-center justify-center relative transition-all duration-200"
                                         :class="{ 'ring-2 ring-red-500 ring-offset-2': isDragging }">
                                        @if($form->profile_picture)
                                        @if($this->getProfilePicturePreviewUrl())
                                            <img src="{{ $this->getProfilePicturePreviewUrl() }}" 
                                                 alt="Profile picture preview" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full">
                                                <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        @elseif(auth()->user()->profile?->getFirstMedia('profile_pictures'))
                                            <div class="relative h-full w-full">
                                                <img src="{{ $this->getStoredProfilePictureUrl() }}" 
                                                     alt="{{ auth()->user()->display_name }}" 
                                                     class="h-full w-full object-cover">
                                                
                                        @if(!$this->isProfilePictureConversionReady())
                                            <!-- Loading overlay -->
                                            <div class="absolute inset-0 bg-gray-900/50 flex items-center justify-center rounded-full"
                                                 x-data="{
                                                     checkConversion() {
                                                         setTimeout(() => {
                                                             @this.call('checkConversions');
                                                         }, 3000);
                                                     }
                                                 }"
                                                 x-init="checkConversion()">
                                                <div class="text-center">
                                                    <svg class="animate-spin h-6 w-6 text-white mx-auto mb-1" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <p class="text-xs text-white">Processing...</p>
                                                </div>
                                            </div>
                                        @endif
                                            </div>
                                        @else
                                            <img src="{{ auth()->user()->gravatar_url }}" 
                                                 alt="{{ auth()->user()->display_name }}" 
                                                 class="h-full w-full object-cover">
                                        @endif
                                        
                                        <!-- Drag overlay -->
                                        <div x-show="isDragging" 
                                             x-cloak
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="transition ease-in duration-150"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="absolute inset-0 bg-red-500/20 flex items-center justify-center rounded-full">
                                            <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        </div>
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
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF up to 10MB. Drag and drop onto the image to upload.
                            </p>
                        </div>

                        <!-- Cover Photo Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Cover Photo
                            </label>
                            
                            @if($form->cover_photo)
                                <!-- Show preview of newly uploaded cover photo -->
                                <div class="relative group">
                                    <div class="h-32 w-full rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600">
                                        @if($this->getCoverPhotoPreviewUrl())
                                            <img src="{{ $this->getCoverPhotoPreviewUrl() }}" 
                                                 alt="Cover photo preview" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                                <div class="text-center">
                                                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Preview not available</p>
                                                </div>
                                            </div>
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
                                <!-- Show existing cover photo -->
                                <div class="relative group">
                                    <div class="h-32 w-full rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 relative">
                                        <img src="{{ $this->getStoredCoverPhotoUrl() }}" 
                                             alt="Cover photo" 
                                             class="h-full w-full object-cover">
                                        
                                        @if(!$this->isCoverPhotoConversionReady())
                                            <!-- Loading overlay -->
                                            <div class="absolute inset-0 bg-gray-900/50 flex items-center justify-center"
                                                 x-data="{
                                                     checkConversion() {
                                                         setTimeout(() => {
                                                             @this.call('checkConversions');
                                                         }, 3000);
                                                     }
                                                 }"
                                                 x-init="checkConversion()">
                                                <div class="text-center">
                                                    <svg class="animate-spin h-8 w-8 text-white mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <p class="text-sm text-white font-medium">Processing image...</p>
                                                </div>
                                            </div>
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
                                                wire:click="removeCoverPhoto"
                                                class="bg-red-600/95 hover:bg-red-700 text-white px-3 py-1 text-xs font-medium rounded-md transition-colors cursor-pointer shadow-lg">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Upload area -->
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
                                     x-data="{ 
                                         isDragging: false,
                                         init() {
                                             this.isDragging = false;
                                         },
                                         handleDragOver(e) { e.preventDefault(); this.isDragging = true; },
                                         handleDragLeave(e) { e.preventDefault(); this.isDragging = false; },
                                         handleDrop(e) { 
                                             e.preventDefault(); 
                                             this.isDragging = false; 
                                             const files = e.dataTransfer.files; 
                                             if (files.length > 0) { 
                                                 @this.set('form.cover_photo', files[0]); 
                                             } 
                                         }
                                     }"
                                     @dragover="handleDragOver"
                                     @dragleave="handleDragLeave"
                                     @drop="handleDrop"
                                     :class="{ 'border-red-500 bg-red-50 dark:bg-red-900/20': isDragging }">
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

                        <!-- Success Message -->
                        @if (session()->has('message'))
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                            {{ session('message') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="save">Save</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
