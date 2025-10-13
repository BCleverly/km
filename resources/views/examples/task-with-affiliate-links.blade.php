{{-- Example of how to display a task with affiliate links --}}

<div class="task-card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
        {{ $task->title }}
    </h2>
    
    <p class="text-gray-600 dark:text-gray-300 mb-4">
        {{ $task->description }}
    </p>
    
    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
        <span>Difficulty: {{ $task->difficulty_level }}/10</span>
        <span>Duration: {{ $task->duration_display }}</span>
        <span class="capitalize">{{ $task->target_user_type->value }}</span>
    </div>
    
    {{-- Display affiliate links --}}
    <x-affiliate-links 
        :task="$task" 
        :show-all="true"
        class="mt-6"
    />
    
    {{-- Or just show the primary affiliate link --}}
    {{-- <x-affiliate-links :task="$task" :show-primary="true" class="mt-6" /> --}}
</div>