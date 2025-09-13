<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(auth()->user()->hasLifetimeSubscription())
            <!-- Lifetime Member Message -->
            <div class="text-center py-16">
                <div class="max-w-2xl mx-auto">
                    <div class="mb-8">
                        <svg class="mx-auto h-24 w-24 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">
                        🎉 Lifetime Member
                    </h1>
                    
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Congratulations! You're a lifetime member of Kink Master. 
                        You have unlimited access to all features forever.
                    </p>
                    
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl border border-purple-200 p-8 mb-8">
                        <h2 class="text-2xl font-semibold text-purple-900 mb-4">
                            What This Means For You
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Unlimited Access</h3>
                                    <p class="text-sm text-gray-600">All premium features and content forever</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">No More Billing</h3>
                                    <p class="text-sm text-gray-600">No subscription fees or payment concerns</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Future Updates</h3>
                                    <p class="text-sm text-gray-600">Access to all new features and improvements</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-green-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h3 class="font-medium text-gray-900">Priority Support</h3>
                                    <p class="text-sm text-gray-600">Enhanced customer support for lifetime members</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <a href="{{ route('app.dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Regular Billing Interface -->
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Billing & Subscription
                </h1>
                <p class="text-gray-600">
                    Manage your subscription and billing information
                </p>
            </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Current Plan -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Current Plan
                </h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $currentPlan->label() }}
                        </h3>
                        <p class="text-gray-600">
                            {{ $currentPlan->description() }}
                        </p>
                    </div>
                    
                    @if($subscription)
                        <div class="border-t pt-4">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-900">Status:</span>
                                    <span class="ml-2 text-gray-600 capitalize">
                                        {{ $subscription->stripe_status }}
                                    </span>
                                </div>
                                @if($subscription->trial_ends_at)
                                    <div>
                                        <span class="font-medium text-gray-900">Trial ends:</span>
                                        <span class="ml-2 text-gray-600">
                                            {{ $subscription->trial_ends_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                @endif
                                @if($subscription->ends_at)
                                    <div>
                                        <span class="font-medium text-gray-900">Ends:</span>
                                        <span class="ml-2 text-gray-600">
                                            {{ $subscription->ends_at->format('M j, Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Plan Features -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Plan Features
                </h2>
                <ul class="space-y-2">
                    @foreach($currentPlan->features() as $feature)
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                Manage Subscription
            </h2>
            <div class="flex flex-wrap gap-4">
                @if($subscription && $subscription->active())
                    <button 
                        wire:click="openBillingPortal"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                    >
                        Manage Billing
                    </button>
                    
                    @if($subscription->cancelled())
                        <button 
                            wire:click="resumeSubscription"
                            wire:confirm="Are you sure you want to resume your subscription?"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                        >
                            Resume Subscription
                        </button>
                    @else
                        <button 
                            wire:click="cancelSubscription"
                            wire:confirm="Are you sure you want to cancel your subscription? You will continue to have access until the end of your current billing period."
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
                        >
                            Cancel Subscription
                        </button>
                    @endif
                @else
                    <a href="{{ route('app.subscription.choose') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                        Subscribe Now
                    </a>
                @endif
            </div>
        </div>

        <!-- Trial Information -->
        @if($user->isOnTrial())
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Free Trial Active
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Your trial ends on {{ $user->trial_ends_at->format('F j, Y') }}. Choose a subscription plan to continue enjoying unlimited access.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endif
    </div>
</div>