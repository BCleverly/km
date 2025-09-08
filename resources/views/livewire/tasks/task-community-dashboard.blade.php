<div x-data="{ activeTab: 'browse' }" 
     x-on:switch-tab.window="activeTab = $event.detail.tab">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Community Task Library</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Browse and discover tasks created by the community, or submit your own for review.
            </p>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="grid grid-cols-1 sm:hidden">
                <!-- Mobile dropdown -->
                <select 
                    x-model="activeTab"
                    aria-label="Select a tab" 
                    class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 dark:bg-white/5 dark:text-gray-100 dark:outline-white/10 dark:*:bg-gray-800 dark:focus:outline-indigo-500"
                >
                    <option value="browse">Browse Tasks</option>
                    <option value="submit-task">Submit Task</option>
                    <option value="submit-outcome">Submit Outcome</option>
                </select>
                <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end fill-gray-500 dark:fill-gray-400">
                    <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                </svg>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200 dark:border-white/10">
                    <nav aria-label="Tabs" class="-mb-px flex">
                        <button
                            @click="activeTab = 'browse'"
                            :class="{
                                'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400': activeTab === 'browse',
                                'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-white/20 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'browse'
                            }"
                            class="w-1/3 border-b-2 px-1 py-4 text-center text-sm font-medium transition-colors cursor-pointer"
                        >
                            Browse Tasks
                        </button>
                        <button
                            @click="activeTab = 'submit-task'"
                            :class="{
                                'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400': activeTab === 'submit-task',
                                'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-white/20 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'submit-task'
                            }"
                            class="w-1/3 border-b-2 px-1 py-4 text-center text-sm font-medium transition-colors cursor-pointer"
                        >
                            Submit Task
                        </button>
                        <button
                            @click="activeTab = 'submit-outcome'"
                            :class="{
                                'border-indigo-500 dark:border-indigo-400 text-indigo-600 dark:text-indigo-400': activeTab === 'submit-outcome',
                                'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-white/20 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'submit-outcome'
                            }"
                            class="w-1/3 border-b-2 px-1 py-4 text-center text-sm font-medium transition-colors cursor-pointer"
                        >
                            Submit Outcome
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- Browse Tab -->
            <div x-show="activeTab === 'browse'" x-transition>
                <livewire:tasks.browse-tasks />
            </div>

            <!-- Submit Task Tab -->
            <div x-show="activeTab === 'submit-task'" x-transition>
                <livewire:tasks.submit-task />
            </div>

            <!-- Submit Outcome Tab -->
            <div x-show="activeTab === 'submit-outcome'" x-transition>
                <livewire:tasks.submit-outcome />
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>