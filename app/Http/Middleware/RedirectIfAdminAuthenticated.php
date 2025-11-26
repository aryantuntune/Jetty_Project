<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdminAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) { // admin logged in
            return redirect()->route('home'); // admin dashboard
        }

        return $next($request);
    }
}
