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
                <div x-data="{ taskMode: 'existing' }">
                    <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                        Task Type
                    </label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" 
                                       x-model="taskMode"
                                       value="existing" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Select Existing Task</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" 
                                       x-model="taskMode"
                                       value="custom" 
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Create Custom Task</span>
                            </label>
                        </div>

                    <!-- Existing Task Selection -->
                    <div x-show="taskMode === 'existing'" wire:key="existing-task-selection">
                        <label for="taskSearch" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Search & Select Task
                        </label>
                        
                        <div class="relative" x-data="{ showDropdown: false }">
                            <input 
                                type="text" 
                                id="taskSearch"
                                wire:model.live="task_search"
                                x-on:focus="showDropdown = true"
                                x-on:blur="setTimeout(() => showDropdown = false, 150)"
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('selected_task_id')
                                ])
                                placeholder="Type to search or click to see all tasks..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showDropdown && !$wire.selected_task_id" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @if(isset($filteredTasks) && $filteredTasks->count() > 0)
                                    @foreach($filteredTasks as $task)
                                        <div wire:click="$set('selected_task_id', {{ $task->id }})"
                                             class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $task->title }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Level {{ $task->difficulty_level }} • {{ Str::limit($task->description, 60) }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        @if(empty($task_search))
                                            No tasks available
                                        @else
                                            No tasks found matching "{{ $task_search }}"
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @error('selected_task_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if(isset($availableTasks))
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Available tasks: {{ $availableTasks->count() }}
                                @if(!empty($task_search))
                                    • Showing {{ $filteredTasks->count() }} matching
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Task Title -->
                    <div x-show="taskMode === 'custom'" x-cloak>
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
                    <div x-show="taskMode === 'custom'" x-cloak>
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

                    <!-- Display Selected Task Info -->
                    <div x-show="taskMode === 'existing' && $wire.selected_task_id" x-cloak class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Selected Task: {{ $title }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-2">{{ $description }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Difficulty Level: {{ $difficulty_level }}/10
                        </p>
                    </div>

                    <!-- Task Settings -->
                    <div x-show="taskMode === 'custom'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                </div>

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


                <!-- Rewards and Punishments -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Reward -->
                    <div>
                        <label for="rewardSearch" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Reward (Optional)
                        </label>
                        
                        <div class="relative" x-data="{ showRewardDropdown: false }">
                            <input 
                                type="text" 
                                id="rewardSearch"
                                wire:model.live="reward_search"
                                x-on:focus="showRewardDropdown = true"
                                x-on:blur="setTimeout(() => showRewardDropdown = false, 150)"
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('reward_id')
                                ])
                                placeholder="Type to search rewards or click to see all..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showRewardDropdown && !$wire.reward_id" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @if(isset($filteredRewards) && $filteredRewards->count() > 0)
                                    <div wire:click="$set('reward_id', null)"
                                         class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-200 dark:border-gray-600">
                                        <div class="font-medium text-gray-900 dark:text-white">No reward</div>
                                    </div>
                                    @foreach($filteredRewards as $reward)
                                        <div wire:click="$set('reward_id', {{ $reward->id }})"
                                             class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $reward->title }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Level {{ $reward->difficulty_level }} • {{ Str::limit($reward->description, 60) }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        @if(empty($reward_search))
                                            No rewards available
                                        @else
                                            No rewards found matching "{{ $reward_search }}"
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @error('reward_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if(isset($availableRewards))
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Available rewards: {{ $availableRewards->count() }}
                                @if(!empty($reward_search))
                                    • Showing {{ $filteredRewards->count() }} matching
                                @endif
                            </p>
                        @endif
                    </div>

                    <!-- Punishment -->
                    <div>
                        <label for="punishmentSearch" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                            Punishment (Optional)
                        </label>
                        
                        <div class="relative" x-data="{ showPunishmentDropdown: false }">
                            <input 
                                type="text" 
                                id="punishmentSearch"
                                wire:model.live="punishment_search"
                                x-on:focus="showPunishmentDropdown = true"
                                x-on:blur="setTimeout(() => showPunishmentDropdown = false, 150)"
                                @class([
                                    'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white',
                                    'border-red-500' => $errors->has('punishment_id')
                                ])
                                placeholder="Type to search punishments or click to see all..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showPunishmentDropdown && !$wire.punishment_id" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @if(isset($filteredPunishments) && $filteredPunishments->count() > 0)
                                    <div wire:click="$set('punishment_id', null)"
                                         class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm border-b border-gray-200 dark:border-gray-600">
                                        <div class="font-medium text-gray-900 dark:text-white">No punishment</div>
                                    </div>
                                    @foreach($filteredPunishments as $punishment)
                                        <div wire:click="$set('punishment_id', {{ $punishment->id }})"
                                             class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $punishment->title }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Level {{ $punishment->difficulty_level }} • {{ Str::limit($punishment->description, 60) }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                        @if(empty($punishment_search))
                                            No punishments available
                                        @else
                                            No punishments found matching "{{ $punishment_search }}"
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @error('punishment_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if(isset($availablePunishments))
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Available punishments: {{ $availablePunishments->count() }}
                                @if(!empty($punishment_search))
                                    • Showing {{ $filteredPunishments->count() }} matching
                                @endif
                            </p>
                        @endif
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