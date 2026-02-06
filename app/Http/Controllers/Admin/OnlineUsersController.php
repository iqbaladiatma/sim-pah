<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use App\Models\User;

class OnlineUsersController extends Controller
{
    public function index()
    {
        // Get all active sessions from database
        $sessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->get()
            ->map(function ($session) {
            $user = User::find($session->user_id);
            $payload = unserialize(base64_decode($session->payload));

            return [
            'id' => $session->id,
            'user_id' => $session->user_id,
            'user' => $user,
            'ip_address' => $session->ip_address,
            'user_agent' => $session->user_agent,
            'browser' => $this->getBrowserInfo($session->user_agent),
            'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity),
            'is_current' => $session->id === session()->getId(),
            ];
        });

        return Inertia::render('Admin/OnlineUsers/Index', [
            'sessions' => $sessions,
            'stats' => [
                'total_online' => $sessions->count(),
                'admins_online' => $sessions->filter(fn($s) => $s['user'] && in_array($s['user']->role, ['admin', 'super admin']))->count(),
                'lembaga_online' => $sessions->filter(fn($s) => $s['user'] && $s['user']->role === 'lembaga')->count(),
            ]
        ]);
    }

    public function kick(Request $request, $sessionId)
    {
        // Prevent kicking own session
        if ($sessionId === session()->getId()) {
            return back()->with('error', 'Tidak dapat mengeluarkan session sendiri.');
        }

        // Delete the session
        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Session berhasil dikeluarkan.');
    }

    private function getBrowserInfo($userAgent)
    {
        // Simple browser detection
        if (strpos($userAgent, 'Chrome') !== false) {
            return ['name' => 'Chrome', 'icon' => '🌐'];
        }
        elseif (strpos($userAgent, 'Firefox') !== false) {
            return ['name' => 'Firefox', 'icon' => '🦊'];
        }
        elseif (strpos($userAgent, 'Safari') !== false) {
            return ['name' => 'Safari', 'icon' => '🧭'];
        }
        elseif (strpos($userAgent, 'Edge') !== false) {
            return ['name' => 'Edge', 'icon' => '🔷'];
        }
        elseif (strpos($userAgent, 'Opera') !== false) {
            return ['name' => 'Opera', 'icon' => '🎭'];
        }
        else {
            return ['name' => 'Unknown', 'icon' => '❓'];
        }
    }
}
