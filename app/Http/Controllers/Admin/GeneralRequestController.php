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

    public function create()
    {
        return inertia('Admin/Requests/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'photo_evidence' => 'nullable|image|max:2048',
        ]);

        $path = $request->hasFile('photo_evidence')
            ? $request->file('photo_evidence')->store('reports', 'public')
            : null;

        $user = $request->user();

        GeneralRequest::create([
            'user_id' => $user->id,
            'institution_id' => $user->institution_id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
            'photo_evidence' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Pengajuan berhasil dibuat.');
    }

    public function edit(GeneralRequest $request)
    {
        $request->load(['user.institution', 'institution']);
        return inertia('Admin/Requests/Edit', [
            'request' => $request
        ]);
    }

    // Fixed: Renamed $request to $httpRequest to allow model binding $request (route param 'request')
    public function update(Request $httpRequest, GeneralRequest $request)
    {
        $validated = $httpRequest->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $request->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        return redirect()->route('admin.requests.index')->with('success', 'Status pengajuan diperbarui.');
    }
}
