<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 {{ $comment->isReply() ? 'ml-8' : '' }}">
    <!-- Comment Header -->
    <div class="flex items-center space-x-3 mb-3">
        <div class="flex-shrink-0">
            @if($comment->user->profile_photo_path)
                <img class="h-8 w-8 rounded-full" src="{{ $comment->user->profile_photo_url }}" alt="{{ $comment->user->name }}">
            @else
                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ substr($comment->user->name, 0, 1) }}
                    </span>
                </div>
            @endif
        </div>
        
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $comment->user->name }}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        @if($this->canEdit || $this->canDelete)
            <div class="flex items-center space-x-2">
                @if($this->canEdit)
                    <button wire:click="startEdit" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        Edit
                    </button>
                @endif
                @if($this->canDelete)
                    <button wire:click="deleteComment" 
                            wire:confirm="Are you sure you want to delete this comment?"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        Delete
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Comment Content -->
    <div class="mb-4">
        @if($isEditing)
            <div class="space-y-3">
                <textarea wire:model="editContent" 
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                          rows="3"></textarea>
                <div class="flex justify-end space-x-2">
                    <button wire:click="cancelEdit" 
                            class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button wire:click="saveEdit" 
                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </div>
        @else
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! \Illuminate\Support\Str::markdown($comment->content) !!}
            </div>
        @endif
    </div>

    <!-- Reactions -->
    <div class="mb-4">
        <livewire:components.reaction-button 
            model-type="comment" 
            :model-id="$comment->id" 
            :key="'reactions-' . $comment->id" />
    </div>

    <!-- Reply Button -->
    @if($this->canReply)
        <div class="mb-4">
            <button wire:click="$dispatch('start-reply', { parentId: {{ $comment->id }} })" 
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                Reply
            </button>
        </div>
    @endif

    <!-- Replies -->
    @if($this->replies->count() > 0)
        <div class="space-y-3">
            @foreach($this->replies as $reply)
                <div wire:key="reply-{{ $reply->id }}">
                    <livewire:comments.comment-item :comment="$reply" :key="'reply-item-' . $reply->id" />
                </div>
            @endforeach
        </div>
    @endif

    <!-- Reply Form (when replying to this comment) -->
    @if($replyingTo === $comment->id)
        <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <livewire:comments.comment-form 
                :commentable="$comment->commentable" 
                :parent-id="$comment->id" 
                :key="'reply-form-' . $comment->id" />
        </div>
    @endif
</div>