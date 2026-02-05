<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Institution;
use App\Models\Room;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ItemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Item Import - Memproses baris:', $row);

        if (empty($row))
            return null;

        // 1. Identifikasi Lembaga (Fuzzy matching)
        $institutionVal = $row['lembaga_kode'] ?? $row['institution_code'] ?? $row['lembaga'] ?? $row['nama_lembaga'] ?? null;

        if (!$institutionVal) {
            foreach ($row as $key => $val) {
                $cleanKey = strtolower((string)$key);
                if (str_contains($cleanKey, 'lembaga') || str_contains($cleanKey, 'inst')) {
                    $institutionVal = $val;
                    break;
                }
            }
        }

        if (!$institutionVal) {
            Log::warning('Skip Item Row: Kolom lembaga tidak terdeteksi.', ['row' => $row, 'keys' => array_keys($row)]);
            return null;
        }

        $institution = Institution::where('code', $institutionVal)
            ->orWhere('name', 'LIKE', '%' . $institutionVal . '%')
            ->first();

        if (!$institution) {
            Log::warning("Skip Item Row: Lembaga '{$institutionVal}' tidak ditemukan di database.");
            return null;
        }

        // 2. Identifikasi / Buat Ruangan
        $roomName = $row['ruangan'] ?? $row['room'] ?? $row['nama_ruangan'] ?? $row['lokasi'] ?? 'Utama';
        $room = Room::firstOrCreate(
        ['institution_id' => $institution->id, 'name' => $roomName],
        ['is_active' => true]
        );

        // 3. Mapping data lainnya
        return new Item([
            'institution_id' => $institution->id,
            'room_id' => $room->id,
            'name' => $row['jenis_barang'] ?? $row['nama_barang'] ?? $row['item_name'] ?? $row['name'] ?? 'Tanpa Nama',
            'purchase_date' => $this->transformDate($row['tanggal_pembukuan_pembelian'] ?? $row['tanggal_pembelian'] ?? $row['tanggal'] ?? $row['purchase_date'] ?? null),
            'stock' => (int)($row['kuantitas'] ?? $row['stok'] ?? $row['jumlah'] ?? $row['quantity'] ?? $row['stock'] ?? 0),
            'source' => $row['sumber_perolehan_barang'] ?? $row['sumber_perolehan'] ?? $row['sumber'] ?? $row['source'] ?? '-',
            'condition' => $row['keadaan_barang'] ?? $row['keadaan'] ?? $row['kondisi'] ?? $row['condition'] ?? 'Baik',
            'responsible_person' => $row['penanggung_jawab'] ?? $row['pic'] ?? $row['responsible_person'] ?? null,
            'note' => $row['keterangan'] ?? $row['catatan'] ?? $row['note'] ?? null,

            'brand' => '-',
            'unit' => 'pcs',
            'min_stock' => 0,
            'is_active' => true,
        ]);
    }

    private function transformDate($value)
    {
        if (!$value || trim((string)$value) === '' || $value === '-')
            return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            $cleanDate = str_replace(['/', '.'], '-', $value);
            return \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
        }
        catch (\Exception $e) {
            Log::error("Format Tanggal Error '{$value}': " . $e->getMessage());
            return null;
        }
    }
}
