<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="/" class="text-3xl font-bold text-gray-900" wire:navigate>
                Kink Master
            </a>
            <h2 class="mt-6 text-2xl font-bold text-gray-900">
                Forgot your password?
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
            </p>
        </div>

        @if($emailSent)
            <!-- Success State -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Reset link sent!
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>We've sent a password reset link to <strong>{{ $form->email }}</strong></p>
                            <p class="mt-2">Please check your email and click the link to reset your password.</p>
                        </div>
                        <div class="mt-4">
                            <a href="/login" class="text-sm font-medium text-green-800 hover:text-green-600 transition-colors" wire:navigate>
                                ← Back to sign in
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Forgot Password Form -->
            <form class="mt-8 space-y-6" wire:submit="sendResetLink">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        wire:model="form.email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('form.email') border-red-500 @enderror"
                        placeholder="Enter your email"
                        autocomplete="email"
                        required
                    >
                    @error('form.email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform hover:scale-105 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="sendResetLink">
                            Send Reset Link
                        </span>
                        <span wire:loading wire:target="sendResetLink" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>

                <!-- Back to Login -->
                <div class="text-center">
                    <a href="/login" class="text-sm font-medium text-red-600 hover:text-red-500 transition-colors" wire:navigate>
                        ← Back to sign in
                    </a>
                </div>
            </form>
        @endif
    </div>
</div>