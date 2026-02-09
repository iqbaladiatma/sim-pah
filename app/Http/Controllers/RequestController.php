<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestController extends Controller
{
    public function index(Request $httpRequest)
    {
        $user = $httpRequest->user();
        $query = \App\Models\Request::with(['user.institution', 'institution']);

        // Filter for Lembaga role
        if ($user->role === 'lembaga' && $user->institution_id) {
            $query->where('institution_id', $user->institution_id);
        }

        // Search Filter
        if ($search = $httpRequest->query('search')) {
            $query->where(function (\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $httpRequest->query('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Requests/Index', [
            'requests' => $requests,
            'filters' => [
                'search' => $httpRequest->query('search', ''),
                'status' => $httpRequest->query('status', 'all'),
            ]
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
            'type' => 'required|in:utilitas,barang_habis_pakai,darurat',
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

    public function edit(\App\Models\Request $request)
    {
        $user = auth()->user();

        // Check ownership
        if ($request->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda hanya bisa mengedit pengajuan Anda sendiri.');
        }

        // Only allow editing pending ones.
        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
        }

        return Inertia::render('Requests/Edit', [
            'request' => $request
        ]);
    }

    public function show(\App\Models\Request $request)
    {
        $user = auth()->user();

        // Multi-tenancy check
        if ($request->institution_id !== $user->institution_id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat pengajuan ini.');
        }

        return Inertia::render('Requests/Show', [
            'request' => $request->load(['user', 'institution'])
        ]);
    }

    public function update(Request $httpRequest, \App\Models\Request $request)
    {
        $user = auth()->user();

        if ($request->user_id !== $user->id) {
            abort(403);
        }

        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Pengajuan yang sudah diproses tidak bisa diedit.');
        }

        $validated = $httpRequest->validate([
            'type' => 'required|in:utilitas,barang_habis_pakai,darurat',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'photo_evidence' => 'nullable|image|max:2048',
        ]);

        $data = [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
        ];

        if ($httpRequest->hasFile('photo_evidence')) {
            $data['photo_evidence'] = $httpRequest->file('photo_evidence')->store('reports', 'public');
        }

        $request->update($data);

        return redirect()->route('requests.index')->with('success', 'Pengajuan berhasil diperbarui.');
    }

    public function destroy(Request $httpRequest, \App\Models\Request $request)
    {
        $user = auth()->user();

        // Check ownership
        if ($request->user_id !== $user->id) {
            abort(403);
        }

        // Only allow deleting pending ones
        if ($request->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status (Tinjauan) yang dapat dihapus.');
        }

        $request->update([
            'deletion_reason' => $httpRequest->deletion_reason ?? 'Dihapus oleh pengguna tanpa alasan.'
        ]);

        $request->delete();

        return redirect()->route('requests.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
