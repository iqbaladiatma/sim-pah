<?php

namespace App\Imports;

use App\Models\Institution;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class InstitutionImport implements ToModel, WithHeadingRow
{
    /**
     * Import Lembaga (Institution)
     * Format yang diharapkan: [code, name]
     */
    public function model(array $row)
    {
        // Debugging LOG
        Log::info('INSTITUTION_IMPORT: Memproses Baris', $row);

        // Mapping Kolom
        // Kita prioritaskan nama kolom yang spesifik untuk Institution (Lembaga)
        $code = $row['code'] ?? $row['kode'] ?? $row['kode_lembaga'] ?? null;
        $name = $row['name'] ?? $row['nama'] ?? $row['nama_lembaga'] ?? null;
        $description = $row['description'] ?? $row['deskripsi'] ?? $row['keterangan'] ?? null;

        // Jika user menggunakan file Ruangan (yang ada 'institution_code')
        // tapi di baris itu 'name' adalah nama ruangan, maka ini akan merusak data Lembaga.
        // Kita coba deteksi jika ada 'institution_code' tapi 'code' kosong.
        if (!$code && isset($row['institution_code'])) {
            $code = $row['institution_code'];
        }

        if (empty($code) || empty($name)) {
            Log::warning('INSTITUTION_IMPORT: Lewati baris karena Kode/Nama kosong.', $row);
            return null;
        }

        // Cek Duplikat
        $existing = Institution::where('code', $code)->first();
        if ($existing) {
            // Jika kode sama, kita update namanya (Ini yang menyebabkan baris banyak jadi 1 jika kodenya sama semua)
            Log::info("INSTITUTION_IMPORT: Update Lembaga yang sudah ada: {$code}");
            $existing->update([
                'name' => $name,
                'description' => $description
            ]);
            return null; // Tidak membuat record baru
        }

        Log::info("INSTITUTION_IMPORT: Berhasil buat Lembaga baru: [{$code}] {$name}");
        return new Institution([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'is_active' => true,
        ]);
    }
}
