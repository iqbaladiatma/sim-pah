<?php

namespace App\Imports;

use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaintenanceLogImport implements ToModel, WithHeadingRow
{
    protected $type;

    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function model(array $row)
    {
        return new MaintenanceLog([
            'type' => $this->type ?? ($row['tipe'] ?? 'maintenance'),
            'title' => $row['judul'],
            'description' => $row['deskripsi'],
            'location' => $row['lokasi'],
            'cost' => $row['biaya'] ?? 0,
            'status' => $row['status'] ?? 'pending',
            'scheduled_at' => isset($row['jadwal']) ? \Carbon\Carbon::parse($row['jadwal']) : null,
        ]);
    }
}
