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
        $model = new MaintenanceLog();
        $fillable = $model->getFillable();
        $data = [];

        // Always ensure type is set
        $data['type'] = $this->type ?? $row['type'] ?? 'maintenance';

        // Helper to get value
        $getValue = function ($key) use ($row) {
            // 1. From Mapping
            if (isset($this->mapping[$key]) && !empty($this->mapping[$key])) {
                $slug = \Illuminate\Support\Str::slug($this->mapping[$key], '_');
                if (isset($row[$slug]))
                    return $row[$slug];
            }
            // 2. Direct key
            if (isset($row[$key]))
                return $row[$key];

            return null;
        };

        foreach ($fillable as $field) {
            if ($field === 'type')
                continue;

            $val = $getValue($field);
            if ($val !== null) {
                // Handle Dates
                if (in_array($field, ['scheduled_at', 'completed_at', 'request_date', 'follow_up_date', 'created_at'])) {
                    try {
                        // Excel dates are sometimes integers (days since 1900), handled by PhpSpreadsheet usually,
                        // but ToModel returns what the reader sees. 
                        // If it's a string, try parsing.
                        if ($val && is_string($val)) {
                            $data[$field] = \Carbon\Carbon::parse($val);
                        }
                        elseif ($val instanceof \DateTime) {
                            $data[$field] = $val;
                        }
                        else {
                            $data[$field] = $val;
                        }
                    }
                    catch (\Exception $e) {
                        $data[$field] = null;
                    }
                }
                else {
                    $data[$field] = $val;
                }
            }
        }

        // Defaults / Fallbacks for critical fields
        if (empty($data['title'])) {
            $data['title'] = $row['judul'] ?? $row['title'] ?? $row['nama'] ?? '-';
        }
        if (empty($data['description'])) {
            $data['description'] = $row['deskripsi'] ?? $row['keterangan'] ?? $row['description'] ?? null;
        }
        if (empty($data['status'])) {
            $data['status'] = $row['status'] ?? 'pending';
        }

        // Specific Defaults based on type
        if ($this->type === 'pemeliharaan-ac' && empty($data['category'])) {
            $data['category'] = 'AC';
        }
        elseif ($this->type === 'pemeliharaan-kamar-mandi' && empty($data['category'])) {
            $data['category'] = 'Kamar Mandi';
        }

        return new MaintenanceLog($data);
    }
}
