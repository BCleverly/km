<div x-data="{
    showUpload: false,
    uploading: false,
    progress: 0,
    canUpload: @js($canUploadImage),
    showModalOrComplete() {
       if (this.canUpload) {
        this.showUpload = true;
       } else {
        $wire.completeTask();
       }
    },
    completeTask() {
        this.showUpload = false;
        $wire.completeTask();
    }
}">
    @if($this->activeTask)
        <h2>{{ $this->activeTask->task->title }}</h2>

        <p>Complete by: {{ $this->activeTask->task->calculateDeadline() }}</p>

        <h3>Reward</h3>
        <p>{{ $this->activeTask->potentialReward->title }}</p>
        <p>{{ $this->activeTask->potentialReward->description }}</p>


        <h3>Punishment</h3>
        <p>{{ $this->activeTask->potentialPunishment->title }}</p>
        <p>{{ $this->activeTask->potentialPunishment->description }}</p>
    @else

    @endif

        @if($this->activeTask === null)
            <button wire:click="assignRandomTask">Surprise me</button>
            <a href="{{ route('app.tasks.create') }}" wire:navigate>I'll decide</a>
        @else
            <button @click="showModalOrComplete">I done it!</button>
            <button wire:click="failTask">I was naughty!</button>
        @endif

    @if($canUploadImage)
        <div x-cloak x-show="showUpload">
            <input type="file"
                   id="completionImage"
                   wire:model="completionImage"
                   accept="image/*"
                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900 dark:file:text-purple-300">
            @if ($completionImage)
                <img src="{{ $completionImage->temporaryUrl() }}" alt="">
            @endif
            <button @click="completeTask">Complete!</button>
        </div>
    @endif



</div>
