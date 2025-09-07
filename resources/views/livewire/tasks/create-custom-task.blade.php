<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create Custom Task</h1>
        <p class="text-gray-600 dark:text-gray-400">
            Design your own task with custom rewards and punishments. Your task will be reviewed before being made available to other users.
        </p>
    </div>

    @if(isset($successMessage) && $successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ $successMessage }}
                    </p>
                </div>
            </div>
        </div>
    @endif


    <form wire:submit="submit" class="space-y-8">
        <!-- Task Information Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Task Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Task Title *
                    </label>
                    <input 
                        type="text" 
                        id="title"
                        wire:model="form.title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter a descriptive title for your task"
                    >
                    @error('form.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Task Description *
                    </label>
                    <textarea 
                        id="description"
                        wire:model="form.description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Provide detailed instructions for completing this task"
                    ></textarea>
                    @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label for="difficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Difficulty Level *
                    </label>
                    <select 
                        id="difficultyLevel"
                        wire:model="form.difficultyLevel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        @foreach([1 => '1 - Very Easy', 2 => '2 - Easy', 3 => '3 - Medium', 4 => '4 - Hard', 5 => '5 - Very Hard'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.difficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Target User Type -->
                <div>
                    <label for="targetUserType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Target User Type *
                    </label>
                    <select 
                        id="targetUserType"
                        wire:model="form.targetUserType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        @foreach([1 => 'Male', 2 => 'Female', 3 => 'Couple', 4 => 'Anyone'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('form.targetUserType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Duration -->
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label for="durationTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration Time *
                        </label>
                        <input 
                            type="number" 
                            id="durationTime"
                            wire:model="form.durationTime"
                            min="1"
                            max="999"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('form.durationTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex-1">
                        <label for="durationType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration Type *
                        </label>
                        <select 
                            id="durationType"
                            wire:model="form.durationType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            @foreach(['minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days', 'weeks' => 'Weeks'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.durationType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Premium -->
                <div class="md:col-span-2">
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="isPremium"
                            wire:model="form.isPremium"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        >
                        <label for="isPremium" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Premium Task (requires premium subscription to access)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4">
            <button 
                type="button" 
                wire:click="resetForm"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
            >
                Reset Form
            </button>
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit">Create Task</span>
                <span wire:loading wire:target="submit">Creating...</span>
            </button>
        </div>
    </form>
</div>