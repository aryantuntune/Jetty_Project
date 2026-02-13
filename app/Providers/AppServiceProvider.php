<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        $this->configureRateLimiting();
    }

    /**
     * Configure smart rate limiting for the application.
     *
     * Strategy:
     * - Authenticated users get generous limits (they're verified customers)
     * - Unauthenticated requests are throttled by IP
     * - Login endpoints use strict IP-based limits (brute-force protection)
     * - Booking/payment endpoints are generous for auth users but strict per-IP for guests
     */
    protected function configureRateLimiting(): void
    {
        // Login: strict IP-based limiting (brute-force protection)
        RateLimiter::for('login', function (Request $request) {
            $key = $request->input('email', '') . '|' . $request->ip();
            return Limit::perMinute(5)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again in a minute.',
                ], 429);
            });
        });

        // Booking: generous for authenticated users, strict for single IP abuse
        RateLimiter::for('booking', function (Request $request) {
            $user = $request->user();
            if ($user) {
                // Authenticated user: 60 bookings per minute (handles last-minute rush)
                return Limit::perMinute(60)->by('user:' . $user->id);
            }
            // Unauthenticated (shouldn't happen, but fallback): 5 per minute per IP
            return Limit::perMinute(5)->by('ip:' . $request->ip());
        });

        // Payment verification: generous for auth users
        RateLimiter::for('payment', function (Request $request) {
            $user = $request->user();
            if ($user) {
                // Authenticated user: 60 verifications per minute
                return Limit::perMinute(60)->by('user:' . $user->id);
            }
            return Limit::perMinute(5)->by('ip:' . $request->ip());
        });

        // Checker verification: high limit for checkers scanning tickets rapidly
        RateLimiter::for('checker-verify', function (Request $request) {
            $user = $request->user();
            if ($user) {
                // Checker: 120 scans per minute (2 per second is fast enough)
                return Limit::perMinute(120)->by('checker:' . $user->id);
            }
            return Limit::perMinute(5)->by('ip:' . $request->ip());
        });
    }
}