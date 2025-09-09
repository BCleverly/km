<div>
<!-- Navigation -->
<nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-gray-900" wire:navigate>
                    Kink Master
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-gray-600 hover:text-gray-900 transition-colors">Features</a>
                <a href="#how-it-works" class="text-gray-600 hover:text-gray-900 transition-colors">How It Works</a>
                <a href="#pricing" class="text-gray-600 hover:text-gray-900 transition-colors">Pricing</a>
                @auth
                    <a href="/app/dashboard" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" wire:navigate>
                        Go to App
                    </a>
                @else
                    <a href="/login" class="text-gray-600 hover:text-gray-900 transition-colors" wire:navigate>Sign In</a>
                    <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" wire:navigate>
                        Get Started
                    </a>
                @endauth
            </div>
            
            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-600 hover:text-gray-900" wire:click="toggleMobileMenu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu -->
        @if($showMobileMenu)
        <div class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#features" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">Features</a>
                <a href="#how-it-works" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">How It Works</a>
                <a href="#pricing" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900">Pricing</a>
                @auth
                    <a href="/app/dashboard" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900" wire:navigate>Go to App</a>
                @else
                    <a href="/login" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900" wire:navigate>Sign In</a>
                    <a href="/register" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900" wire:navigate>Get Started</a>
                @endauth
            </div>
        </div>
        @endif
    </div>
</nav>

<!-- Hero Section -->
<section class="pt-20 pb-16 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Your Ultimate
                <span class="text-red-600">Task & Reward</span>
                Community
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                Join a trusted community where tasks become adventures, completion brings rewards, 
                and every challenge is an opportunity to grow and explore.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="/app/dashboard" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105" wire:navigate>
                        Go to App
                    </a>
                @else
                    <a href="/register" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105" wire:navigate>
                        Start Your Journey
                    </a>
                @endauth
                <a href="#how-it-works" class="border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Why Choose Kink Master?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Experience a unique blend of gamification, community, and personal growth
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="text-center p-8 rounded-xl border border-gray-200 hover:border-red-200 hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Gamified Tasks</h3>
                <p class="text-gray-600">
                    Get assigned personalized tasks and earn rewards or face consequences based on your completion. 
                    Every action has meaning in our trust-based system.
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="text-center p-8 rounded-xl border border-gray-200 hover:border-red-200 hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Community Driven</h3>
                <p class="text-gray-600">
                    Contribute to our growing library of tasks, rewards, and stories. 
                    Your submissions help build a richer experience for everyone.
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="text-center p-8 rounded-xl border border-gray-200 hover:border-red-200 hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Trust & Privacy</h3>
                <p class="text-gray-600">
                    Built on a foundation of trust with robust privacy controls. 
                    Your journey is yours alone, shared only when you choose.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="how-it-works" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                How It Works
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Simple steps to start your personalized journey
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Step 1 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                    1
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Sign Up & Set Preferences</h3>
                <p class="text-gray-600">
                    Create your account and tell us about your preferences, interests, and comfort levels. 
                    We'll personalize your experience from day one.
                </p>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                    2
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Get Assigned Tasks</h3>
                <p class="text-gray-600">
                    Receive carefully curated tasks based on your profile. 
                    Each task is designed to challenge and engage you at the right level.
                </p>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-600 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                    3
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Complete & Report</h3>
                <p class="text-gray-600">
                    Complete your tasks and report your results. 
                    Earn rewards for success or face consequences for failure - it's all part of the journey.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Simple, Transparent Pricing
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Start free, upgrade when you're ready for more
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Free Plan -->
            <div class="p-8 rounded-xl border border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Free</h3>
                <p class="text-gray-600 mb-6">Perfect for getting started</p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Limited daily tasks
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Basic rewards & consequences
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Community access
                    </li>
                </ul>
                <a href="/register" class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-lg font-medium transition-colors text-center block" wire:navigate>
                    Get Started Free
                </a>
</div>

            <!-- Premium Plan -->
            <div class="p-8 rounded-xl border-2 border-red-600 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-red-600 text-white px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Premium</h3>
                <p class="text-gray-600 mb-6">Unlimited access to everything</p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Unlimited daily tasks
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Premium rewards & consequences
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Advanced customization
                    </li>
                    <li class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Priority support
                    </li>
                </ul>
                <a href="/register" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-medium transition-colors text-center block" wire:navigate>
                    Start Premium Trial
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-red-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Ready to Start Your Journey?
        </h2>
        <p class="text-xl text-red-100 mb-8 max-w-2xl mx-auto">
            Join thousands of users who have transformed their daily routines into exciting adventures
        </p>
        @auth
            <a href="/app/dashboard" class="bg-white hover:bg-gray-100 text-red-600 px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 inline-block" wire:navigate>
                Go to App
            </a>
        @else
            <a href="/register" class="bg-white hover:bg-gray-100 text-red-600 px-8 py-4 rounded-lg font-semibold text-lg transition-all transform hover:scale-105 inline-block" wire:navigate>
                Get Started Today
            </a>
        @endauth
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">Kink Master</h3>
                <p class="text-gray-400">
                    Your ultimate task and reward community for personal growth and exploration.
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Product</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Community</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Guidelines</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Safety</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Kink Master. All rights reserved.</p>
        </div>
    </div>
</footer>
</div>