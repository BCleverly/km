<div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <livewire:manage-widget />
    </div>

   

    {{-- Active Outcomes Section --}}
    @if($activeOutcomes->count() > 0)
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Active Outcomes</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activeOutcomes->count() }} active</p>
                        </div>
                    </div>
                    
                    @if($activeOutcomes->count() > 1)
                        <div class="flex items-center gap-2">
                            <button 
                                onclick="previousOutcome()" 
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                id="prevBtn"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button 
                                onclick="nextOutcome()" 
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                id="nextBtn"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="relative overflow-hidden select-none cursor-grab active:cursor-grabbing" 
                 x-data="outcomeSlider()" 
                 @mousedown="startDrag($event)"
                 @mousemove="drag($event)"
                 @mouseup="endDrag()"
                 @mouseleave="endDrag()"
                 @touchstart="startDrag($event)"
                 @touchmove="drag($event)"
                 @touchend="endDrag()">
                <div class="flex transition-transform duration-300 ease-in-out" 
                     id="outcomesContainer"
                     :style="`transform: translateX(${translateX}px)`"
                     :class="{ 'transition-none': isDragging, 'opacity-90': isDragging }">
                    @foreach($activeOutcomes as $index => $outcome)
                        <div class="w-full flex-shrink-0 p-6" data-outcome-index="{{ $index }}">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center
                                        @if($outcome->outcome->intended_type === 'reward')
                                            bg-green-100 dark:bg-green-900/30
                                        @else
                                            bg-red-100 dark:bg-red-900/30
                                        @endif">
                                        @if($outcome->outcome->intended_type === 'reward')
                                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ ucfirst($outcome->outcome->intended_type) }}
                                        </h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $outcome->assigned_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $outcome->outcome->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ $outcome->outcome->description }}</p>
                                    
                                    @if($outcome->expires_at)
                                        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Expires {{ $outcome->expires_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                    
                                    <button
                                        wire:click="completeOutcome({{ $outcome->id }})"
                                        wire:loading.attr="disabled"
                                        @class([
                                            'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 hover:cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed',
                                            'bg-green-600 hover:bg-green-700 text-white' => $outcome->outcome->intended_type === 'reward',
                                            'bg-red-600 hover:bg-red-700 text-white' => $outcome->outcome->intended_type === 'punishment'
                                        ])
                                    >
                                        <span wire:loading.remove wire:target="completeOutcome({{ $outcome->id }})">Mark as Completed</span>
                                        <span wire:loading wire:target="completeOutcome({{ $outcome->id }})">Completing...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @if($activeOutcomes->count() > 1)
                <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-center gap-2">
                        @foreach($activeOutcomes as $index => $outcome)
                            <button 
                                onclick="goToOutcome({{ $index }})" 
                                class="w-2 h-2 rounded-full transition-colors outcome-dot {{ $index === 0 ? 'bg-purple-500' : 'bg-gray-300 dark:bg-gray-600' }}"
                                data-outcome-dot="{{ $index }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            @endif
            </div>
        </div>
    @endif




    {{-- Recent Activity --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
        </div>
        <div class="p-6">
            @if($recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-start space-x-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                            {{-- Activity Icon --}}
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                    @if($activity->activity_type->color() === 'green') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($activity->activity_type->color() === 'red') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
                                    @elseif($activity->activity_type->color() === 'blue') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
                                    @elseif($activity->activity_type->color() === 'yellow') bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400
                                    @elseif($activity->activity_type->color() === 'orange') bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400
                                    @elseif($activity->activity_type->color() === 'purple') bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400
                                    @else bg-gray-100 text-gray-600 dark:bg-gray-900/30 dark:text-gray-400
                                    @endif">
                                    {{ $activity->activity_type->icon() }}
                                </div>
                            </div>

                            {{-- Activity Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity->title }}
                                    </h4>
                                    <time class="text-xs text-gray-500 dark:text-gray-400" datetime="{{ $activity->activity_at->toISOString() }}">
                                        {{ $activity->activity_at->diffForHumans() }}
                                    </time>
</div>

                                @if($activity->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $activity->description }}
                                    </p>
                                @endif

                                {{-- Task Link --}}
                                <div class="mt-2">
                                    <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $activity->task->title }}
                                    </span>
                                </div>

                                {{-- Completion Image Display --}}
                                @if($activity->userAssignedTask && $activity->userAssignedTask->has_completion_image)
                                    @php
                                        $completionImage = $activity->userAssignedTask->getFirstMedia('completion_images');
                                    @endphp
                                    @if($completionImage)
                                        <div class="mt-3">
                                            <img src="{{ $completionImage->getUrl('medium') }}"
                                                 alt="Task completion image"
                                                 class="w-full max-w-xs rounded-lg shadow-sm border border-gray-200 dark:border-gray-600 cursor-pointer hover:shadow-md transition-shadow"
                                                 onclick="openImageModal('{{ $completionImage->getUrl('large') }}')">
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- View All Activities Link --}}
                <div class="mt-6 text-center">
                    <a href="#" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium cursor-pointer transition-colors duration-200">
                        View all activity →
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h4 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No activity yet</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Complete your first task to see your activity history here.
                    </p>
                </div>
            @endif
        </div>
    </div>


        {{-- Image Modal --}}
        <div id="imageModal" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="{ show: false, imageUrl: '' }" x-show="show" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="show = false"></div>

                {{-- Modal Content --}}
                <div class="inline-block w-full max-w-4xl overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-lg shadow-xl sm:my-8 sm:align-middle">
                    <div class="relative">
                        {{-- Close Button --}}
                        <button @click="show = false"
                                class="absolute top-4 right-4 z-10 p-2 text-white bg-black bg-opacity-50 rounded-full hover:bg-opacity-75 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        {{-- Image --}}
                        <img :src="imageUrl"
                             alt="Task completion image"
                             class="w-full h-auto max-h-[80vh] object-contain">
                    </div>
                </div>
            </div>
        </div>

        <script>
        function openImageModal(imageUrl) {
            const modal = document.getElementById('imageModal');
            const alpineData = Alpine.$data(modal);
            alpineData.imageUrl = imageUrl;
            alpineData.show = true;
        }

        // Alpine.js component for drag/swipe functionality
        function outcomeSlider() {
            return {
                currentIndex: 0,
                totalItems: {{ $activeOutcomes->count() ?? 0 }},
                translateX: 0,
                startX: 0,
                currentX: 0,
                isDragging: false,
                dragThreshold: 80, // Minimum drag distance to trigger slide
                containerWidth: 0,

                init() {
                    this.containerWidth = this.$el.offsetWidth;
                    this.updateDisplay();
                },

                startDrag(event) {
                    if (this.totalItems <= 1) return;
                    
                    this.isDragging = true;
                    this.startX = this.getEventX(event);
                    this.currentX = this.startX;
                    
                    // Prevent default to avoid text selection
                    event.preventDefault();
                },

                drag(event) {
                    if (!this.isDragging || this.totalItems <= 1) return;
                    
                    this.currentX = this.getEventX(event);
                    const deltaX = this.currentX - this.startX;
                    const baseTranslate = -this.currentIndex * this.containerWidth;
                    
                    this.translateX = baseTranslate + deltaX;
                    
                    event.preventDefault();
                },

                endDrag() {
                    if (!this.isDragging || this.totalItems <= 1) return;
                    
                    this.isDragging = false;
                    const deltaX = this.currentX - this.startX;
                    
                    // Determine if we should slide to next/previous
                    if (Math.abs(deltaX) > this.dragThreshold) {
                        if (deltaX > 0 && this.currentIndex > 0) {
                            // Dragged right, go to previous
                            this.currentIndex--;
                        } else if (deltaX < 0 && this.currentIndex < this.totalItems - 1) {
                            // Dragged left, go to next
                            this.currentIndex++;
                        }
                    }
                    
                    this.updateDisplay();
                },

                getEventX(event) {
                    return event.type.includes('touch') ? event.touches[0].clientX : event.clientX;
                },

                updateDisplay() {
                    this.translateX = -this.currentIndex * this.containerWidth;
                    this.updateDots();
                    this.updateButtons();
                },

                updateDots() {
                    const dots = document.querySelectorAll('.outcome-dot');
                    dots.forEach((dot, index) => {
                        if (index === this.currentIndex) {
                            dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                            dot.classList.add('bg-purple-500');
                        } else {
                            dot.classList.remove('bg-purple-500');
                            dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                        }
                    });
                },

                updateButtons() {
                    const prevBtn = document.getElementById('prevBtn');
                    const nextBtn = document.getElementById('nextBtn');
                    
                    if (prevBtn) prevBtn.disabled = this.currentIndex === 0;
                    if (nextBtn) nextBtn.disabled = this.currentIndex === this.totalItems - 1;
                },

                goToSlide(index) {
                    if (index >= 0 && index < this.totalItems) {
                        this.currentIndex = index;
                        this.updateDisplay();
                    }
                }
            }
        }

        // Legacy functions for backward compatibility with button clicks
        function previousOutcome() {
            const slider = document.querySelector('[x-data*="outcomeSlider"]');
            if (slider) {
                const alpineData = Alpine.$data(slider);
                if (alpineData.currentIndex > 0) {
                    alpineData.currentIndex--;
                    alpineData.updateDisplay();
                }
            }
        }

        function nextOutcome() {
            const slider = document.querySelector('[x-data*="outcomeSlider"]');
            if (slider) {
                const alpineData = Alpine.$data(slider);
                if (alpineData.currentIndex < alpineData.totalItems - 1) {
                    alpineData.currentIndex++;
                    alpineData.updateDisplay();
                }
            }
        }

        function goToOutcome(index) {
            const slider = document.querySelector('[x-data*="outcomeSlider"]');
            if (slider) {
                const alpineData = Alpine.$data(slider);
                alpineData.goToSlide(index);
            }
        }
        </script>
</div>
