<div class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Cover Photo -->
        @if($this->coverPhotoUrl)
            <div class="relative h-64 rounded-xl overflow-hidden mb-6">
                <img src="{{ $this->coverPhotoUrl }}" 
                     alt="{{ $this->displayName }}'s cover photo" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            </div>
        @else
            <div class="h-32 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl mb-6"></div>
        @endif

        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <!-- Profile Picture -->
                <div class="relative">
                    <img src="{{ $this->profilePictureUrl }}" 
                         alt="{{ $this->displayName }}" 
                         class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-800 shadow-lg">
                </div>
                
                <!-- Profile Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ $this->displayName }}
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-2">
                        
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Joined {{ $this->joinedDate }}
                    </p>
                </div>
            </div>
        </div>

        <!-- About Section -->
        @if($this->about)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">About</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $this->about }}
                </p>
            </div>
        @endif

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Tasks Completed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                <div class="text-3xl font-bold text-red-600 dark:text-red-400 mb-2">
                    {{ $this->completedTasksCount }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Tasks Completed
                </div>
            </div>

            <!-- Current Streak -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-2">
                    {{ $this->currentStreak }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Day Streak
                </div>
            </div>

            <!-- Total Points -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                    {{ $this->totalPoints }}
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">
                    Total Points
                </div>
            </div>
        </div>

        <!-- Status Updates -->
        <div class="mb-6">
            <livewire:status.status-list :user="$this->user" :limit="10" />
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
            
            @php
                $recentActivities = $this->recentActivities(5);
            @endphp
            
            @if($recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                @if($activity->activity_type === \App\TaskActivityType::Completed)
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $activity->activity_type->label() }}: {{ $activity->task->title }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $activity->activity_at->diffForHumans() }}
                                </p>
                                
                                {{-- Completion Image Display --}}
                                @if($activity->activity_type === \App\TaskActivityType::Completed && $activity->userAssignedTask && $activity->userAssignedTask->has_completion_image)
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
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No recent activity</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        This user hasn't completed any tasks yet.
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
    </script>
</div>  