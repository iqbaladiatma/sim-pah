<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUpdateRequest;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin' || $user->is_super_admin;

        $query = Item::with(['institution', 'room']);

        if (!$isAdmin) {
            $query->where('institution_id', $user->institution_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('brand', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->paginate(10);

        // For Admin to select institution when creating items
        $institutions = $isAdmin ?Institution::all() : [];

        // Pass rooms for dropdown
        $rooms = [];
        if (!$isAdmin) {
            $rooms = \App\Models\Room::where('institution_id', $user->institution_id)->get();
        }
        else {
            $rooms = \App\Models\Room::all(); // Potentially large, but acceptable for now
        }

        return inertia('Items/Index', [
            'items' => $items,
            'institutions' => $institutions,
            'rooms' => $rooms,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin' || $user->is_super_admin;

        $rules = [
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
            'brand' => 'nullable|string|max:255',
            'condition' => 'nullable|string',
            'note' => 'nullable|string',
            'room_id' => 'nullable', // Removed exists check temporarily or fix empty string handling
        ];

        // If room_id is provided and not empty, check existence
        if ($request->filled('room_id')) {
            $rules['room_id'] = 'exists:rooms,id';
        }

        if ($isAdmin) {
            $rules['institution_id'] = 'required|exists:institutions,id';
        }

        $validated = $request->validate($rules);

        // Ensure room_id is null if empty
        if (!$request->filled('room_id')) {
            $validated['room_id'] = null;
        }

        if (!$isAdmin) {
            $validated['institution_id'] = $user->institution_id;
        }

        Item::create($validated);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin' || $user->is_super_admin;

        // Verify ownership for non-admins
        if (!$isAdmin && $item->institution_id !== $user->institution_id) {
            abort(403);
        }

        // Check if it's a Stock Update Request (simplified check based on payload)
        // If 'reason' is present and 'stock' is present, assume Stock Update Request logic for non-admins?
        // Or if form is "Edit Item", we might have full data.
        // User instruction: "CRUD di sana".
        // I'll allow Full Edit for Admin.
        // For Lembaga: "Update Stok" logic exists. Do they need full edit?
        // I will assume Lembaga can update DETAILS directly, but STOCK via request?
        // Or simplified: Just update everything directly EXCEPT delete?
        // The prompt emphasized DELETE constraint.
        // But the previous code forced STOCK REQUEST for Lembaga.
        // I'll maintain the STOCK REQUEST for Lembaga to be safe/consistent with "Management" aspect.

        if ($isAdmin) {
            $validated = $request->validate([
                'institution_id' => 'sometimes|exists:institutions,id',
                'name' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'unit' => 'nullable|string|max:50',
                'min_stock' => 'nullable|integer|min:0',
                'brand' => 'nullable|string|max:255',
                'condition' => 'nullable|string',
                'note' => 'nullable|string',
                'room_id' => 'nullable|exists:rooms,id',
            ]);

            $item->update($validated);
            return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
        }
        else {
            // Lembaga Logic
            // If they are sending 'reason', it's likely a Stock Request (as per previous design).
            if ($request->has('reason') && $request->has('stock') && count($request->all()) <= 4) { // Heuristic
                $validated = $request->validate([
                    'stock' => 'required|integer|min:0',
                    'reason' => 'required|string|max:500',
                ]);

                ItemUpdateRequest::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'type' => 'update',
                    'old_data' => ['stock' => $item->stock],
                    'new_data' => ['stock' => $validated['stock']],
                    'reason' => $validated['reason'],
                    'status' => 'pending',
                ]);

                return redirect()->back()->with('success', 'Permintaan update stok dikirim ke Admin.');
            }
            else {
                // Assume General Update (no sensitive stock change or allowed direct)
                // For now, I'll allow updating details (Name, Brand, etc) DIRECTLY. 
                // But NOT stock if not via request?
                // Let's implement safe update: 
                // Exclude 'stock' from direct update if not admin? 
                // Or just allow everything per "CRUD" request?
                // I will allow everything to be "CRUD" compatible, but strictly DELETE is gated.
                // Reverting previous "Stock Request" enforcement might be risky if that was a strict rule.
                // However, user said "Ubah sistem... jadi... bisa CRUD". "Edit" is part of CRUD.
                // I'll allow Direct Edit for Lembaga for now to fulfill "CRUD".

                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'stock' => 'required|integer|min:0', // If they change stock here, it's direct?
                    'unit' => 'nullable|string|max:50',
                    'min_stock' => 'nullable|integer|min:0',
                    'brand' => 'nullable|string|max:255',
                    'condition' => 'nullable|string',
                    'note' => 'nullable|string',
                    'room_id' => 'nullable|exists:rooms,id',
                    'reason' => 'nullable|string' // Just in case
                ]);

                // If stock changed, and we want to enforce request? 
                // I'll stick to: Direct Update for simplicity of "CRUD". 
                // ONLY DELETE is explicitly constrained in the prompt.

                $item->update($validated);
                return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Item $item)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin' || $user->is_super_admin;

        // Verify ownership for non-admins
        if (!$isAdmin && $item->institution_id !== $user->institution_id) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($isAdmin) {
            // Admin: Direct Delete (Request by User: "wait approval". But Admin is approver.)
            // I'll do direct delete.
            // Log the reason manually since we are deleting.
            // We can use a temporary request that is auto-approved, or just delete.
            // Direct delete is better UX for Admin.

            // Optional: Create a "History" record of this deletion?
            // ActivityLog handles it but 'reason' might be lost unless in properties.
            activity()
                ->performedOn($item)
                ->causedBy($user)
                ->withProperties(['reason' => $validated['reason']])
                ->log('deleted');

            $item->delete();
            return redirect()->back()->with('success', 'Barang berhasil dihapus (Admin).');
        }
        else {
            // Lembaga: Request Delete
            ItemUpdateRequest::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
                'type' => 'delete',
                'old_data' => $item->toArray(),
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Permintaan penghapusan dikirim ke Admin.');
        }
    }
}
