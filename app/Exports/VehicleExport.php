<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Vehicle::latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Kendaraan',
            'No. Plat',
            'Tipe',
            'Merk',
            'Tahun',
            'Pajak Berlaku',
            'Status',
            'Catatan'
        ];
    }

    public function map($vehicle): array
    {
        return [
            $vehicle->id,
            $vehicle->name,
            $vehicle->plate_number,
            $vehicle->type,
            $vehicle->brand,
            $vehicle->year,
            $vehicle->tax_expiry,
            $vehicle->status,
            $vehicle->note,
        ];
    }
}
