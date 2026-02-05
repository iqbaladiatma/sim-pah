<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemRequestController extends Controller
{
    public function index()
    {
        $requests = \App\Models\ItemUpdateRequest::with(['item', 'user.institution'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return inertia('Admin/ItemRequests/Index', [
            'requests' => $requests
        ]);
    }

    public function approve(\App\Models\ItemUpdateRequest $itemUpdateRequest)
    {
        if ($itemUpdateRequest->status !== 'pending')
            abort(400, 'Request already processed');

        // Update Item Stock
        $item = $itemUpdateRequest->item;
        $item->update(['stock' => $itemUpdateRequest->new_data['stock']]);

        // Update Request Status
        $itemUpdateRequest->update([
            'status' => 'approved',
            'admin_id' => auth()->id(),
            'processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Stok berhasil diupdate.');
    }

    public function reject(\App\Models\ItemUpdateRequest $itemUpdateRequest)
    {
        if ($itemUpdateRequest->status !== 'pending')
            abort(400, 'Request already processed');

        $itemUpdateRequest->update([
            'status' => 'rejected',
            'admin_id' => auth()->id(),
            'processed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Request ditolak.');
    }
}
