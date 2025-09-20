<div>
    @if(auth()->user()->canSendPartnerInvitations())
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Invite Your Partner
            </h2>
            <p class="text-gray-600 dark:text-gray-300 text-sm mb-6">
                Send an invitation to your partner to join you on Kink Master. They'll be able to share tasks and activities with you.
            </p>

            <!-- Invitation Success Message -->
            @if (session()->has('invitation_message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('invitation_message') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Invitation Error Messages -->
            @if ($errors->has('email'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ $errors->first('email') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentInvitation)
                <!-- Pending Invitation State -->
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/20 mb-4">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-2">
                        Invitation Pending
                    </h3>
                    <div class="mt-1">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            You have sent an invitation to <strong>{{ $currentInvitation->email }}</strong>
                        </p>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                            <span x-data="countdownTimer('{{ $currentInvitation->expires_at->toISOString() }}')" x-text="timeRemaining"></span>
                        </p>
                        @if($currentInvitation->message)
                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1 italic">
                                "{{ $currentInvitation->message }}"
                            </p>
                        @endif
                    </div>
                    
                    <!-- Revoke Button -->
                    <div class="mt-4">
                        <button
                            wire:click="revokeInvitation"
                            wire:confirm="Are you sure you want to revoke this invitation? This action cannot be undone."
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="revokeInvitation"
                        >
                            <span wire:loading.remove wire:target="revokeInvitation">Revoke Invitation</span>
                            <span wire:loading wire:target="revokeInvitation">Revoking...</span>
                        </button>
                    </div>
                </div>
            @elseif ($showForm)
                <!-- Invitation Form -->
                <div class="space-y-4">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Partner's Email Address
                        </label>
                        <input type="email" 
                               id="email" 
                               wire:model="email"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                               placeholder="partner@example.com"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message Field -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Personal Message (Optional)
                        </label>
                        <textarea id="message" 
                                  wire:model="message"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('message') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                  placeholder="Add a personal message to your invitation..."></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="button"
                            wire:click="sendInvitation"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                            wire:loading.attr="disabled"
                            wire:target="sendInvitation"
                        >
                            <span wire:loading.remove wire:target="sendInvitation">Send Invitation</span>
                            <span wire:loading wire:target="sendInvitation">Sending...</span>
                        </button>
                    </div>
                </div>
            @elseif ($lastInvitation && !$currentInvitation)
                <!-- Success State (just sent) -->
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/20 mb-4">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-green-900 dark:text-green-100 mb-2">
                        Invitation Sent!
                    </h3>
                    <p class="text-sm text-green-700 dark:text-green-300 mb-4">
                        Your invitation has been sent to <strong>{{ $lastInvitation->email }}</strong>
                    </p>
                    <button wire:click="sendAnother" 
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                        Send another invitation
                    </button>
                </div>
            @endif

            <!-- Recent Invitations -->
            @if($this->getSentInvitations()->count() > 0)
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Recent Invitations</h3>
                    <div class="space-y-3">
                        @foreach($this->getSentInvitations() as $invitation)
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <span class="text-gray-900 dark:text-white">{{ $invitation->email }}</span>
                                    <span class="text-gray-500 dark:text-gray-400 ml-2">
                                        {{ $invitation->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($invitation->accepted_at)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Accepted
                                        </span>
                                    @elseif($invitation->expires_at < now())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            Expired
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Pending
                                        </span>
                                        <button
                                            wire:click="revokeInvitationById({{ $invitation->id }})"
                                            wire:confirm="Are you sure you want to revoke this invitation?"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium"
                                            wire:loading.attr="disabled"
                                            wire:target="revokeInvitationById"
                                        >
                                            <span wire:loading.remove wire:target="revokeInvitationById">Revoke</span>
                                            <span wire:loading wire:target="revokeInvitationById">...</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
function countdownTimer(expiresAt) {
    return {
        timeRemaining: '',
        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },
        updateTime() {
            const now = new Date().getTime();
            const expiry = new Date(expiresAt).getTime();
            const distance = expiry - now;

            if (distance < 0) {
                this.timeRemaining = 'Expired';
                return;
            }

            const hours = Math.floor(distance / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            if (hours > 0) {
                this.timeRemaining = `${hours}h ${minutes}m remaining`;
            } else {
                this.timeRemaining = `${minutes}m remaining`;
            }
        }
    }
}
</script>
