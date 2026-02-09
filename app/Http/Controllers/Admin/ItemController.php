<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('institution_id') && $request->institution_id) {
            $institution = \App\Models\Institution::findOrFail($request->institution_id);
            $query = Item::with(['institution', 'room'])
                ->where('institution_id', $institution->id);

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            return Inertia::render('Admin/Items/Index', [
                'mode' => 'list',
                'institution' => $institution,
                'items' => $query->latest()->paginate(10)->withQueryString(),
                'filters' => $request->only(['search', 'institution_id']),
                'stats' => [
                    'total_items' => Item::where('institution_id', $institution->id)->count(),
                    'total_stock' => Item::where('institution_id', $institution->id)->sum('stock'),
                    'low_stock' => Item::where('institution_id', $institution->id)->whereColumn('stock', '<=', 'min_stock')->count(),
                    'total_value' => 0
                ]
            ]);
        } else {
            $query = \App\Models\Institution::withCount('items');

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            }

            return Inertia::render('Admin/Items/Index', [
                'mode' => 'folders',
                'institutions' => $query->get(),
                'filters' => $request->only(['search']),
                'stats' => [
                    'total_items' => Item::count(),
                    'total_stock' => Item::sum('stock'),
                    'low_stock' => Item::whereColumn('stock', '<=', 'min_stock')->count(),
                    'total_value' => 0
                ]
            ]);
        }
    }

    public function create()
    {
        return Inertia::render('Admin/Items/Create', [
            'institutions' => \App\Models\Institution::all(),
            'rooms' => \App\Models\Room::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'nullable|string', // mapped to note? No model has note. Description is not in fillable? Wait model has note.
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'source' => 'nullable|string',
            'condition' => 'nullable|string',
            'responsible_person' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        // Note: Model has no image field in fillable.

        Item::create([
            'institution_id' => $validated['institution_id'] ?? null,
            'room_id' => $request->room_id,
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'] ?? 0,
            'unit' => $validated['unit'] ?? 'pcs',
            'purchase_date' => $validated['purchase_date'] ?? null,
            'source' => $validated['source'] ?? null,
            'condition' => $validated['condition'] ?? 'Baik',
            'responsible_person' => $validated['responsible_person'] ?? null,
            'note' => $validated['note'] ?? $request->description, // Map description to note if needed or ignore
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        return Inertia::render('Admin/Items/Edit', [
            'item' => $item->load(['institution', 'room']),
            'institutions' => \App\Models\Institution::all(),
            'rooms' => \App\Models\Room::all(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'purchase_date' => 'nullable|date',
            'source' => 'nullable|string',
            'condition' => 'nullable|string',
            'responsible_person' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        $item->update([
            'institution_id' => $validated['institution_id'] ?? null,
            'room_id' => $request->room_id,
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'stock' => $validated['stock'],
            'min_stock' => $validated['min_stock'] ?? 0,
            'unit' => $validated['unit'] ?? 'pcs',
            'purchase_date' => $validated['purchase_date'] ?? null,
            'source' => $validated['source'] ?? null,
            'condition' => $validated['condition'] ?? 'Baik',
            'responsible_person' => $validated['responsible_person'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
