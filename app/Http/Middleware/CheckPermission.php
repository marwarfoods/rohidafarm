<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!\Auth::check()) {
            return redirect()->route('login');
        }

        // Determine the CRUD action based on request method and path/name
        $method = strtoupper($request->method());
        $routeName = $request->route() ? $request->route()->getName() : '';
        $action = 'view'; // default fallback

        if ($method === 'DELETE' || str_contains($routeName, 'delete') || str_contains($routeName, 'destroy') || str_contains($routeName, 'force-delete') || str_contains($routeName, 'bulk-delete') || str_contains($routeName, 'clear')) {
            $action = 'delete';
        } elseif ($method === 'POST' && (str_contains($routeName, 'store') || str_contains($routeName, 'create'))) {
            $action = 'create';
        } elseif (str_contains($routeName, 'create')) {
            $action = 'create';
        } elseif ($method === 'PUT' || $method === 'PATCH' || str_contains($routeName, 'edit') || str_contains($routeName, 'update') || str_contains($routeName, 'status') || str_contains($routeName, 'cancel') || str_contains($routeName, 'sync') || str_contains($routeName, 'reorder') || str_contains($routeName, 'compress')) {
            $action = 'edit';
        } elseif ($method === 'POST') {
            // Any other mutating POST action (e.g. assign, stock, approve, url-import)
            // must require edit-level permission rather than silently falling back to view.
            $action = 'edit';
        } else {
            $action = 'view';
        }

        $specificPermission = "{$permission}-{$action}";

        if (\Auth::user()->hasPermission($specificPermission)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => "Access Denied: You do not have the required permission to {$action} {$permission}."], 403);
        }

        return redirect()->route('admin.dashboard')->with('error', "Access Denied: You do not have permission to {$action} {$permission}.");
    }
}
