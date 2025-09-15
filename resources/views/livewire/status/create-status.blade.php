<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-start space-x-3">
        <img 
            src="{{ auth()->user()->profile_picture_url }}" 
            alt="{{ auth()->user()->display_name }}"
            class="w-10 h-10 rounded-full object-cover"
        >
        <div class="flex-1">
            <form wire:submit="create" class="space-y-4">
                <div>
                    <textarea
                        wire:model="content"
                        placeholder="What's on your mind?"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                        rows="3"
                        maxlength="{{ $this->maxLength }}"
                    ></textarea>
                    
                    @error('content')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="isPublic"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Public</span>
                        </label>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="{{ $this->remainingCharacters < 20 ? 'text-red-500' : 'text-gray-500' }}">
                                {{ $this->characterCount }}
                            </span>
                            / {{ $this->maxLength }}
                        </span>
                        
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="create"
                        >
                            <span wire:loading.remove wire:target="create">Post</span>
                            <span wire:loading wire:target="create">Posting...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>