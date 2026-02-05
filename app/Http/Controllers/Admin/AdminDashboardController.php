<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\Request as GeneralRequest;
use App\Models\Item;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return inertia('Admin/Dashboard', [
            'stats' => [
                'total_institutions' => Institution::count(),
                'total_users' => User::count(),
                'total_items' => Item::count(),
                'pending_requests' => GeneralRequest::where('status', 'pending')->count(),
                'low_stock_items' => Item::whereColumn('stock', '<=', 'min_stock')->count(),
            ],
            'recent_requests' => GeneralRequest::with(['user.institution', 'institution'])
            ->latest()
            ->take(5)
            ->get(),
            'recent_activity' => Activity::with('causer')
            ->latest()
            ->take(6)
            ->get(),
        ]);
    }
}
