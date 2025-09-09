<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="/" class="text-3xl font-bold text-gray-900" wire:navigate>
                Kink Master
            </a>
            <h2 class="mt-6 text-2xl font-bold text-gray-900">
                Get started for free
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Already registered?
                <a href="/login" class="font-medium text-red-600 hover:text-red-500 transition-colors" wire:navigate>
                    Sign in to your account
                </a>
            </p>
        </div>

        <!-- Registration Form -->
        <form class="mt-8 space-y-6" wire:submit="register">
            <div class="space-y-4">
                <!-- Name Fields -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First name
                        </label>
                        <input 
                            id="first_name" 
                            type="text" 
                            wire:model="form.first_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('form.first_name') border-red-500 @enderror"
                            placeholder="Enter your first name"
                            autocomplete="given-name"
                            required
                        >
                        @error('form.first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last name
                        </label>
                        <input 
                            id="last_name" 
                            type="text" 
                            wire:model="form.last_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('form.last_name') border-red-500 @enderror"
                            placeholder="Enter your last name"
                            autocomplete="family-name"
                            required
                        >
                        @error('form.last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username Field -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <input 
                        id="username" 
                        type="text" 
                        wire:model="form.username"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('form.username') border-red-500 @enderror"
                        placeholder="Choose a unique username"
                        autocomplete="username"
                        required
                    >
                    @error('form.username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
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

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        wire:model="form.password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors @error('form.password') border-red-500 @enderror"
                        placeholder="Create a password"
                        autocomplete="new-password"
                        required
                    >
                    @error('form.password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform hover:scale-105 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="register">
                        Sign up →
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating account...
                    </span>
                </button>
            </div>

            <!-- Divider -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 text-gray-500">Or continue with</span>
                </div>
            </div>

            <!-- Social Login Options (Disabled) -->
            <div class="grid grid-cols-3 gap-3">
                <!-- Passkeys Authentication -->
                <x-authenticate-passkey>
                    <button 
                        type="button"
                        disabled
                        class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed opacity-50"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <span class="ml-2">Passkey</span>
                    </button>
                </x-authenticate-passkey>

                <!-- Google Login -->
                <button 
                    type="button"
                    disabled
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed opacity-50"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="ml-2">Google</span>
                </button>

                <!-- Facebook Login -->
                <button 
                    type="button"
                    disabled
                    class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed opacity-50"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="ml-2">Facebook</span>
                </button>
            </div>
        </form>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                By signing up, you agree to our
                <a href="/terms" class="text-red-600 hover:text-red-500 transition-colors" wire:navigate>Terms of Service</a>
                and
                <a href="/privacy" class="text-red-600 hover:text-red-500 transition-colors" wire:navigate>Privacy Policy</a>
            </p>
        </div>
    </div>
</div>