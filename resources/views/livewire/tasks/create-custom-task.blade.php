<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create Custom Task</h1>
        <p class="text-gray-600 dark:text-gray-400">
            Choose existing tasks and outcomes, or create your own. You'll be assigned the task immediately to start working on it.
        </p>
    </div>

    @if(isset($successMessage) && $successMessage)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ $successMessage }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-8" x-data="{ taskSelection: 'existing', rewardSelection: 'existing', punishmentSelection: 'existing' }">
        <!-- Task Selection Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Task Selection</h2>
            
            <div class="space-y-4">
                <!-- Task Selection Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Choose Task Source *
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="taskSelection"
                                value="existing" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Use existing task from community</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="taskSelection"
                                value="create" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Create new custom task</span>
                        </label>
                    </div>
                    @error('form.taskSelection') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Existing Task Selection -->
                <div x-show="taskSelection === 'existing'">
                    <div>
                        <label for="taskAutocomplete" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search & Select Task *
                        </label>
                        
                        <div class="relative" x-data="{ showDropdown: false }">
                            <input 
                                type="text" 
                                id="taskAutocomplete"
                                wire:model.live="form.taskSearch"
                                x-on:focus="showDropdown = true"
                                x-on:blur="setTimeout(() => showDropdown = false, 150)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Type to search or click to see all tasks..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showDropdown && !$wire.form.selectedTaskId" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @foreach($this->getAllAvailableTasks() as $id => $title)
                                    <div 
                                        wire:click="selectTask({{ $id }}, '{{ addslashes($title) }}')"
                                        class="px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 cursor-pointer text-gray-900 dark:text-white"
                                    >
                                        {{ $title }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($form->selectedTaskId)
                            <div class="mt-2 p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-sm text-green-700 dark:text-green-300">
                                        Selected: {{ collect($this->getAllAvailableTasks())->get($form->selectedTaskId) }}
                                    </span>
                                    <button 
                                        type="button"
                                        wire:click="$set('form.selectedTaskId', null)"
                                        class="ml-auto text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @error('form.selectedTaskId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Custom Task Creation -->
                <div x-show="taskSelection === 'create'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Task Title *
                            </label>
                            <input 
                                type="text" 
                                id="title"
                                wire:model="form.title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Enter a descriptive title for your task"
                            >
                            @error('form.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Task Description *
                            </label>
                            <textarea 
                                id="description"
                                wire:model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Provide detailed instructions for completing this task"
                            ></textarea>
                            @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="difficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Difficulty Level *
                            </label>
                            <select 
                                id="difficultyLevel"
                                wire:model="form.difficultyLevel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach($this->getDifficultyLevelOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.difficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Target User Type -->
                        <div>
                            <label for="targetUserType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Target User Type *
                            </label>
                            <select 
                                id="targetUserType"
                                wire:model="form.targetUserType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach($this->getTargetUserTypeOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.targetUserType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Duration Time -->
                        <div>
                            <label for="durationTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Duration Time *
                            </label>
                            <input 
                                type="number" 
                                id="durationTime"
                                wire:model="form.durationTime"
                                min="1"
                                max="999"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                            @error('form.durationTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Duration Type -->
                        <div>
                            <label for="durationType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Duration Type *
                            </label>
                            <select 
                                id="durationType"
                                wire:model="form.durationType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach($this->getDurationTypeOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.durationType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Premium Task -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="form.isPremium"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Premium Task (requires premium subscription to access)
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reward Selection Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Reward Selection</h2>
            
            <div class="space-y-4">
                <!-- Reward Selection Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Choose Reward Source *
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="rewardSelection"
                                value="existing" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Use existing outcome from community</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="rewardSelection"
                                value="create" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Create new custom reward</span>
                        </label>
                    </div>
                    @error('form.rewardSelection') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Existing Reward Selection -->
                <div x-show="rewardSelection === 'existing'">
                    <div>
                        <label for="rewardAutocomplete" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search & Select Outcome (will be used as reward)
                        </label>
                        
                        <div class="relative" x-data="{ showDropdown: false }">
                            <input 
                                type="text" 
                                id="rewardAutocomplete"
                                wire:model.live="form.rewardSearch"
                                x-on:focus="showDropdown = true"
                                x-on:blur="setTimeout(() => showDropdown = false, 150)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Type to search or click to see all outcomes..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showDropdown && !$wire.form.selectedRewardId" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @foreach($this->getAvailableRewards() as $id => $reward)
                                    <div 
                                        wire:click="selectReward({{ $id }}, '{{ addslashes($reward['title']) }}')"
                                        class="px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 cursor-pointer text-gray-900 dark:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">{{ $reward['title'] }}</div>
                                            <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                                @if($reward['intended_type'] === 'reward') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 @endif">
                                                {{ ucfirst($reward['intended_type']) }}
                                            </span>
                                        </div>
                                        @if($reward['description'])
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($reward['description'], 80) }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($form->selectedRewardId)
                            @php
                                $selectedReward = collect($this->getAllAvailableRewards())->get($form->selectedRewardId);
                            @endphp
                            <div class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                <div class="flex items-start">
                                    <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-medium text-green-700 dark:text-green-300">
                                                Selected: {{ $selectedReward['title'] }}
                                            </div>
                                            <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                                @if($selectedReward['intended_type'] === 'reward') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 @endif">
                                                {{ ucfirst($selectedReward['intended_type']) }}
                                            </span>
                                        </div>
                                        @if($selectedReward['description'])
                                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                {{ $selectedReward['description'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="$set('form.selectedRewardId', null); $set('form.rewardSearch', '')"
                                        class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @error('form.selectedRewardId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Custom Reward Creation -->
                <div x-show="rewardSelection === 'create'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Reward Title -->
                        <div class="md:col-span-2">
                            <label for="rewardTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reward Title *
                            </label>
                            <input 
                                type="text" 
                                id="rewardTitle"
                                wire:model="form.rewardTitle"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Enter a descriptive title for the reward"
                            >
                            @error('form.rewardTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Reward Description -->
                        <div class="md:col-span-2">
                            <label for="rewardDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reward Description *
                            </label>
                            <textarea 
                                id="rewardDescription"
                                wire:model="form.rewardDescription"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Describe what the reward entails"
                            ></textarea>
                            @error('form.rewardDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Reward Difficulty Level -->
                        <div>
                            <label for="rewardDifficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reward Difficulty Level *
                            </label>
                            <select 
                                id="rewardDifficultyLevel"
                                wire:model="form.rewardDifficultyLevel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach($this->getDifficultyLevelOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.rewardDifficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Punishment Selection Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Punishment Selection</h2>
            
            <div class="space-y-4">
                <!-- Punishment Selection Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Choose Punishment Source *
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="punishmentSelection"
                                value="existing" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Use existing outcome from community</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                x-model="punishmentSelection"
                                value="create" 
                                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Create new custom punishment</span>
                        </label>
                    </div>
                    @error('form.punishmentSelection') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Existing Punishment Selection -->
                <div x-show="punishmentSelection === 'existing'">
                    <div>
                        <label for="punishmentAutocomplete" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search & Select Outcome (will be used as punishment)
                        </label>
                        
                        <div class="relative" x-data="{ showDropdown: false }">
                            <input 
                                type="text" 
                                id="punishmentAutocomplete"
                                wire:model.live="form.punishmentSearch"
                                x-on:focus="showDropdown = true"
                                x-on:blur="setTimeout(() => showDropdown = false, 150)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Type to search or click to see all outcomes..."
                                autocomplete="off"
                            >
                            
                            <div x-show="showDropdown && !$wire.form.selectedPunishmentId" 
                                 x-transition
                                 class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm max-h-48 overflow-y-auto">
                                @foreach($this->getAvailablePunishments() as $id => $punishment)
                                    <div 
                                        wire:click="selectPunishment({{ $id }}, '{{ addslashes($punishment['title']) }}')"
                                        class="px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 cursor-pointer text-gray-900 dark:text-white"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium">{{ $punishment['title'] }}</div>
                                            <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                                @if($punishment['intended_type'] === 'reward') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 @endif">
                                                {{ ucfirst($punishment['intended_type']) }}
                                            </span>
                                        </div>
                                        @if($punishment['description'])
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($punishment['description'], 80) }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($form->selectedPunishmentId)
                            @php
                                $selectedPunishment = collect($this->getAllAvailablePunishments())->get($form->selectedPunishmentId);
                            @endphp
                            <div class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                                <div class="flex items-start">
                                    <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-medium text-green-700 dark:text-green-300">
                                                Selected: {{ $selectedPunishment['title'] }}
                                            </div>
                                            <span class="ml-2 px-2 py-1 text-xs rounded-full 
                                                @if($selectedPunishment['intended_type'] === 'reward') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 @endif">
                                                {{ ucfirst($selectedPunishment['intended_type']) }}
                                            </span>
                                        </div>
                                        @if($selectedPunishment['description'])
                                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                {{ $selectedPunishment['description'] }}
                                            </div>
                                        @endif
                                    </div>
                                    <button 
                                        type="button"
                                        wire:click="$set('form.selectedPunishmentId', null); $set('form.punishmentSearch', '')"
                                        class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        
                        @error('form.selectedPunishmentId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Custom Punishment Creation -->
                <div x-show="punishmentSelection === 'create'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Punishment Title -->
                        <div class="md:col-span-2">
                            <label for="punishmentTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Punishment Title *
                            </label>
                            <input 
                                type="text" 
                                id="punishmentTitle"
                                wire:model="form.punishmentTitle"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Enter a descriptive title for the punishment"
                            >
                            @error('form.punishmentTitle') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Punishment Description -->
                        <div class="md:col-span-2">
                            <label for="punishmentDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Punishment Description *
                            </label>
                            <textarea 
                                id="punishmentDescription"
                                wire:model="form.punishmentDescription"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Describe what the punishment entails"
                            ></textarea>
                            @error('form.punishmentDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Punishment Difficulty Level -->
                        <div>
                            <label for="punishmentDifficultyLevel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Punishment Difficulty Level *
                            </label>
                            <select 
                                id="punishmentDifficultyLevel"
                                wire:model="form.punishmentDifficultyLevel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            >
                                @foreach($this->getDifficultyLevelOptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('form.punishmentDifficultyLevel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Privacy Options Section -->
        <div x-show="taskSelection === 'create' || rewardSelection === 'create' || punishmentSelection === 'create'" 
             x-transition
             class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Privacy & Review Options</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            wire:model="form.keepPrivate"
                            class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            Keep this task private to me only
                        </span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        If unchecked, your custom task and outcomes will be submitted for community review and may be made available to other users.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <button 
                type="button" 
                wire:click="resetForm"
                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600"
            >
                Reset Form
            </button>
            
            <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove wire:target="submit">Create & Assign Task</span>
                <span wire:loading wire:target="submit">Creating...</span>
            </button>
        </div>
    </form>
</div>