@if($affiliateLinks->count() > 0)
    <div class="affiliate-links {{ $class }}">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            @if($showAll)
                Recommended Products
            @else
                Recommended Product
            @endif
        </h4>
        
        <div class="space-y-2">
            @foreach($affiliateLinks as $link)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $link->name }}
                        </h5>
                        
                        @if($link->pivot->description)
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                {{ $link->pivot->description }}
                            </p>
                        @endif
                        
                        <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500 dark:text-gray-400">
                            <span class="capitalize">{{ $link->partner_type }}</span>
                            @if($link->is_premium)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Premium
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ml-4">
                        <a 
                            href="{{ $link->url }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                        >
                            {{ $link->pivot->link_text ?? 'Shop Now' }}
                            <svg class="ml-1 w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
            <em>We may earn a commission from purchases made through these links.</em>
        </p>
    </div>
@endif