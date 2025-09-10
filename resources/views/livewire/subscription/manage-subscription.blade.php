<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-8">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Subscription Management
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Manage your subscription and billing
                    </p>
                </div>

                <div class="mt-8">
                    <!-- Current Status -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Current Status
                        </h2>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Subscription Status</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ ucfirst(str_replace('_', ' ', $subscriptionStatus)) }}
                                </p>
                            </div>
                            @if($isPartOfCouple && $partner)
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Couple Account</p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        Partner: {{ $partner->name }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Subscription Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(!$user->hasActiveSubscription())
                            <!-- No Active Subscription -->
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                            No Active Subscription
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <p>
                                                @if($isPartOfCouple)
                                                    Your couple subscription has expired. Please renew to continue accessing the platform.
                                                @else
                                                    You need an active subscription to access premium features.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button wire:click="subscribe" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Subscribe Now
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Active Subscription -->
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                            Active Subscription
                                        </h3>
                                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                            <p>
                                                @if($isPartOfCouple)
                                                    You have access through your couple subscription.
                                                @else
                                                    You have an active subscription.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button wire:click="manageBilling" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Manage Billing
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Couple Information -->
                        @if($isPartOfCouple && $partner)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Couple Account
                                        </h3>
                                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                            <p>Partner: {{ $partner->name }}</p>
                                            <p>Email: {{ $partner->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Back to Dashboard -->
                    <div class="mt-8 text-center">
                        <a href="{{ route('app.dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>