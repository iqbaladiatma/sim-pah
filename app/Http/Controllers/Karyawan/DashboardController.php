<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $institutionId = $request->user()->institution_id;

        return inertia('Karyawan/Dashboard', [
            'stats' => [
                'my_items' => \App\Models\Item::where('institution_id', $institutionId)->count(),
                'my_pending_requests' => \App\Models\Request::where('institution_id', $institutionId)->where('status', 'pending')->count(),
            ],
            'last_requests' => \App\Models\Request::where('institution_id', $institutionId)->latest()->take(5)->get(),
        ]);
    }
}
