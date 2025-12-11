<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // If customer is logged in and visiting admin login page → redirect to customer dashboard
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        // If admin (default user) is logged in → redirect to admin dashboard
        if (Auth::guard('web')->check()) {
            return redirect('/home'); // your admin dashboard route
        }

        return $next($request);
    }
}
