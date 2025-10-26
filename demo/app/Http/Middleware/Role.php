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

        $allowed = array_map('strval', $roles);
        if (in_array((string) $user->role_id, $allowed, true)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}