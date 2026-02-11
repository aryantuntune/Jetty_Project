<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyLock
{
    /**
     * Handle an incoming request.
     *
     * When FRONTEND_STACK=react, all legacy Blade routes are locked behind a 403
     * unless the authenticated user is a Super Admin (role_id = 1).
     *
     * This prevents accidental use of the old Blade UI while the React frontend
     * is the primary stack, while still allowing Super Admins to access it for
     * debugging or emergency fallback.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only enforce the lock when the React stack is active
        if (config('frontend.stack') !== 'react') {
            return $next($request);
        }

        $user = $request->user();

        // Allow Super Admins (role_id = 1) to pass through
        if ($user && $user->role_id === 1) {
            return $next($request);
        }

        // Block everyone else with 403
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Legacy interface is disabled. The system is running on the React frontend.',
                'hint' => 'Remove /legacy/ from the URL to use the current interface.',
            ], 403);
        }

        abort(403, 'Legacy interface is disabled. The system is running on the React frontend. Remove /legacy/ from the URL to use the current interface.');
    }
}
