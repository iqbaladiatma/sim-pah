<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request as GeneralRequest;
use Illuminate\Http\Request;

class GeneralRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = GeneralRequest::with(['user.institution', 'institution']);

        $requests = $query->latest()->paginate(10);

        return inertia('Admin/Requests/Index', [
            'requests' => $requests
        ]);
    }

    public function update(Request $request, GeneralRequest $generalRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $generalRequest->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Status pengajuan diperbarui.');
    }
}
