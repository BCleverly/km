<div class="p-4 sm:p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
            
            @php
                $recentActivities = $this->recentActivities;
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
                                                 @click="$dispatch('open-image-modal', { url: '{{ $completionImage->getUrl('large') }}' })">
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
                        {{ $isOwnProfile ? 'You haven\'t completed any tasks yet.' : 'This user hasn\'t completed any tasks yet.' }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>