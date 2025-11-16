<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockRole5
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    // role 5 can ONLY access verify & logout
    if (auth()->user()->role_id == 5) {

        if (! $request->is('verify') && ! $request->is('verify/*') && 
            ! $request->is('logout')) {

            return redirect()->route('verify.index');
        }
    }

    return $next($request);
}

}
