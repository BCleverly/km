<div class="max-w-4xl mx-auto">
    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
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

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Submit a New Task</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Create a new task for community review. All submissions will be reviewed before being added to the community library.
            </p>
        </div>

        <form wire:submit="submitTask" class="space-y-8">
            <!-- Task Details -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Task Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Task Title *
                        </label>
                        <input
                            type="text"
                            id="title"
                            wire:model="taskForm.title"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter task title"
                        >
                        @error('taskForm.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="difficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Difficulty Level *
                        </label>
                        <select
                            id="difficultyLevel"
                            wire:model="taskForm.difficultyLevel"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($this->difficultyLevels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('taskForm.difficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Task Description *
                    </label>
                    <textarea
                        id="description"
                        wire:model="taskForm.description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Describe the task in detail..."
                    ></textarea>
                    @error('taskForm.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="durationTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration Time *
                        </label>
                        <input
                            type="number"
                            id="durationTime"
                            wire:model="taskForm.durationTime"
                            min="1"
                            max="999"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                        @error('taskForm.durationTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="durationType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration Type *
                        </label>
                        <select
                            id="durationType"
                            wire:model="taskForm.durationType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="minutes">Minutes</option>
                            <option value="hours">Hours</option>
                            <option value="days">Days</option>
                            <option value="weeks">Weeks</option>
                        </select>
                        @error('taskForm.durationType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="targetUserType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Target User Type *
                        </label>
                        <select
                            id="targetUserType"
                            wire:model="taskForm.targetUserType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($this->userTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('taskForm.targetUserType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Tags Section -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tags (Optional)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Add tags to help categorize your task. These can be updated by moderators during review.
                    </p>
                    
                    @foreach($this->taskForm->getAvailableTags() as $typeKey => $typeData)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ $typeData['name'] }}
                                @if($typeData['required'])
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $typeData['description'] }}</p>
                            
                            @php
                                $selectedTags = $this->taskForm->getSelectedTags()[$typeKey] ?? [];
                            @endphp
                            
                            <!-- Selected Tags Display -->
                            @if(!empty($selectedTags))
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected tags:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedTags as $tag)
                                            <span @class([
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' => $tag['is_pending'],
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => !$tag['is_pending']
                                            ])>
                                                {{ $tag['name'] }}
                                                @if($tag['is_pending'])
                                                    <span class="ml-1 text-xs">(pending)</span>
                                                @endif
                                                <button
                                                    type="button"
                                                    wire:click="removeTag('{{ $typeKey }}', {{ $tag['id'] }})"
                                                    class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600"
                                                >
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            @if(!empty($typeData['tags']))
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mb-3">
                                    @foreach($typeData['tags'] as $tagId => $tagName)
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="taskForm.tags.{{ $typeKey }}"
                                                value="{{ $tagId }}"
                                                class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                            >
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $tagName }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-3">No {{ strtolower($typeData['name']) }} tags available yet.</p>
                            @endif
                            
                            <!-- Create New Tag -->
                            <div class="flex items-center space-x-2" x-data="{ newTagName: '' }">
                                <input
                                    type="text"
                                    x-model="newTagName"
                                    placeholder="Create new {{ strtolower($typeData['name']) }} tag..."
                                    class="flex-1 px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                >
                                <button
                                    type="button"
                                    x-on:click="
                                        if (newTagName.trim()) {
                                            $wire.createTag('{{ $typeKey }}', newTagName.trim());
                                            newTagName = '';
                                        }
                                    "
                                    x-bind:disabled="!newTagName.trim()"
                                    wire:loading.attr="disabled"
                                    wire:target="createTag"
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="createTag">Add</span>
                                    <span wire:loading wire:target="createTag">Adding...</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <button
                    type="button"
                    wire:click="$dispatch('switch-tab', { tab: 'browse' })"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submitTask"
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                    <span wire:loading.remove wire:target="submitTask">Submit for Review</span>
                    <span wire:loading wire:target="submitTask">Submitting...</span>
                </button>
            </div>
        </form>
    </div>
</div>
