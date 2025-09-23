<div class="max-w-6xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Desire Discovery</h1>
        <p class="text-gray-600 dark:text-gray-400">
            @if($this->partner)
                Explore fantasies, fetishes, and kinks with {{ $this->partner->display_name }}.
            @elseif($this->user->hasRole('Admin'))
                Admin Mode - Explore and manage all desire content.
            @else
                Discover new fantasies, fetishes, and kinks with your partner.
            @endif
        </p>
    </div>

    @if(!$this->hasPartner)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
            <div class="text-yellow-600 dark:text-yellow-400 text-lg font-semibold mb-2">
                Partner Required
            </div>
            <p class="text-yellow-700 dark:text-yellow-300 mb-4">
                You need a linked partner to access Desire Discovery features.
            </p>
            <a href="{{ route('app.partner.invite') }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                Invite Partner
            </a>
        </div>
    @else
        <!-- Tab Navigation -->
        <div class="mb-8">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button wire:click="setActiveTab('explore')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'explore' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            Explore
                        </div>
                    </button>
                    <button wire:click="setActiveTab('compatibility')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'compatibility' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Compatibility
                        </div>
                    </button>
                    <button wire:click="setActiveTab('history')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'history' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            History
                        </div>
                    </button>
                    <button wire:click="setActiveTab('submit')"
                            class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'submit' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Submit
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            @if($activeTab === 'explore')
                @include('livewire.desire-discovery.partials.explore')
            @elseif($activeTab === 'compatibility')
                @include('livewire.desire-discovery.partials.compatibility')
            @elseif($activeTab === 'history')
                @include('livewire.desire-discovery.partials.history')
            @elseif($activeTab === 'submit')
                @include('livewire.desire-discovery.partials.submit')
            @endif
        </div>
    @endif
</div>