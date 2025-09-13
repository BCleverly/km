<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPremiumSubscription
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

        // Check if user has premium features (Premium, Couple, or Lifetime)
        if ($user->hasActivePremiumSubscription() || $user->hasLifetimeSubscription()) {
            return $next($request);
        }

        // For API requests, return JSON response
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'PREMIUM_SUBSCRIPTION_REQUIRED',
                'message' => 'A premium subscription is required to access this feature.',
            ], 403);
        }

        // For web requests, redirect to subscription page
        return redirect()->route('app.subscription.choose')
            ->with('error', 'A premium subscription is required to access this feature.');
    }
}
