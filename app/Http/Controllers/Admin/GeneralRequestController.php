<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as GeneralRequest;
use App\Exports\RequestsExport;
use Maatwebsite\Excel\Facades\Excel;
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
                'total_cost_pending' => (float) GeneralRequest::where('status', 'pending')->sum('estimated_cost'),
                'total_cost_approved' => (float) GeneralRequest::where('status', 'approved')->sum('estimated_cost'),
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
            'request' => $request->load(['user.institution', 'institution']),
            'institutions' => \App\Models\Institution::orderBy('code')->get(),
        ]);
    }

    public function update(Request $httpRequest, GeneralRequest $request)
    {
        $validated = $httpRequest->validate([
            'institution_id' => 'required|exists:institutions,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string',
            'photo_evidence' => 'nullable|image|max:2048',
        ]);

        $data = [
            'institution_id' => $validated['institution_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'estimated_cost' => $validated['estimated_cost'],
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'],
        ];

        if ($httpRequest->hasFile('photo_evidence')) {
            $data['photo_evidence'] = $httpRequest->file('photo_evidence')->store('reports', 'public');
        }

        $request->update($data);

        return redirect()->route('admin.requests.index')->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    public function destroy(GeneralRequest $request)
    {
        $request->delete();
        return redirect()->back()->with('success', 'Pengajuan berhasil dihapus.');
    }

    /**
     * Export requests to Excel
     */
    public function export(Request $request)
    {
        $status = $request->query('status', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $fileName = 'Pengajuan_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new RequestsExport($status, $startDate, $endDate),
            $fileName
        );
    }
}
