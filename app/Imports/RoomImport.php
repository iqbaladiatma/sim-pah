<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\Institution;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class RoomImport implements ToModel, WithHeadingRow
{
    /**
     * Import Ruangan
     * Format: [institution_code, name, description]
     */
    public function model(array $row)
    {
        Log::info('ROOM_IMPORT: Memproses Baris', $row);

        $institutionCode = $row['institution_code'] ?? $row['lembaga_kode'] ?? $row['kode_lembaga'] ?? null;
        $roomName = $row['name'] ?? $row['nama_ruangan'] ?? $row['nama'] ?? null;
        $description = $row['description'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? null;

        if (!$institutionCode || !$roomName) {
            Log::warning('ROOM_IMPORT: Lewati baris karena Kode Lembaga atau Nama Ruangan kosong.', $row);
            return null;
        }

        // Cari Lembaga
        $institution = Institution::where('code', $institutionCode)->first();

        if (!$institution) {
            Log::error("ROOM_IMPORT: Lembaga dengan kode '{$institutionCode}' tidak ditemukan. Harap buat Lembaga dulu.");
            return null;
        }

        // Buat atau Update Ruangan (Unique per lembaga + nama)
        $room = Room::updateOrCreate(
        [
            'institution_id' => $institution->id,
            'name' => $roomName
        ],
        [
            'description' => $description,
            'is_active' => true,
        ]
        );

        Log::info("ROOM_IMPORT: Berhasil simpan ruangan '{$roomName}' untuk lembaga '{$institution->code}'");
        return $room;
    }
}
