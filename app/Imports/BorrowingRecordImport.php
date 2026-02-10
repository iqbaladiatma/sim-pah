<?php

namespace App\Imports;

use App\Models\BorrowingRecord;
use App\Models\Item;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class BorrowingRecordImport implements ToModel, WithHeadingRow
{
    protected $mapping;

    public function __construct($mapping = [])
    {
        $this->mapping = $mapping;
    }

    private function get($key, $row)
    {
        if (isset($this->mapping[$key]) && !empty($this->mapping[$key])) {
            $slug = Str::slug($this->mapping[$key], '_');
            return $row[$slug] ?? null;
        }
        return null;
    }

    public function model(array $row)
    {
        if (empty($row))
            return null;

        // Lookup Item
        $code = $this->get('item_code', $row) ?? $row['kode_barang'] ?? $row['kode'] ?? null;
        $item = null;
        if ($code) {
            $item = Item::where('code', $code)->first();
        }

        // Lookup User (Borrower)
        $borrowerName = $this->get('borrower_name', $row) ?? $row['peminjam'] ?? $row['nama_peminjam'] ?? null;
        $user = null;
        if ($borrowerName) {
            $user = User::where('name', 'LIKE', "%{$borrowerName}%")->first();
        }

        if (!$item)
            return null; // Skip if item not found

        return new BorrowingRecord([
            'item_id' => $item->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'institution_id' => $item->institution_id,
            'borrow_date' => $this->get('borrow_date', $row) ?? $row['tanggal_pinjam'] ?? now(),
            'return_date' => $this->get('return_date', $row) ?? $row['tanggal_kembali'] ?? null,
            'status' => $this->get('status', $row) ?? 'borrowed',
            'quantity' => $this->get('quantity', $row) ?? 1,
            'notes' => 'Imported via Excel. Borrower: ' . ($borrowerName ?? 'Unknown'),
        ]);
    }
}
