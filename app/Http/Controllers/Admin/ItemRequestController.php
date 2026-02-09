<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemUpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ItemRequestController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'pending' => ItemUpdateRequest::where('status', 'pending')->count(),
            'approved' => ItemUpdateRequest::where('status', 'approved')->count(),
            'rejected' => ItemUpdateRequest::where('status', 'rejected')->count(),
            'total' => ItemUpdateRequest::count(),
        ];

        if ($request->has('institution_id') && $request->institution_id) {
            $institution = \App\Models\Institution::findOrFail($request->institution_id);

            $query = ItemUpdateRequest::with(['user.institution', 'item'])
                ->whereHas('user', function ($q) use ($institution) {
                    $q->where('institution_id', $institution->id);
                });

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->whereHas('item', function ($sq) use ($request) {
                        $sq->where('name', 'like', '%' . $request->search . '%');
                    })->orWhereHas('user', function ($sq) use ($request) {
                        $sq->where('name', 'like', '%' . $request->search . '%');
                    });
                });
            }

            return Inertia::render('Admin/ItemRequests/Index', [
                'mode' => 'list',
                'institution' => $institution,
                'requests' => $query->latest()->paginate(10)->withQueryString(),
                'filters' => $request->only(['search', 'institution_id']),
                'stats' => $stats
            ]);
        } else {
            // Folder view: Get institutions with pending request counts
            $institutions = \App\Models\Institution::withCount([
                'itemUpdateRequests' => function ($q) {
                    $q->where('status', 'pending');
                }
            ])->get();

            // Filter institutions by search if provided
            if ($request->search) {
                $institutions = $institutions->filter(function ($inst) use ($request) {
                    return stripos($inst->name, $request->search) !== false ||
                        stripos($inst->code, $request->search) !== false;
                })->values();
            }

            return Inertia::render('Admin/ItemRequests/Index', [
                'mode' => 'folders',
                'institutions' => $institutions,
                'filters' => $request->only(['search']),
                'stats' => $stats
            ]);
        }
    }

    public function approve(ItemUpdateRequest $request)
    {
        if ($request->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request) {
            // Update request status
            $request->update(['status' => 'approved']);

            if ($request->type === 'delete') {
                $request->item()->delete();
            } else {
                // Update item stock
                $item = $request->item;
                $newStock = $request->new_data['stock'] ?? $item->stock;
                $item->update(['stock' => $newStock]);
            }
        });

        $message = $request->type === 'delete' ? 'Penghapusan barang disetujui.' : 'Update stok disetujui & berhasil diubah.';
        return back()->with('success', $message);


    }

    public function reject(ItemUpdateRequest $request)
    {
        if ($request->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Permintaan update stok ditolak.');
    }
}
