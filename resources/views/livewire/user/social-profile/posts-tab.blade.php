<div class="max-w-4xl mx-auto p-2 sm:p-3">
    <!-- Main Feed -->
    <div class="space-y-2">
        <!-- Status Creation -->
        @if($isOwnProfile)
            <livewire:status.create-status-with-image />
        @endif

        <!-- Status Feed -->
        <div class="space-y-2">
            <livewire:status.status-list-with-images :user="$user" :limit="10" />
        </div>
    </div>
</div>