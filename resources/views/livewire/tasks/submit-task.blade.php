<div class="max-w-4xl mx-auto">
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
                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                    <span wire:loading.remove wire:target="submitTask">Submit for Review</span>
                    <span wire:loading wire:target="submitTask">Submitting...</span>
                </button>
            </div>
        </form>
    </div>
</div>
