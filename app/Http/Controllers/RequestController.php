<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = \App\Models\Request::with(['user.institution', 'institution']);

        // Filter for Lembaga role
        if ($user->role === 'lembaga' && $user->institution_id) {
            $query->where('institution_id', $user->institution_id);
        }

        $requests = $query->latest()->paginate(10);

        return Inertia::render('Requests/Index', [
            'requests' => $requests
        ]);
    }

    public function create()
    {
        return Inertia::render('Requests/Create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Ensure user has permission (Lembaga or Admin)
        if (!in_array($user->role, ['lembaga', 'admin'])) {
            abort(403, 'Akses ditolak. Hanya karyawan lembaga atau admin yang bisa membuat request.');
        }

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

        // Auto-assign Institution ID from the User
        \App\Models\Request::create([
            'user_id' => $user->id,
            'institution_id' => $user->institution_id, // Automatically tracked
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
            'photo_evidence' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('requests.index')->with('success', 'Pengajuan berhasil dikirim.');
    }

// Update is handled by Admin/GeneralRequestController for approval
// But if we need 'edit' for the user (e.g. while pending), we can add it later.
}
