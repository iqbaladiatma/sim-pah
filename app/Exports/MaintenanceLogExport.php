<?php

namespace App\Exports;

use App\Models\MaintenanceLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaintenanceLogExport implements FromCollection, WithHeadings, WithMapping
{
    protected $type;

    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = MaintenanceLog::with(['institution', 'room', 'performer']);
        if ($this->type) {
            $query->where('type', $this->type);
        }
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tipe',
            'Judul',
            'Deskripsi',
            'Lokasi',
            'Lembaga',
            'Ruangan',
            'Jadwal',
            'Selesai',
            'Biaya',
            'Status',
            'Pelaksana',
            'Dibuat Pada'
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            ucfirst($log->type),
            $log->title,
            $log->description,
            $log->location,
            $log->institution?->name,
            $log->room?->name,
            $log->scheduled_at?->format('d/m/Y H:i'),
            $log->completed_at?->format('d/m/Y H:i'),
            $log->cost,
            $log->status,
            $log->performer?->name,
            $log->created_at->format('d/m/Y H:i'),
        ];
    }
}
