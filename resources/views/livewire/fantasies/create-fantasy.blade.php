<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('app.fantasies.index') }}" 
               class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Share Your Fantasy</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Share your fantasy with the community. Keep it respectful and within our guidelines.</p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <form wire:submit="save">
            <!-- Content -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Fantasy Content
                </label>
                <textarea
                    id="content"
                    wire:model="content"
                    rows="8"
                    placeholder="Share your fantasy here... (Maximum 280 words)"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('content') border-red-500 @enderror"
                ></textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                
                <!-- Word Count -->
                <div class="mt-2 flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        Word count: <span class="font-medium">{{ $this->getWordCount() }}</span>
                    </span>
                    <span class="font-medium {{ $this->getRemainingWords() < 20 ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ $this->getRemainingWords() }} words remaining
                    </span>
                </div>
            </div>

            <!-- Tags -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tags (Optional)
                </label>
                <div class="space-y-2">
                    @foreach($this->getAvailableTags() as $tag)
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                wire:model="selectedTags"
                                value="{{ $tag->id }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @if($this->getAvailableTags()->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        No tags available. Tags will be added by administrators.
                    </p>
                @endif
            </div>

            <!-- Anonymous Option -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="is_anonymous"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Submit anonymously (your username won't be shown)
                    </span>
                </label>
            </div>

            <!-- Premium Option - Hidden for now -->
            {{-- <div class="mb-6">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        wire:model="is_premium"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Make this a premium fantasy (requires premium subscription)
                    </span>
                </label>
            </div> --}}

            <!-- Guidelines -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Community Guidelines</h3>
                <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                    <li>• Keep content respectful and consensual</li>
                    <li>• No illegal or harmful content</li>
                    <li>• Maximum 280 words per fantasy</li>
                    <li>• All fantasies are reviewed before publication</li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('app.fantasies.index') }}" 
                   class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Submit Fantasy</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </div>
        </form>
    </div>
</div>