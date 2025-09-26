<div class="p-4 sm:p-6">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">All Media</h3>
            
            @php
                $allMedia = $this->allMedia;
            @endphp
            
            @if($allMedia->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($allMedia as $media)
                        <div class="group relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden aspect-square">
                            <img src="{{ $media['thumb_url'] }}" 
                                 alt="{{ $media['name'] }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200 cursor-pointer"
                                 @click="$dispatch('open-image-modal', { url: '{{ $media['url'] }}' })">
                            
                            <!-- Media Type Badge -->
                            <div class="absolute top-2 left-2">
                                @if($media['type'] === 'profile')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                        Profile
                                    </span>
                                @elseif($media['type'] === 'status')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        Status
                                    </span>
                                @elseif($media['type'] === 'task')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                        Task
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Media Info -->
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <p class="text-white text-xs font-medium truncate">{{ $media['name'] }}</p>
                                <p class="text-white/80 text-xs">{{ $media['created_at']->format('M j, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No media found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $isOwnProfile ? 'You haven\'t uploaded any media yet.' : 'This user hasn\'t uploaded any media yet.' }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>