<?php

namespace App\Imports;

use App\Models\IsoChecklist;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IsoChecklistImport implements ToModel, WithHeadingRow
{
    protected $mapping;

    public function __construct($mapping = [])
    {
        $this->mapping = $mapping;
    }

    private function get($key, $row)
    {
        if (isset($this->mapping[$key]) && !empty($this->mapping[$key])) {
            $slug = \Illuminate\Support\Str::slug($this->mapping[$key], '_');
            return $row[$slug] ?? null;
        }
        return null;
    }

    public function model(array $row)
    {
        if (empty($row))
            return null;

        return new IsoChecklist([
            'title' => $this->get('title', $row) ?? $row['judul'] ?? $row['nama_checklist'] ?? 'Untitled Checklist',
            'category' => $this->get('category', $row) ?? $row['kategori'] ?? 'General',
            'frequency' => $this->get('frequency', $row) ?? $row['frekuensi'] ?? 'Bulanan',
            'items' => [], // Default empty items
            'is_active' => true,
        ]);
    }
}
