<!-- Historical Review -->
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->responseStats['user_responses'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Your Responses</div>
                </div>
            </div>
        </div>

        @if($this->partner)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->responseStats['partner_responses'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $this->partner->display_name }}'s Responses</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->responseStats['user_percentage'] }}%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Completion Rate</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->responseStats['total_items'] }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Items</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search by title or description..."
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-pink-500 focus:border-pink-500">
            </div>
            <div class="md:w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter by Type</label>
                <select wire:model.live="filterType" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    @foreach($this->itemTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort by</label>
                <select wire:model.live="sortBy" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="created_at">Date</option>
                    <option value="title">Title</option>
                    <option value="item_type">Type</option>
                </select>
            </div>
            <div class="md:w-32">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order</label>
                <select wire:model.live="sortDirection" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="desc">Newest</option>
                    <option value="asc">Oldest</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Historical Items List -->
    <div class="space-y-4">
        @forelse($this->historicalItems as $item)
            @php
                $userResponse = $item->partnerResponses->where('user_id', $this->user->id)->first();
                $partnerResponse = $this->partner ? $item->partnerResponses->where('user_id', $this->partner->id)->first() : null;
                $isMatch = $userResponse && $partnerResponse && $userResponse->response_type === $partnerResponse->response_type;
            @endphp
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 {{ $isMatch ? 'ring-2 ring-green-200 dark:ring-green-800' : '' }}">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200 text-sm font-medium rounded-full">
                                {{ $item->item_type->label() }}
                            </span>
                            @if($item->category)
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded-full">
                                    {{ $item->category->name }}
                                </span>
                            @endif
                            @if($isMatch)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Perfect Match!
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $item->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ $item->description }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span>by {{ $item->author->display_name }} • {{ $item->created_at->format('M j, Y') }}</span>
                            <span>Difficulty: {{ $item->difficulty_level }}/10</span>
                        </div>
                    </div>
                </div>
                
                <!-- Response Indicators -->
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-xs font-medium text-blue-600 dark:text-blue-400">
                            {{ substr($this->user->display_name, 0, 1) }}
                        </span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $this->user->display_name }}:</span>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            @if($userResponse?->response_type === \App\Enums\DesireResponseType::Yes) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200
                            @elseif($userResponse?->response_type === \App\Enums\DesireResponseType::Maybe) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200
                            @elseif($userResponse?->response_type === \App\Enums\DesireResponseType::No) bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                            {{ $userResponse?->response_type?->label() ?? 'No response' }}
                        </span>
                    </div>
                    
                    @if($this->partner)
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center text-xs font-medium text-purple-600 dark:text-purple-400">
                                {{ substr($this->partner->display_name, 0, 1) }}
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $this->partner->display_name }}:</span>
                            <span class="px-2 py-1 rounded text-xs font-medium
                                @if($partnerResponse?->response_type === \App\Enums\DesireResponseType::Yes) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200
                                @elseif($partnerResponse?->response_type === \App\Enums\DesireResponseType::Maybe) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200
                                @elseif($partnerResponse?->response_type === \App\Enums\DesireResponseType::No) bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 @endif">
                                {{ $partnerResponse?->response_type?->label() ?? 'No response' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Historical Data</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">You haven't responded to any desire items yet.</p>
                <button wire:click="setActiveTab('explore')" 
                        class="inline-flex items-center px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    Start Exploring
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($this->historicalItems->hasPages())
        <div class="mt-8">
            {{ $this->historicalItems->links() }}
        </div>
    @endif
</div>
