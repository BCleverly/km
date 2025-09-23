<!-- Submit Desire Item -->
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Submit Desire Item</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Share your fantasies, fetishes, kinks, or toy ideas with the community. All submissions are reviewed before being added to the exploration pool.
            </p>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="submit" class="space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Title *
                </label>
                <input type="text" 
                       id="title"
                       wire:model="title" 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500 @error('title') border-red-500 @enderror"
                       placeholder="Enter a descriptive title...">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description *
                </label>
                <textarea id="description"
                          wire:model="description" 
                          rows="4"
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500 @error('description') border-red-500 @enderror"
                          placeholder="Describe the fantasy, fetish, kink, or toy in detail..."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type and Category -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="item_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type *
                    </label>
                    <select id="item_type"
                            wire:model="item_type" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500 @error('item_type') border-red-500 @enderror">
                        <option value="">Select a type...</option>
                        @foreach($this->itemTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('item_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category
                    </label>
                    <select id="category_id"
                            wire:model="category_id" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500">
                        <option value="">Select a category...</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Difficulty and Target User Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Difficulty Level (1-10) *
                    </label>
                    <input type="range" 
                           id="difficulty_level"
                           wire:model="difficulty_level" 
                           min="1" 
                           max="10" 
                           class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider @error('difficulty_level') border-red-500 @enderror">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>1 (Beginner)</span>
                        <span class="font-medium">{{ $difficulty_level }}</span>
                        <span>10 (Expert)</span>
                    </div>
                    @error('difficulty_level')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="target_user_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Target User Type *
                    </label>
                    <select id="target_user_type"
                            wire:model="target_user_type" 
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500 @error('target_user_type') border-red-500 @enderror">
                        <option value="any">Any User Type</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="couple">Couple</option>
                    </select>
                    @error('target_user_type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Tags
                </label>
                <div class="flex gap-2 mb-2">
                    <input type="text" 
                           wire:model="newTag" 
                           wire:keydown.enter.prevent="addTag"
                           placeholder="Add a tag..."
                           class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500">
                    <button type="button" 
                            wire:click="addTag"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Add
                    </button>
                </div>
                @if(count($tags) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200">
                                {{ $tag }}
                                <button type="button" 
                                        wire:click="removeTag('{{ $tag }}')"
                                        class="ml-2 text-pink-600 dark:text-pink-400 hover:text-pink-800 dark:hover:text-pink-200">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-pink-600 text-white font-medium rounded-lg hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Submit Desire Item
                </button>
            </div>
        </form>
    </div>
</div>
