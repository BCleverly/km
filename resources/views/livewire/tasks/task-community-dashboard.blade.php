<div x-data="{ activeTab: 'browse' }">
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
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="activeTab = 'browse'"
                        :class="{
                            'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'browse',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'browse'
                        }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Browse Tasks
                    </button>
                    <button
                        @click="activeTab = 'submit-task'"
                        :class="{
                            'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'submit-task',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'submit-task'
                        }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Submit Task
                    </button>
                    <button
                        @click="activeTab = 'submit-outcome'"
                        :class="{
                            'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'submit-outcome',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'submit-outcome'
                        }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors"
                    >
                        Submit Outcome
                    </button>
                </nav>
            </div>
        </div>

        <!-- Browse Tab -->
        <div x-show="activeTab === 'browse'" x-transition>
            @include('livewire.tasks.partials.browse-tasks')
        </div>

        <!-- Submit Task Tab -->
        <div x-show="activeTab === 'submit-task'" x-transition>
            @include('livewire.tasks.partials.submit-task')
        </div>

        <!-- Submit Outcome Tab -->
        <div x-show="activeTab === 'submit-outcome'" x-transition>
            @include('livewire.tasks.partials.submit-outcome')
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>