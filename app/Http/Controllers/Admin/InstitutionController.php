<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = \App\Models\Institution::latest()->paginate(10);
        return inertia('Admin/Institutions/Index', [
            'institutions' => $institutions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:institutions,code',
            'description' => 'nullable|string',
        ]);

        \App\Models\Institution::create($validated);

        return redirect()->back()->with('success', 'Lembaga berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:institutions,code,' . $institution->id,
            'description' => 'nullable|string',
        ]);

        $institution->update($validated);

        return redirect()->back()->with('success', 'Lembaga berhasil diperbarui.');
    }

    public function destroy(\App\Models\Institution $institution)
    {
        $institution->delete();
        return redirect()->back()->with('success', 'Lembaga berhasil dihapus.');
    }
}
