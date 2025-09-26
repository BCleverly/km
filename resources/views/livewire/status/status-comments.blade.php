<div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
    <div class="space-y-4">
        @if($this->comments->count() > 0)
            <!-- Comments List -->
            <div class="space-y-3">
                @foreach($this->comments as $comment)
                    <livewire:comments.comment-item 
                        :comment="$comment" 
                        :key="'comment-' . $comment->id" 
                    />
                @endforeach
            </div>

            <!-- Pagination -->
            @if($this->comments->hasPages())
                <div class="mt-4">
                    {{ $this->comments->links() }}
                </div>
            @endif
        @else
            <!-- No Comments Message -->
            <div class="text-center py-6">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No comments yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to share your thoughts!</p>
            </div>
        @endif

        <!-- Comment Form -->
        @auth
            <div class="mt-4">
                <livewire:comments.comment-form 
                    :model-path="get_class($status) . ':' . $status->id" 
                    :parent-id="null"
                />
            </div>
        @else
            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Sign in</a> 
                    to leave a comment
                </p>
            </div>
        @endauth
    </div>
</div>