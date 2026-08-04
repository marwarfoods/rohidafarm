<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request and check if user has Admin role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin() || Auth::user()->roles()->exists()) {
                return $next($request);
            }
            return redirect()->route('customer.dashboard')->with('error', 'Access denied. Administrative privileges required.');
        }

        return redirect()->route('login')->with('error', 'Unauthorized access. Administrative privileges required.');
    }
}
