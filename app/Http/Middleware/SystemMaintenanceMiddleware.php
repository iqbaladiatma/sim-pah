<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemMaintenanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenance = \App\Models\SystemSetting::where('key', 'app_maintenance_mode')->first();

        if ($maintenance && $maintenance->value === '1') {
            $user = $request->user();

            // Bypass for admins and super admins
            if (!$user || !in_array($user->role, ['admin', 'super admin'])) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Sistem sedang dalam pemeliharaan rutin.'], 503);
                }

                return inertia('Maintenance', [
                    'message' => 'Aplikasi SIM PAH sedang dalam proses pemeliharaan rutin oleh tim IT URT. Silakan coba beberapa saat lagi.'
                ])->toResponse($request)->setStatusCode(503);
            }
        }

        return $next($request);
    }
}
