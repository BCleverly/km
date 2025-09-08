<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Submit a New Outcome</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Create a new reward or punishment for community review. All submissions will be reviewed before being added to the community library.
            </p>
        </div>

        <form wire:submit="submitOutcome" class="space-y-8">
            <!-- Outcome Details -->
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Outcome Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="outcomeTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Outcome Title *
                        </label>
                        <input
                            type="text"
                            id="outcomeTitle"
                            wire:model="outcomeForm.title"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="Enter outcome title"
                        >
                        @error('outcomeForm.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="intendedType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Intended Type *
                        </label>
                        <select
                            id="intendedType"
                            wire:model="outcomeForm.intendedType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="reward">Reward</option>
                            <option value="punishment">Punishment</option>
                        </select>
                        @error('outcomeForm.intendedType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="outcomeDifficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Difficulty Level *
                        </label>
                        <select
                            id="outcomeDifficultyLevel"
                            wire:model="outcomeForm.difficultyLevel"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($this->difficultyLevels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('outcomeForm.difficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="outcomeTargetUserType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Target User Type *
                        </label>
                        <select
                            id="outcomeTargetUserType"
                            wire:model="outcomeForm.targetUserType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            @foreach($this->userTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('outcomeForm.targetUserType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="outcomeDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Outcome Description *
                    </label>
                    <textarea
                        id="outcomeDescription"
                        wire:model="outcomeForm.description"
                        rows="4"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        placeholder="Describe the outcome in detail..."
                    ></textarea>
                    @error('outcomeForm.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                    <span wire:loading.remove wire:target="submitOutcome">Submit for Review</span>
                    <span wire:loading wire:target="submitOutcome">Submitting...</span>
                </button>
            </div>
        </form>
    </div>
</div>
