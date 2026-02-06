<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as GeneralRequest;
use Inertia\Inertia;

class GeneralRequestController extends Controller
{
    public function index()
    {
        // For Admin: View All Requests
        $requests = GeneralRequest::with(['user.institution', 'institution'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Requests/Index', [
            'requests' => $requests,
            'stats' => [
                'pending' => GeneralRequest::where('status', 'pending')->count(),
                'approved' => GeneralRequest::where('status', 'approved')->count(),
                'rejected' => GeneralRequest::where('status', 'rejected')->count(),
                'total_cost_pending' => GeneralRequest::where('status', 'pending')->sum('estimated_cost'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'photo_evidence' => 'nullable|image|max:2048', // Max 2MB, optional
        ]);

        $path = $request->hasFile('photo_evidence')
            ? $request->file('photo_evidence')->store('reports', 'public')
            : null;

        $user = $request->user();

        GeneralRequest::create([
            'user_id' => $user->id,
            'institution_id' => $validated['institution_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
            'photo_evidence' => $path,
            'status' => 'pending',
            'is_admin_create' => true // Optional flag if we want to track admin activity
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Pengajuan berhasil dibuat oleh Admin.');
    }

    public function create()
    {
        $institutions = \App\Models\Institution::orderBy('code')->get();

        return Inertia::render('Admin/Requests/Create', [
            'institutions' => $institutions
        ]);
    }

    public function edit(GeneralRequest $request)
    {
        return Inertia::render('Admin/Requests/Edit', [
            'request' => $request->load(['user.institution', 'institution'])
        ]);
    }

    public function update(Request $httpRequest, GeneralRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string'
        ]);

        // Explicitly update the passed model instance
        $request->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note']
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function destroy(GeneralRequest $request)
    {
        $request->delete();
        return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
    }
}
