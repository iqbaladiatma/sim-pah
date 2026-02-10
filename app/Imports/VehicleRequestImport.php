<?php

namespace App\Imports;

use App\Models\VehicleRequest;
use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class VehicleRequestImport implements ToModel, WithHeadingRow
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

        // Lookup Vehicle
        $plate = $this->get('vehicle_plate', $row) ?? $row['plat_nomor'] ?? $row['nopol'] ?? null;
        $vehicle = null;
        if ($plate) {
            $vehicle = Vehicle::where('plate_number', 'LIKE', "%{$plate}%")->first();
        }

        return new VehicleRequest([
            'vehicle_id' => $vehicle ? $vehicle->id : null,
            'start_time' => $this->get('start_time', $row) ?? $row['waktu_mulai'] ?? now(),
            'end_time' => $this->get('end_time', $row) ?? $row['waktu_selesai'] ?? now()->addHours(1),
            'destination' => $this->get('destination', $row) ?? $row['tujuan'] ?? 'Dalam Kota',
            'purpose' => $this->get('purpose', $row) ?? $row['keperluan'] ?? 'Operasional',
            'start_mileage' => $this->get('start_mileage', $row) ?? $row['km_awal'] ?? 0,
            'end_mileage' => $this->get('end_mileage', $row) ?? $row['km_akhir'] ?? 0,
            'status' => 'approved', // Default import as approved?
            'user_id' => auth()->id(), // Default to importer?
        ]);
    }
}
