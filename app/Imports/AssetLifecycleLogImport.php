<?php

namespace App\Imports;

use App\Models\AssetLifecycleLog;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class AssetLifecycleLogImport implements ToModel, WithHeadingRow
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

        // Lookup Item by ID or Code
        $identifier = $this->get('item_id', $row) ?? $row['kode_barang'] ?? $row['item_id'] ?? null;
        $item = null;
        if ($identifier) {
            $item = Item::where('id', $identifier)->orWhere('code', $identifier)->first();
        }

        if (!$item)
            return null;

        return new AssetLifecycleLog([
            'item_id' => $item->id,
            'reason' => $this->get('reason', $row) ?? $row['alasan'] ?? 'Lelang / Mutasi Aset',
            'approved_by' => $this->get('approved_by', $row) ?? auth()->user()->name,
            'value' => $this->get('value', $row) ?? $row['nilai'] ?? 0,
            'action_date' => $this->get('action_date', $row) ?? $row['tanggal'] ?? now(),
            'type' => 'auction', // Default type for this import context
        ]);
    }
}
