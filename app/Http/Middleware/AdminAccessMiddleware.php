<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Only allows users with 'admin' or 'super admin' roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        // Allow both 'admin' and 'super admin' roles
        if (!in_array($user->role, ['admin', 'super admin'])) {
            abort(403, 'Unauthorized action. Admin access required.');
        }

        return $next($request);
    }
}
