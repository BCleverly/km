<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Comments System Demo
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            This demonstrates the comment system attached to a Story model. The system supports:
        </p>
        <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 mt-2 space-y-1">
            <li>Nested replies (up to 3 levels deep)</li>
            <li>Markdown formatting with live preview</li>
            <li>Reactions with emoji support</li>
            <li>Edit and delete permissions</li>
            <li>Real-time updates with Livewire</li>
        </ul>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            Story: {{ $story->title }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ Str::limit($story->content, 200) }}
        </p>

        <!-- Comments System -->
        <livewire:comments.comments-list :commentable="$story" />
    </div>

    <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-3">
            How to Use This Comment System
        </h3>
        <div class="text-blue-800 dark:text-blue-200 space-y-2">
            <p><strong>1. Add to any model:</strong> Use the <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">Commentable</code> trait</p>
            <p><strong>2. Include in views:</strong> Add <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">&lt;livewire:comments.comments-list :commentable="$model" /&gt;</code></p>
            <p><strong>3. Features:</strong> Markdown support, reactions, nested replies, permissions</p>
        </div>
    </div>
</div>