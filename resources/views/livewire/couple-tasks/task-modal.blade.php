<!-- Task Completion/Thank You Modal -->
@if($selectedTask)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('selectedTask', null)">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $selectedTask->canBeCompleted() ? 'Complete Task' : 'Thank Your Partner' }}
                </h3>

                @if($selectedTask->canBeCompleted())
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $selectedTask->title }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $selectedTask->description }}</p>
                        
                        @if($selectedTask->dom_message)
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-4">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    <strong>Message from {{ $selectedTask->assignedBy->display_name }}:</strong><br>
                                    {{ $selectedTask->dom_message }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="completionNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Completion Notes (Optional)
                        </label>
                        <textarea id="completionNotes" 
                                  wire:model="completionNotes" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="How did it go? Any notes for your partner..."></textarea>
                    </div>

                    <div class="flex justify-between">
                        <button wire:click="failTask" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Mark as Failed
                        </button>
                        <div class="space-x-2">
                            <button wire:click="declineTask" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Decline
                            </button>
                            <button wire:click="completeTask" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Complete
                            </button>
                        </div>
                    </div>
                @elseif($selectedTask->canBeThanked())
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $selectedTask->title }}</h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            Thank {{ $selectedTask->assignedBy->display_name }} for this task.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label for="thankYouMessage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Thank You Message
                        </label>
                        <textarea id="thankYouMessage" 
                                  wire:model="thankYouMessage" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Thank your partner for the task..."
                                  required></textarea>
                        @error('thankYouMessage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button wire:click="$set('selectedTask', null)" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Cancel
                        </button>
                        <button wire:click="sendThankYou" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Send Thank You
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
