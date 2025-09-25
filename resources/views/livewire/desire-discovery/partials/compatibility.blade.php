<!-- Compatibility View -->
<div class="space-y-6">
    <!-- Compatibility Overview -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Compatibility Report</h2>
            <p class="text-gray-600 dark:text-gray-400">
                See how you and {{ $this->partner?->display_name ?? 'your partner' }} match on different desires and interests.
            </p>
        </div>

        @if($this->compatibilityStats['total_items'] > 0)
            <!-- Main Compatibility Score -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full mb-4">
                    <div class="text-center text-white">
                        <div class="text-3xl font-bold">{{ $this->compatibilityStats['compatibility_percentage'] }}%</div>
                        <div class="text-sm opacity-90">Compatible</div>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Great Match!</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    You both responded to {{ $this->compatibilityStats['total_items'] }} items with {{ $this->compatibilityStats['matches'] }} perfect matches.
                </p>
            </div>

            <!-- Detailed Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->compatibilityStats['yes_matches'] }}</div>
                    <div class="text-sm text-green-700 dark:text-green-300">Yes Matches</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->compatibilityStats['maybe_matches'] }}</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300">Maybe Matches</div>
                </div>
                <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $this->compatibilityStats['no_matches'] }}</div>
                    <div class="text-sm text-red-700 dark:text-red-300">No Matches</div>
                </div>
                <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->compatibilityStats['total_items'] }}</div>
                    <div class="text-sm text-blue-700 dark:text-blue-300">Total Items</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center gap-4">
                <button wire:click="setActiveTab('explore')" 
                        class="inline-flex items-center px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    Continue Exploring
                </button>
                <button wire:click="setActiveTab('history')" 
                        class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    View History
                </button>
            </div>
        @else
            <!-- No Data State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Compatibility Data Yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Start exploring desire items together to see your compatibility!
                </p>
                <button wire:click="setActiveTab('explore')" 
                        class="inline-flex items-center px-6 py-3 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    Start Exploring
                </button>
            </div>
        @endif
    </div>

    @if($this->compatibilityStats['total_items'] > 0)
        <!-- Compatibility Items List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Compatibility Details</h3>
            
            <div class="space-y-4">
                @forelse($this->compatibilityItems as $item)
                    @php
                        $userResponse = $item->partnerResponses->where('user_id', $this->user->id)->first();
                        $partnerResponse = $this->partner ? $item->partnerResponses->where('user_id', $this->partner->id)->first() : null;
                        $isMatch = $userResponse && $partnerResponse && $userResponse->response_type === $partnerResponse->response_type;
                    @endphp
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ $isMatch ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $item->title }}</h4>
                                    @if($isMatch)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Perfect Match!
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ Str::limit($item->description, 100) }}</p>
                                
                                <div class="flex items-center gap-4 text-sm">
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
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">No compatibility data available.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
