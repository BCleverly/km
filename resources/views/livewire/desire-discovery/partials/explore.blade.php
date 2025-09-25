<!-- Exploration Interface -->
<div class="space-y-6">
    <!-- Progress and Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Exploration Progress</h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                @if($this->partner)
                    Exploring with {{ $this->partner->display_name }}
                @elseif($this->user->hasRole('Admin'))
                    Admin Mode - Exploring all content
                @else
                    Exploring community content
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ count($items) }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Items Remaining</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->responseStats['user_responses'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Your Responses</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->responseStats['user_percentage'] }}%</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completion</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter by Type</label>
                <select wire:model.live="filterType" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    @foreach($this->itemTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="showOnlyUnresponded" class="rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show only unresponded items</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    @if($currentItem)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden" 
             x-data="{
                 startX: 0,
                 startY: 0,
                 currentX: 0,
                 currentY: 0,
                 isDragging: false,
                 
                 handleStart(e) {
                     this.isDragging = true;
                     this.startX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
                     this.startY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
                 },
                 
                 handleMove(e) {
                     if (!this.isDragging) return;
                     
                     this.currentX = (e.type === 'touchmove' ? e.touches[0].clientX : e.clientX) - this.startX;
                     this.currentY = (e.type === 'touchmove' ? e.touches[0].clientY : e.clientY) - this.startY;
                     
                     const card = this.$refs.card;
                     card.style.transform = `translate(${this.currentX}px, ${this.currentY}px) rotate(${this.currentX * 0.1}deg)`;
                     
                     // Visual feedback
                     if (Math.abs(this.currentX) > 50) {
                         card.style.backgroundColor = this.currentX > 0 ? '#10b981' : '#ef4444';
                     } else if (Math.abs(this.currentY) > 50) {
                         card.style.backgroundColor = this.currentY < 0 ? '#f59e0b' : '#6b7280';
                     } else {
                         card.style.backgroundColor = '';
                     }
                 },
                 
                 handleEnd() {
                     if (!this.isDragging) return;
                     this.isDragging = false;
                     
                     const card = this.$refs.card;
                     card.style.transform = '';
                     card.style.backgroundColor = '';
                     
                     if (Math.abs(this.currentX) > 100) {
                         // Swipe left/right
                         if (this.currentX > 0) {
                             @this.call('respond', 3); // Yes
                         } else {
                             @this.call('respond', 1); // No
                         }
                     } else if (Math.abs(this.currentY) > 100) {
                         // Swipe up/down
                         @this.call('respond', 2); // Maybe
                     }
                     
                     this.currentX = 0;
                     this.currentY = 0;
                 }
             }"
             x-ref="card"
             @mousedown="handleStart($event)"
             @mousemove="handleMove($event)"
             @mouseup="handleEnd()"
             @mouseleave="handleEnd()"
             @touchstart="handleStart($event)"
             @touchmove="handleMove($event)"
             @touchend="handleEnd()">
            
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-200 text-sm font-medium rounded-full">
                            {{ $currentItem->item_type->label() }}
                        </span>
                        @if($currentItem->category)
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm rounded-full">
                                {{ $currentItem->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Difficulty: {{ $currentItem->difficulty_level }}/10
                    </div>
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $currentItem->title }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">{{ $currentItem->description }}</p>
                
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>by {{ $currentItem->author->display_name }}</span>
                    <span>{{ $currentItem->created_at->format('M j, Y') }}</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="bg-gray-50 dark:bg-gray-700 px-8 py-6">
                <div class="flex justify-center gap-4">
                    <button wire:click="respond({{ \App\Enums\DesireResponseType::No->value }})"
                            class="flex items-center justify-center w-12 h-12 bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <button wire:click="respond({{ \App\Enums\DesireResponseType::Maybe->value }})"
                            class="flex items-center justify-center w-12 h-12 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    <button wire:click="respond({{ \App\Enums\DesireResponseType::Yes->value }})"
                            class="flex items-center justify-center w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
                <div class="text-center mt-3 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex justify-center gap-8">
                        <span>No</span>
                        <span>Maybe</span>
                        <span>Yes</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">All Caught Up!</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">You've responded to all available items.</p>
            <button wire:click="loadItems" class="inline-flex items-center px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                Refresh Items
            </button>
        </div>
    @endif
</div>
