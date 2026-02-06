<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemUpdateRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ItemRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ItemRequests/Index', [
            'requests' => ItemUpdateRequest::with(['user.institution', 'item'])->latest()->paginate(10),
            'stats' => [
                'pending' => ItemUpdateRequest::where('status', 'pending')->count(),
                'approved' => ItemUpdateRequest::where('status', 'approved')->count(),
                'rejected' => ItemUpdateRequest::where('status', 'rejected')->count(),
                'total' => ItemUpdateRequest::count(),
            ]
        ]);
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
            }
            else {
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
