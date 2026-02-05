<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Request::with(['user.institution', 'institution']);

        if ($user->role === 'karyawan') {
            $query->where('institution_id', $user->institution_id);
        }

        $requests = $query->latest()->paginate(10);

        return inertia('Requests/Index', [
            'requests' => $requests
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'karyawan' && $user->role !== 'admin')
            abort(403, 'Hanya karyawan atau admin yang bisa membuat request.');

        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'photo_evidence' => 'nullable|image|max:2048', // Max 2MB, optional
        ]);

        $path = $request->hasFile('photo_evidence')
            ? $request->file('photo_evidence')->store('reports', 'public')
            : null;

        \App\Models\Request::create([
            'user_id' => $user->id,
            'institution_id' => $user->institution_id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
            'photo_evidence' => $path,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan berhasil dikirim.');
    }

    public function update(Request $request, \App\Models\Request $generalRequest)
    {
        // Renamed variable to avoid conflict with Type Request
        if ($request->user()->role !== 'admin')
            abort(403);

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
