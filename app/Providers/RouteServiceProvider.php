<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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
        //
    }

    /**
     * Get the URL prefix for React/Inertia routes.
     *
     * When FRONTEND_STACK=react: returns '' (root)
     * When FRONTEND_STACK=blade: returns 'v2'
     */
    public static function reactPrefix(): string
    {
        return config('frontend.stack') === 'react' ? '' : 'v2';
    }

    /**
     * Get the URL prefix for legacy Blade routes.
     *
     * When FRONTEND_STACK=react: returns 'legacy'
     * When FRONTEND_STACK=blade: returns '' (root)
     */
    public static function legacyPrefix(): string
    {
        return config('frontend.stack') === 'react' ? 'legacy' : '';
    }

    /**
     * Check if the active frontend stack is React.
     */
    public static function isReact(): bool
    {
        return config('frontend.stack') === 'react';
    }

    /**
     * Get the middleware array to apply to legacy routes.
     *
     * When React is active, legacy routes are locked behind legacy.lock middleware.
     * When Blade is active, no extra middleware is applied.
     *
     * @return array
     */
    public static function legacyMiddleware(): array
    {
        return self::isReact() ? ['legacy.lock'] : [];
    }
}
