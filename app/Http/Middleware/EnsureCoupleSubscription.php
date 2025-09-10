<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCoupleSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not authenticated, let them through (auth middleware will handle this)
        if (!$user) {
            return $next($request);
        }

        // If user is admin, let them through
        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        // If user is part of a couple, check if either partner has an active subscription
        if ($user->isPartOfCouple()) {
            // If neither partner has an active subscription, redirect to subscription management
            if (!$user->hasActiveSubscription()) {
                return redirect()->route('subscription.manage')
                    ->with('error', 'Your couple subscription has expired. Please renew to continue accessing the platform.');
            }
        }

        return $next($request);
    }
}