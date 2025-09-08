<div class="max-w-4xl mx-auto p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Subscription Management
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Manage your subscription and billing preferences.
        </p>
    </div>

    <!-- Current Subscription Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                Current Plan
            </h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($currentPlan->value === 'free') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                @elseif($subscription && $subscription->onTrial()) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                @endif">
                {{ $currentPlan->label() }}
                @if($subscription && $subscription->onTrial())
                    (Trial)
                @endif
            </span>
        </div>

        @if($subscription)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ ucfirst($subscription->stripe_status) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $subscription->formatted_plan_price }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($subscription->onTrial())
                            Trial Ends
                        @else
                            Next Billing
                        @endif
                    </p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $subscription->subscription_expiry?->format('M j, Y') ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <!-- Subscription Actions -->
            <div class="flex flex-wrap gap-3">
                @if($subscription->stripe_status === 'active' && $currentPlan !== \App\Enums\SubscriptionPlan::LIFETIME)
                    <button 
                        wire:click="cancelSubscription(false)"
                        wire:confirm="Are you sure you want to cancel your subscription? You'll retain access until the end of your billing period."
                        class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800"
                        wire:loading.attr="disabled"
                        wire:target="cancelSubscription">
                        Cancel Subscription
                    </button>
                @endif

                @if($subscription->stripe_status === 'canceled' && $subscription->ends_at && $subscription->ends_at->isFuture())
                    <button 
                        wire:click="resumeSubscription"
                        class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-900 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-800"
                        wire:loading.attr="disabled"
                        wire:target="resumeSubscription">
                        Resume Subscription
                    </button>
                @endif
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">
                You're currently on the free plan. Upgrade to unlock premium features!
            </p>
        @endif
    </div>

    <!-- Available Plans -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
            Available Plans
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($this->availablePlans as $plan)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6
                    @if($plan === \App\Enums\SubscriptionPlan::LIFETIME) ring-2 ring-yellow-500 @endif">
                    
                    @if($plan === \App\Enums\SubscriptionPlan::LIFETIME)
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium">
                                Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $plan->label() }}
                        </h3>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            {{ $plan->formattedPrice() }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $plan->description() }}
                        </p>
                    </div>

                    <ul class="space-y-3 mb-6">
                        @foreach($plan->features() as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($this->canUpgradeTo($plan))
                        <button 
                            wire:click="showUpgradeModal({{ $plan->value }})"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                @if($plan === \App\Enums\SubscriptionPlan::LIFETIME) bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 @endif"
                            wire:loading.attr="disabled">
                            @if($subscription)
                                Change Plan
                            @else
                                Subscribe
                            @endif
                        </button>
                    @else
                        <button 
                            disabled
                            class="w-full px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed dark:bg-gray-700 dark:text-gray-500 dark:border-gray-600">
                            @if($currentPlan === $plan)
                                Current Plan
                            @else
                                Not Available
                            @endif
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Upgrade Modal -->
    @if($showUpgradeModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="hideUpgradeModal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $subscription ? 'Change Plan' : 'Subscribe' }} to {{ $selectedPlan?->label() }}
                    </h3>
                    
                    <div class="mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                {{ $selectedPlan?->formattedPrice() }}
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $selectedPlan?->description() }}
                            </p>
                        </div>
                    </div>

                    @if($selectedPlan === \App\Enums\SubscriptionPlan::LIFETIME)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Payment Method
                            </label>
                            <select 
                                wire:model="paymentMethodId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Payment Method</option>
                                <!-- Payment methods would be loaded here -->
                            </select>
                            @error('paymentMethodId') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <button 
                            wire:click="hideUpgradeModal"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button 
                            wire:click="createSubscription"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                            wire:target="createSubscription">
                            <span wire:loading.remove wire:target="createSubscription">
                                {{ $subscription ? 'Change Plan' : 'Subscribe' }}
                            </span>
                            <span wire:loading wire:target="createSubscription">
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md dark:bg-green-900 dark:text-green-300 dark:border-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md dark:bg-red-900 dark:text-red-300 dark:border-red-700">
            {{ session('error') }}
        </div>
    @endif
</div>