<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Subscription Status
        </h3>
        <a href="{{ route('app.subscription') }}" 
           class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            Manage
        </a>
    </div>

    <div class="flex items-center space-x-3 mb-4">
        <div class="flex-shrink-0">
            <div class="w-3 h-3 rounded-full
                @if($currentPlan === \App\Enums\SubscriptionPlan::FREE) bg-gray-400
                @elseif($subscription && $subscription->onTrial()) bg-blue-500
                @else bg-green-500
                @endif">
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $currentPlan->label() }}
                @if($subscription && $subscription->onTrial())
                    (Trial)
                @endif
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $currentPlan->description() }}
            </p>
        </div>
    </div>

    @if($subscription)
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Price:</span>
                <span class="font-medium text-gray-900 dark:text-white">
                    {{ $subscription->formatted_plan_price }}
                </span>
            </div>
            
            @if($subscription->subscription_expiry)
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">
                        @if($subscription->onTrial())
                            Trial Ends:
                        @else
                            Next Billing:
                        @endif
                    </span>
                    <span class="font-medium text-gray-900 dark:text-white">
                        {{ $subscription->subscription_expiry->format('M j, Y') }}
                    </span>
                </div>
            @endif

            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                <span class="font-medium
                    @if($subscription->stripe_status === 'active') text-green-600 dark:text-green-400
                    @elseif(in_array($subscription->stripe_status, ['canceled', 'cancelled'])) text-red-600 dark:text-red-400
                    @else text-yellow-600 dark:text-yellow-400
                    @endif">
                    {{ ucfirst($subscription->stripe_status) }}
                </span>
            </div>
        </div>
    @else
        <div class="text-center py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                Upgrade to unlock premium features
            </p>
            <a href="{{ route('app.subscription') }}" 
               class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                View Plans
            </a>
        </div>
    @endif

    @if($subscription && $subscription->onTrial())
        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        Your trial ends on {{ $subscription->subscription_expiry->format('M j, Y') }}. 
                        <a href="{{ route('app.subscription') }}" class="font-medium underline">Add a payment method</a> to continue.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>