<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Section - Profile Information -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        Profile
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                        This information will be displayed publicly so be careful what you share.
                    </p>
                </div>
            </div>

            <!-- Right Section - Profile Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <form wire:submit="save" class="space-y-6">
                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Username
                            </label>
                            <div class="flex rounded-md shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-sm">
                                    kink-master.com/
                                </span>
                                <input type="text" 
                                       id="username" 
                                       wire:model="form.username" 
                                       class="flex-1 min-w-0 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-none rounded-r-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white @error('form.username') border-red-500 @enderror" 
                                       placeholder="janesmith" 
                                       required>
                            </div>
                            @error('form.username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- About Field -->
                        <div>
                            <label for="about" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                About
                            </label>
                            <textarea id="about" 
                                      wire:model="form.about" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white @error('form.about') border-red-500 @enderror" 
                                      placeholder="Write a few sentences about yourself."></textarea>
                            @error('form.about')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Photo Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Photo
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <button type="button" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors cursor-pointer">
                                    Change
                                </button>
                            </div>
                        </div>

                        <!-- Cover Photo Field -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                                Cover photo
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:border-gray-400 dark:hover:border-gray-500 transition-colors cursor-pointer">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                        <label class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                            <span>Upload a file</span>
                                            <input type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        PNG, JPG, GIF up to 10MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Success Message -->
                        @if (session()->has('message'))
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
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

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="save">Save</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
