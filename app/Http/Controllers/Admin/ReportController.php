<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Item;
use App\Models\Request as GeneralRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $institution_stats = Institution::withCount(['items', 'requests'])->get()->map(function ($inst) {
            return [
                'id' => $inst->id,
                'name' => $inst->name,
                'code' => $inst->code,
                'items_count' => $inst->items_count,
                'requests_count' => $inst->requests_count,
                'total_cost' => GeneralRequest::where('institution_id', $inst->id)->sum('estimated_cost'),
                'pending_requests' => GeneralRequest::where('institution_id', $inst->id)->where('status', 'pending')->count(),
            ];
        });

        return inertia('Admin/Reports/Index', [
            'stats' => $institution_stats
        ]);
    }
}
