<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    // Use as: ->middleware('role:1,2')
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin (role_id=1) ALWAYS has access to everything
        if ((int) $user->role_id === 1) {
            return $next($request);
        }

        $allowed = array_map('strval', $roles);
        if (in_array((string) $user->role_id, $allowed, true)) {
            return $next($request);
        }

        if ($roles == 'only5') {                 // custom rule
            if (auth()->user()->role_id != 5) {
                abort(403);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}