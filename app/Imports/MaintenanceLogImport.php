<?php

namespace App\Imports;

use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaintenanceLogImport implements ToModel, WithHeadingRow
{
    protected $type;
    protected $mapping;

    public function __construct($type = null, $mapping = [])
    {
        $this->type = $type;
        $this->mapping = $mapping;
    }

    public function model(array $row)
    {
        // Helper to get value based on mapping
        $get = function ($key, $default = null) use ($row) {
            // 1. Check if mapping exists for this key
            if (isset($this->mapping[$key]) && !empty($this->mapping[$key])) {
                // The mapping value is the original header (e.g. "Judul Kegiatan")
                // WithHeadingRow converts headers to slug (e.g. "judul_kegiatan")
                $slug = \Illuminate\Support\Str::slug($this->mapping[$key], '_');
                return $row[$slug] ?? $default;
            }
            // 2. Fallback to direct key lookup (if mapping not used for this field)
            return $row[$key] ?? $default;
        };

        return new MaintenanceLog([
            'type' => $this->type ?? $get('type', 'maintenance'),
            'title' => $get('judul', $row['judul'] ?? $row['title'] ?? '-'),
            'description' => $get('deskripsi', $row['deskripsi'] ?? $row['description'] ?? '-'),
            'location' => $get('lokasi', $row['lokasi'] ?? $row['location'] ?? null),
            'cost' => $get('biaya', $row['biaya'] ?? $row['cost'] ?? 0),
            'status' => $get('status', 'pending'),
            'scheduled_at' => is_string($val = $get('jadwal', null)) ? \Carbon\Carbon::parse($val) : null,
            // Add other fields as needed
            'category' => $this->type === 'pemeliharaan-ac' ? 'AC' : ($this->type === 'pemeliharaan-kamar-mandi' ? 'Kamar Mandi' : 'Sarpras'), // Auto-set category based on type if possible, or mapping
        ]);
    }
}
