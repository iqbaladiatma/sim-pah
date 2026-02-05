<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = \App\Models\Item::with(['institution', 'room']);

        if ($user->role === 'karyawan') {
            $query->where('institution_id', $user->institution_id);
        }

        $items = $query->latest()->paginate(10);

        // For Admin to select institution when creating items
        $institutions = $user->role === 'admin' ?\App\Models\Institution::all() : [];

        return inertia('Items/Index', [
            'items' => $items,
            'institutions' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        // Admin Only
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|integer|min:0',
        ]);

        \App\Models\Item::create($validated);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\Item $item)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Admin: Direct Update
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'unit' => 'required|string|max:50',
                'min_stock' => 'required|integer|min:0',
            ]);

            $item->update($validated);
            return redirect()->back()->with('success', 'Barang berhasil diperbarui.');

        }
        else {
            // Karyawan: Create Update Request
            if ($item->institution_id !== $user->institution_id)
                abort(403);

            $validated = $request->validate([
                'stock' => 'required|integer|min:0',
                'reason' => 'required|string|max:500',
            ]);

            \App\Models\ItemUpdateRequest::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'old_data' => ['stock' => $item->stock],
                'new_data' => ['stock' => $validated['stock']],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Permintaan update stok dikirim ke Admin.');
        }
    }

    public function destroy(Request $request, \App\Models\Item $item)
    {
        if ($request->user()->role !== 'admin')
            abort(403);
        $item->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
