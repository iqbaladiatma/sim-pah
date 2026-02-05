<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return inertia('Admin/Dashboard', [
            'stats' => [
                'total_institutions' => \App\Models\Institution::count(),
                'pending_requests' => \App\Models\Request::where('status', 'pending')->count(),
                'low_stock_items' => \App\Models\Item::whereColumn('stock', '<=', 'min_stock')->count(),
            ],
            'recent_activity' => \Spatie\Activitylog\Models\Activity::latest()->take(5)->get(),
        ]);
    }
}
