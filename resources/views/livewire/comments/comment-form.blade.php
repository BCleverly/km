<div class="space-y-4">
    @if($this->isReply)
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Replying to a comment...
        </div>
    @endif

    <!-- Formatting Toolbar -->
    <div class="flex items-center space-x-2 p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <button type="button" 
                wire:click="insertBold"
                class="p-1 text-sm font-bold hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Bold">
            B
        </button>
        <button type="button" 
                wire:click="insertItalic"
                class="p-1 text-sm italic hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Italic">
            I
        </button>
        <button type="button" 
                wire:click="insertQuote"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Quote">
            ❝
        </button>
        <button type="button" 
                wire:click="insertUnorderedList"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Unordered List">
            • List
        </button>
        <button type="button" 
                wire:click="insertOrderedList"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Ordered List">
            1. List
        </button>
        <button type="button" 
                wire:click="insertLink"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Link">
            🔗
        </button>
        <button type="button" 
                wire:click="insertCodeBlock"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Code Block">
            { }
        </button>
        <button type="button" 
                wire:click="insertInlineCode"
                class="p-1 text-sm hover:bg-gray-200 dark:hover:bg-gray-600 rounded"
                title="Inline Code">
            `code`
        </button>
    </div>

    <!-- Comment Input -->
    <div class="space-y-3">
        <textarea wire:model="content" 
                  placeholder="{{ $this->placeholder }}"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  rows="4"></textarea>
        
        @error('content')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Preview Toggle and Markdown Info -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            @if($content)
                <button type="button" 
                        wire:click="togglePreview"
                        class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ $showPreview ? 'Hide Preview' : 'Show Preview' }}
                </button>
            @endif
        </div>
        
        <div class="text-sm text-gray-500 dark:text-gray-400">
            You can use 
            <a href="https://www.markdownguide.org/basic-syntax/" 
               target="_blank" 
               class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                Markdown
            </a>
        </div>
    </div>

    <!-- Preview -->
    @if($showPreview && $previewContent)
        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Preview:</h4>
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! \Illuminate\Support\Str::markdown($previewContent) !!}
            </div>
        </div>
    @endif

    <!-- Submit Button -->
    <div class="flex justify-end">
        @if($this->isReply)
            <div class="flex space-x-2">
                <button type="button" 
                        wire:click="$dispatch('cancel-reply')"
                        class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    Cancel
                </button>
                <button type="button" 
                        wire:click="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    {{ $this->submitButtonText }}
                </button>
            </div>
        @else
            <button type="button" 
                    wire:click="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                {{ $this->submitButtonText }}
            </button>
        @endif
    </div>
</div>