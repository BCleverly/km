<div class="min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Send Task to Submissive Partner
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Create a custom task for your submissive partner with rewards and punishments. Make sure not to disappoint them!
            </p>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="save" class="space-y-6">
                <!-- Partner Selection -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Assign to Partner
                    </label>
                    <select id="assigned_to" 
                            wire:model="assigned_to" 
                            @class([
                                'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                'border-red-500' => $errors->has('assigned_to')
                            ])
                            required>
                        <option value="">Select your partner</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}">
                                {{ $partner->display_name }} 
                                @if($partner->bdsm_role_label)
                                    ({{ $partner->bdsm_role_label }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Task Mode Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        Task Type
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   wire:model="task_mode" 
                                   value="custom" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Create Custom Task</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   wire:model="task_mode" 
                                   value="existing" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select Existing Task</span>
                        </label>
                    </div>
                </div>

                @if($task_mode === 'existing')
                    <!-- Existing Task Selection -->
                    <div wire:key="existing-task-selection">
                        <label for="selected_task_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Select Task
                        </label>
                        <select id="selected_task_id" 
                                wire:model="selected_task_id" 
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('selected_task_id')
                                ])
                                required>
                            <option value="">Choose a task...</option>
                            @if(isset($availableTasks) && $availableTasks->count() > 0)
                                @foreach($availableTasks as $task)
                                    <option value="{{ $task->id }}">
                                        {{ $task->title }} (Level {{ $task->difficulty_level }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No tasks available</option>
                            @endif
                        </select>
                        @error('selected_task_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if(isset($availableTasks))
                            <p class="mt-1 text-xs text-gray-500">Available tasks: {{ $availableTasks->count() }}</p>
                        @endif
                    </div>
                @endif

                @if($task_mode === 'custom')
                    <!-- Task Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Task Title
                        </label>
                        <input type="text" 
                               id="title" 
                               wire:model="title" 
                               @class([
                                   'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                   'border-red-500' => $errors->has('title')
                               ])
                               placeholder="Enter task title"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Task Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Task Description
                        </label>
                        <textarea id="description" 
                                  wire:model="description" 
                                  rows="4"
                                  @class([
                                      'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                      'border-red-500' => $errors->has('description')
                                  ])
                                  placeholder="Describe the task in detail..."
                                  required></textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <!-- Display Selected Task Info -->
                    @if($selected_task_id)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                Selected Task: {{ $title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $description }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Difficulty Level: {{ $difficulty_level }}/10
                            </p>
                        </div>
                    @endif
                @endif

                <!-- Dom Message -->
                <div>
                    <label for="dom_message" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Personal Message from You
                    </label>
                    <textarea id="dom_message" 
                              wire:model="dom_message" 
                              rows="3" 
                              @class([
                                  'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                  'border-red-500' => $errors->has('dom_message')
                              ])
                              placeholder="Add a personal message to motivate your partner..."></textarea>
                    @error('dom_message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        This message will be shown to your partner to motivate them.
                    </p>
                </div>

                <!-- Task Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Difficulty Level -->
                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Difficulty Level
                        </label>
                        <select id="difficulty_level" 
                                wire:model="difficulty_level" 
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('difficulty_level')
                                ])
                                required>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                        @error('difficulty_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration_hours" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Duration (Hours)
                        </label>
                        <input type="number" 
                               id="duration_hours" 
                               wire:model="duration_hours" 
                               min="1" 
                               max="168" 
                               @class([
                                   'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                   'border-red-500' => $errors->has('duration_hours')
                               ])
                               required>
                        @error('duration_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Rewards and Punishments -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Reward -->
                    <div>
                        <label for="reward_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Reward (Optional)
                        </label>
                        <select id="reward_id" 
                                wire:model="reward_id" 
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('reward_id')
                                ])>
                            <option value="">No reward</option>
                            @foreach($rewards as $reward)
                                <option value="{{ $reward->id }}">{{ $reward->title }}</option>
                            @endforeach
                        </select>
                        @error('reward_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Punishment -->
                    <div>
                        <label for="punishment_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Punishment (Optional)
                        </label>
                        <select id="punishment_id" 
                                wire:model="punishment_id" 
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('punishment_id')
                                ])>
                            <option value="">No punishment</option>
                            @foreach($punishments as $punishment)
                                <option value="{{ $punishment->id }}">{{ $punishment->title }}</option>
                            @endforeach
                        </select>
                        @error('punishment_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" 
                            wire:loading.attr="disabled" 
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Send Task</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>