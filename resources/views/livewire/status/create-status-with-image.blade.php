<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-2 sm:p-3"
     x-data="{ 
        isDragging: false,
        handleDragOver(e) { 
            e.preventDefault(); 
            this.isDragging = true; 
        },
        handleDragLeave(e) { 
            e.preventDefault(); 
            this.isDragging = false; 
        },
        handleDrop(e) { 
            e.preventDefault(); 
            this.isDragging = false; 
            const files = e.dataTransfer.files; 
            if (files.length > 0) { 
                try {
                    @this.set('image', files[0]);
                } catch (error) {
                    console.error('Drag and drop failed:', error);
                    @this.set('image', null);
                }
            } 
        }
     }"
     @dragover="handleDragOver"
     @dragleave="handleDragLeave"
     @drop="handleDrop"
     :class="{ 'border-red-500 bg-red-50 dark:bg-red-900/20': isDragging }">
    <form wire:submit="create" class="space-y-2">
        <div>
            <textarea
                wire:model="content"
                placeholder="What's on your mind? (Optional if uploading an image)"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                rows="3"
                maxlength="{{ $this->maxLength }}"
            ></textarea>
            
            @error('content')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Image Preview (when image is uploaded) -->
        @if($this->showImagePreview && $this->imagePreviewUrl)
            <div class="relative group">
                <div class="relative rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700">
                    <img src="{{ $this->imagePreviewUrl }}" 
                         alt="Status image preview" 
                         class="w-full h-64 object-cover">
                    
                    <!-- Remove Image Button -->
                    <button 
                        type="button"
                        wire:click="removeImage"
                        class="absolute top-2 right-2 p-2 bg-black/50 hover:bg-black/70 text-white rounded-full transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        
        @error('image')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model="isPublic"
                        class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Public</span>
                </label>
            </div>

            <div class="flex items-center justify-between sm:justify-end space-x-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="{{ $this->remainingCharacters < 20 ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $this->characterCount }}
                    </span>
                    / {{ $this->maxLength }}
                </span>
                
                <div class="flex items-center space-x-2">
                    <!-- Image Upload Icon -->
                    <label class="p-2 text-gray-400 hover:text-red-500 transition-colors cursor-pointer" title="Add Photo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <input type="file" 
                               wire:model="image" 
                               accept="image/*" 
                               class="sr-only">
                    </label>
                    
                    <button
                        type="submit"
                        class="px-4 sm:px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                        wire:loading.attr="disabled"
                        wire:target="create"
                    >
                        <span wire:loading.remove wire:target="create">Post</span>
                        <span wire:loading wire:target="create" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Posting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
