<div class="min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                @if($this->currentPlanStatus['is_on_trial'])
                    Choose Your Plan
                @elseif($this->currentPlanStatus['has_paid_subscription'])
                    Manage Your Subscription
                @else
                    Choose Your Plan
                @endif
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                @if($this->currentPlanStatus['is_on_trial'])
                    Unlock the full potential of Kink Master with our premium subscription plans. 
                    Start your journey with a 14-day free trial.
                @elseif($this->currentPlanStatus['has_paid_subscription'])
                    You're currently on the <strong>{{ $this->currentPlan->label() }}</strong> plan. 
                    Upgrade or downgrade to a different plan below.
                @else
                    Unlock the full potential of Kink Master with our premium subscription plans.
                @endif
            </p>
        </div>

        <!-- Status Notice -->
        @if($this->currentPlanStatus['is_on_trial'])
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
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
                            <p>Your trial ends on {{ $this->currentPlanStatus['trial_ends_at']->format('F j, Y') }}. Choose a plan to continue enjoying unlimited access.</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($this->currentPlanStatus['has_paid_subscription'])
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Active Subscription
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>You're currently subscribed to the <strong>{{ $this->currentPlan->label() }}</strong> plan. 
                            @if($this->currentPlanStatus['subscription_ends_at'])
                                Your subscription renews on {{ $this->currentPlanStatus['subscription_ends_at']->format('F j, Y') }}.
                            @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            @foreach($this->plans as $planValue => $planData)
                @php
                    $plan = $planData['plan'];
                @endphp
                <div class="relative bg-white rounded-lg shadow-lg border-2 {{ $selectedPlan === $plan ? 'border-blue-500' : ($planData['is_current'] ? 'border-green-500' : 'border-gray-200') }} hover:border-blue-300 transition-colors duration-200">
                    @if($planData['popular'])
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                                Most Popular
                            </span>
                        </div>
                    @endif
                    
                    @if($planData['is_current'])
                        <div class="absolute -top-4 right-4">
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                Current Plan
                            </span>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $planData['name'] }}
                            </h3>
                            <p class="text-gray-600 mb-4">
                                {{ $planData['description'] }}
                            </p>
                            <div class="text-4xl font-bold text-gray-900 mb-2">
                                {{ $planData['price'] }}
                                @if($plan !== \App\Enums\SubscriptionPlan::Lifetime)
                                    <span class="text-lg font-normal text-gray-500">/month</span>
                                @endif
                            </div>
                        </div>

                        <ul class="space-y-3 mb-6">
                            @foreach($planData['features'] as $feature)
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if($planData['is_current'])
                            <button 
                                disabled
                                class="w-full py-3 px-4 rounded-lg font-medium bg-green-100 text-green-800 cursor-not-allowed"
                            >
                                Current Plan
                            </button>
                        @else
                            <button 
                                wire:click="selectPlan({{ $planValue }})"
                                class="w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 {{ $selectedPlan === $plan ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}"
                            >
                                @if($selectedPlan === $plan)
                                    Selected
                                @elseif($planData['is_upgrade'])
                                    Upgrade to {{ $planData['name'] }}
                                @elseif($planData['is_downgrade'])
                                    Downgrade to {{ $planData['name'] }}
                                @else
                                    Select {{ $planData['name'] }}
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Subscribe Button -->
        <div class="text-center">
            @if($selectedPlan)
                @php
                    $currentPlan = $this->currentPlan;
                    $isUpgrade = $currentPlan->value < $selectedPlan->value;
                    $isDowngrade = $currentPlan->value > $selectedPlan->value;
                @endphp
                <button 
                    wire:click="subscribe"
                    wire:loading.attr="disabled"
                    wire:target="subscribe"
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-4 px-8 rounded-lg text-lg transition-colors duration-200"
                >
                    <span wire:loading.remove wire:target="subscribe">
                        @if($this->currentPlanStatus['has_paid_subscription'])
                            @if($isUpgrade)
                                Upgrade to {{ $selectedPlan->label() }}
                            @elseif($isDowngrade)
                                Downgrade to {{ $selectedPlan->label() }}
                            @else
                                Switch to {{ $selectedPlan->label() }}
                            @endif
                        @else
                            Subscribe to {{ $selectedPlan->label() }}
                        @endif
                    </span>
                    <span wire:loading wire:target="subscribe">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            @else
                <p class="text-gray-500">Please select a plan to continue</p>
            @endif
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Error
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">
                Frequently Asked Questions
            </h2>
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Can I cancel anytime?
                    </h3>
                    <p class="text-gray-600">
                        Yes, you can cancel your subscription at any time. You'll continue to have access until the end of your current billing period.
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        What happens after my trial ends?
                    </h3>
                    <p class="text-gray-600">
                        After your 14-day trial, you'll need to choose a subscription plan to continue using the service. Your account will be limited to free features until you subscribe.
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        Is my payment information secure?
                    </h3>
                    <p class="text-gray-600">
                        Yes, we use Stripe for secure payment processing. We never store your payment information on our servers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>