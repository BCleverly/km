<div class="space-y-6">
    @if(auth()->check())
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Status Updates</h2>
            
            @if($this->canCreateStatus)
                <button
                    wire:click="toggleCreateForm"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                    <span wire:loading.remove wire:target="toggleCreateForm">
                        {{ $showCreateForm ? 'Cancel' : 'New Status' }}
                    </span>
                    <span wire:loading wire:target="toggleCreateForm">Loading...</span>
                </button>
            @else
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Daily limit reached ({{ $this->dailyStatusCount }}/{{ $this->maxStatusesPerDay }})
                </div>
            @endif
        </div>

        @if($showCreateForm)
            <livewire:status.create-status />
        @endif
    @else
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Status Updates</h2>
    @endif

    @if($this->statuses->count() > 0)
        <div class="space-y-4">
            @foreach($this->statuses as $status)
                <livewire:status.status-item :status="$status" :key="$status->id" />
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-gray-500 dark:text-gray-400">
                @if($user)
                    {{ $user->display_name }} hasn't posted any status updates yet.
                @else
                    No status updates to show.
                @endif
            </div>
        </div>
    @endif
</div>