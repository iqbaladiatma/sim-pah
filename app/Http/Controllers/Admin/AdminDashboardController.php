<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Request as GeneralRequest;
use App\Models\Item;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        try {
            $online_count = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                ->count();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Online Users Error: ' . $e->getMessage());
            $online_count = 0;
        }

        try {
            $stats = [
                // Core counts
                'total_institutions' => Institution::count(),
                'total_users' => User::count(),
                'total_items' => Item::count(),
                'pending_requests' => GeneralRequest::where('status', 'pending')->count(),

                // Inventory checks
                'low_stock_items' => Item::whereColumn('stock', '<=', 'min_stock')->count(),
                'out_of_stock' => Item::where('stock', '<=', 0)->count(),

                // Financials
                'total_cost_pending' => (float) GeneralRequest::where('status', 'pending')->sum('estimated_cost'),
                'total_cost_approved' => (float) GeneralRequest::where('status', 'approved')->sum('estimated_cost'),

                // Realtime
                'online_users' => $online_count,
            ];
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Stats Error: ' . $e->getMessage());
            $stats = [
                'total_institutions' => 0,
                'total_users' => 0,
                'total_items' => 0,
                'pending_requests' => 0,
                'low_stock_items' => 0,
                'out_of_stock' => 0,
                'total_cost_pending' => 0,
                'total_cost_approved' => 0,
                'online_users' => 0,
            ];
        }

        try {
            $recent_requests = GeneralRequest::with(['user.institution', 'institution'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Recent Requests Error: ' . $e->getMessage());
            $recent_requests = [];
        }

        try {
            $recent_activity = Activity::with('causer')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Dashboard Activity Log Error: ' . $e->getMessage());
            $recent_activity = [];
        }

        return inertia('Admin/Dashboard', [
            'stats' => $stats,
            'recent_requests' => $recent_requests,
            'recent_activity' => $recent_activity,
        ]);
    }
}
