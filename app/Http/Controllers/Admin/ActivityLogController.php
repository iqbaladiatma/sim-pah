<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer:id,name,email', 'subject'])
            ->latest();

        // Filter berdasarkan user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter berdasarkan event
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        // Filter berdasarkan subject type (model)
        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }

        // Pagination dengan 20 item per halaman (lebih ringan)
        $activities = $query->paginate(20)->withQueryString();

        // Cache stats untuk 5 menit agar tidak query terus
        $stats = Cache::remember('activity_log_stats', 300, function () {
            return [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', today())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ];
        });

        // Get available subject types for filter
        $subjectTypes = Activity::select('subject_type')
            ->distinct()
            ->pluck('subject_type')
            ->filter()
            ->map(fn($type) => class_basename($type))
            ->values();

        return Inertia::render('Admin/ActivityLog/Index', [
            'activities' => $activities,
            'filters' => $request->only(['user_id', 'date_from', 'date_to', 'event', 'subject_type']),
            'stats' => $stats,
            'subjectTypes' => $subjectTypes,
        ]);
    }
}
