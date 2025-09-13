<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Admins always have access
        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        // Check if user has an active subscription (including trial)
        if ($user->hasActiveSubscription()) {
            return $next($request);
        }

        // If user needs to choose a subscription plan, redirect to subscription page
        if ($user->needsSubscriptionChoice()) {
            return redirect()->route('app.subscription.choose');
        }

        // If user is on trial but it's expired, redirect to subscription page
        if ($user->has_used_trial && !$user->isOnTrial()) {
            return redirect()->route('app.subscription.choose');
        }

        // For API requests, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'SUBSCRIPTION_REQUIRED',
                'message' => 'An active subscription is required to access this feature.',
            ], 403);
        }

        // For web requests, redirect to subscription page
        return redirect()->route('app.subscription.choose');
    }
}
