<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            // Customer area → go to customer login
            if ($request->is('customer/*')) {
                return route('customer.login');
            }

            // Default admin login
            return route('login');
        }
    }
}
