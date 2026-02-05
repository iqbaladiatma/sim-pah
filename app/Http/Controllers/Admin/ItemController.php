<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Institution;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemImport;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['institution', 'room'])->latest()->paginate(50);
        $institutions = Institution::all();

        return inertia('Admin/Items/Index', [
            'items' => $items,
            'institutions' => $institutions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'source' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        Item::create(array_merge($validated, [
            'brand' => '-',
            'unit' => 'pcs',
            'min_stock' => 0
        ]));

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'nullable|date',
            'stock' => 'required|integer|min:0',
            'source' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'responsible_person' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $item->update($validated);
        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file', // More flexible than mimes:csv
        ]);

        try {
            \Illuminate\Support\Facades\Log::info('Starting Item Import...');
            Excel::import(new ItemImport, $request->file('file'));
            \Illuminate\Support\Facades\Log::info('Item Import Finished.');
            return redirect()->back()->with('success', 'Data barang berhasil diimpor.');
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Item Import Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'lembaga_kode', 'ruangan', 'jenis_barang',
            'tanggal_pembukuan_pembelian', 'kuantitas', 'sumber_perolehan_barang',
            'keadaan_barang', 'penanggung_jawab', 'keterangan'
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, [
                'SMA-AH', 'Lab Komputer 1', 'Laptop',
                '2023-10-25', '10', 'Yayasan',
                'Baik', 'Ihsan Adi', 'Hibah dari yayasan pusat'
            ]);
            fclose($file);
        };

        return response()->streamDownload($callback, 'template_inventaris.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
