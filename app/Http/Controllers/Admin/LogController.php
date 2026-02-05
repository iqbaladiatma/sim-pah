<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::with('causer')->latest()->paginate(20);

        return inertia('Admin/Logs/Index', [
            'logs' => $logs
        ]);
    }
}
