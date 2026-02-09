<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Institution::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $institutions = $query->latest()->paginate(10)->withQueryString();

        return inertia('Admin/Institutions/Index', [
            'institutions' => $institutions,
            'filters' => $request->only(['search']),
            'stats' => [
                'total' => \App\Models\Institution::count(),
                'new_this_month' => \App\Models\Institution::whereMonth('created_at', now()->month)->count(),
            ]
        ]);
    }

    public function create()
    {
        return inertia('Admin/Institutions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:institutions,code',
            'description' => 'nullable|string',
        ]);

        \App\Models\Institution::create($validated);

        return redirect()->route('admin.institutions.index')->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function edit(\App\Models\Institution $institution)
    {
        return inertia('Admin/Institutions/Edit', [
            'institution' => $institution
        ]);
    }

    public function update(Request $request, \App\Models\Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:institutions,code,' . $institution->id,
            'description' => 'nullable|string',
        ]);

        $institution->update($validated);

        return redirect()->route('admin.institutions.index')->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function destroy(\App\Models\Institution $institution)
    {
        $institution->delete();
        return redirect()->back()->with('success', 'Lembaga berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\InstitutionImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data lembaga berhasil diimpor.');
    }

    public function downloadTemplate()
    {
        $headers = ['code', 'name'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['SMA-AH', 'SMA Abu Hurairah']);
            fputcsv($file, ['SMP-AH', 'SMP Abu Hurairah']);
            fclose($file);
        };

        return response()->streamDownload($callback, 'template_lembaga.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
