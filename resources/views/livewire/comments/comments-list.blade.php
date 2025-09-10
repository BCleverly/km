<div class="space-y-6">
    @if($showForm)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <livewire:comments.comment-form :model-path="$modelPath" />
        </div>
    @endif

    @if($this->comments->count() > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Comments ({{ $this->comments->total() }})
            </h3>
            
            @foreach($this->comments as $comment)
                <div wire:key="comment-{{ $comment->id }}">
                    <livewire:comments.comment-item :comment="$comment" :key="'comment-item-' . $comment->id" />
                    
                    <!-- Reply Form (when replying to this comment) -->
                    @if($replyingTo === $comment->id)
                        <div class="mt-4 ml-8 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <livewire:comments.comment-form 
                                :model-path="$modelPath" 
                                :parent-id="$comment->id" 
                                :key="'reply-form-' . $comment->id" />
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($this->comments->hasPages())
            <div class="mt-6">
                {{ $this->comments->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <div class="text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-lg font-medium">No comments yet</p>
                <p class="text-sm">Be the first to share your thoughts!</p>
            </div>
        </div>
    @endif
</div>