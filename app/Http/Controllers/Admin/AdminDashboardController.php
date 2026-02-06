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
        $online_count = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->count();

        return inertia('Admin/Dashboard', [
            'stats' => [
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
            ],
            'recent_requests' => GeneralRequest::with(['user.institution', 'institution'])
                ->latest()
                ->take(5)
                ->get(),
            'recent_activity' => Activity::with('causer')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
