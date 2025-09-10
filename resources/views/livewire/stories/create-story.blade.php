<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('app.stories.index') }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Write Your Story</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Share your story with the community. Be creative and engaging!</p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form wire:submit="save">
            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Story Title
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    placeholder="Enter an engaging title for your story..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('title') border-red-500 @enderror"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Summary -->
            <div class="mb-6">
                <label for="summary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Story Summary
                </label>
                <textarea
                    id="summary"
                    wire:model="summary"
                    rows="3"
                    placeholder="Write a brief summary of your story... (Maximum 500 characters)"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('summary') border-red-500 @enderror"
                ></textarea>
                @error('summary')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ strlen($summary) }}/500 characters
                </div>
            </div>

            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Story Content
                </label>
                <textarea
                    id="content"
                    wire:model="content"
                    rows="15"
                    placeholder="Write your story here... (Minimum 100 words)"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('content') border-red-500 @enderror"
                ></textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                
                <!-- Word Count and Reading Time -->
                <div class="mt-2 flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        Word count: <span class="font-medium">{{ $this->getWordCount() }}</span>
                    </span>
                    <span class="text-gray-500 dark:text-gray-400">
                        Reading time: <span class="font-medium">{{ $this->getReadingTime() }} minutes</span>
                    </span>
                </div>
            </div>


            <!-- Guidelines -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Story Guidelines</h3>
                <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <li>• Write engaging and well-structured stories</li>
                    <li>• Keep content respectful and consensual</li>
                    <li>• Minimum 100 words required for submission</li>
                    <li>• Save as draft to continue writing later</li>
                    <li>• All submitted stories are reviewed before publication</li>
                    <li>• Use proper formatting and paragraphs</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('app.stories.index') }}" 
                   class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="saveAsDraft"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="saveAsDraft"
                    >
                        <span wire:loading.remove wire:target="saveAsDraft">Save as Draft</span>
                        <span wire:loading wire:target="saveAsDraft">Saving...</span>
                    </button>
                    <button
                        type="button"
                        wire:click="submitForReview"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                        wire:target="submitForReview"
                    >
                        <span wire:loading.remove wire:target="submitForReview">Submit for Review</span>
                        <span wire:loading wire:target="submitForReview">Submitting...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>