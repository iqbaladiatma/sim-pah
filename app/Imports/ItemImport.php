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
                $cleanKey = strtolower((string) $key);
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
        $condition = $row['keadaan_barang'] ?? $row['keadaan'] ?? $row['kondisi_saat_ini'] ?? $row['kondisi'] ?? $row['condition'] ?? 'B';

        // KIR specialized condition columns
        if (isset($row['baik_b']) && $row['baik_b'] === 'V')
            $condition = 'B';
        if (isset($row['kurang_baik_kb']) && $row['kurang_baik_kb'] === 'V')
            $condition = 'KB';
        if (isset($row['rusak_berat_rb']) && $row['rusak_berat_rb'] === 'V')
            $condition = 'RB';

        return new Item([
            'institution_id' => $institution->id,
            'room_id' => $room->id,
            'name' => $row['jenis_barang_nama_barang'] ?? $row['jenis_barang'] ?? $row['nama_barang'] ?? $row['item_name'] ?? $row['name'] ?? 'Tanpa Nama',
            'code' => $row['no_kode_barang'] ?? $row['kode_barang'] ?? $row['kode'] ?? $row['code'] ?? null,
            'no_urut_satker' => $row['no_urut_satker'] ?? $row['urut_satker'] ?? null,
            'no_urut_pondok' => $row['no_urut_pondok'] ?? $row['urut_pondok'] ?? null,
            'purchased_at' => (string) ($row['tahun_pembuatan_pembelian'] ?? $row['tanggal_pembukuan_pembelian'] ?? $row['tanggal_pengecekan'] ?? $row['tanggal_pembelian'] ?? $row['tanggal'] ?? $row['purchase_date'] ?? $row['purchase_at'] ?? null),
            'received_at' => (string) ($row['tanggal_penyerahan_barang'] ?? $row['tanggal_penyerahan'] ?? $row['received_at'] ?? null),
            'stock' => (int) ($row['jumlah_barang_register'] ?? $row['jumlah_aset'] ?? $row['kuantitas'] ?? $row['stok'] ?? $row['jumlah'] ?? $row['quantity'] ?? $row['stock'] ?? 1),
            'unit' => $row['nama_satuan'] ?? $row['satuan'] ?? $row['unit'] ?? 'Unit',
            'source' => $row['sumber_perolehan_barang'] ?? $row['sumber_perolehan'] ?? $row['sumber'] ?? $row['source'] ?? '-',
            'condition' => $condition,
            'price' => (float) ($row['nilai_aset'] ?? $row['harga'] ?? $row['harga_perolehan'] ?? $row['price'] ?? 0),
            'depreciation_price' => (float) ($row['harga_setelah_penyusutan'] ?? $row['penyusutan'] ?? $row['depreciation_price'] ?? 0),
            'responsible_person' => $row['petugas_pemeriksa'] ?? $row['penanggung_jawab'] ?? $row['pic'] ?? $row['responsible_person'] ?? null,
            'note' => $row['keterangan'] ?? $row['catatan'] ?? $row['note'] ?? null,
            'brand' => $row['merkkode'] ?? $row['merk'] ?? $row['brand'] ?? '-',
            'serial_number' => $row['no_seri_pabrik'] ?? $row['no_seri'] ?? $row['serial_number'] ?? null,
            'size' => $row['ukuran'] ?? $row['size'] ?? null,
            'material' => $row['bahan'] ?? $row['material'] ?? '-',
            'specification' => $row['merk_2'] ?? $row['spesifikasi'] ?? $row['specification'] ?? '-',
            'min_stock' => 0,
            'is_active' => true,
        ]);
    }

    private function transformDate($value)
    {
        if (!$value || trim((string) $value) === '' || $value === '-')
            return null;

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }
            $cleanDate = str_replace(['/', '.'], '-', $value);
            return \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Format Tanggal Error '{$value}': " . $e->getMessage());
            return null;
        }
    }
}
