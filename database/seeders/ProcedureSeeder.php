<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\MaintenanceLog;
use App\Models\AssetLifecycleLog;
use App\Models\BorrowingRecord;
use App\Models\ParkingLog;
use App\Models\Vehicle;
use App\Models\VehicleRequest;
use App\Models\IsoChecklist;
use App\Models\Institution;
use App\Models\Room;
use App\Models\User;

class ProcedureSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data to avoid confusion during testing
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MaintenanceLog::truncate();
        AssetLifecycleLog::truncate();
        BorrowingRecord::truncate();
        ParkingLog::truncate();
        VehicleRequest::truncate();
        Vehicle::truncate();
        IsoChecklist::truncate();
        Item::truncate();
        Room::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure we have some base data
        $institution = Institution::where('code', 'SD_PA')->first() ?? Institution::first();
        if (!$institution) {
            $institution = Institution::create([
                'name' => 'SD IT Abu Hurairah Putra',
                'code' => 'SD_PA',
                'is_active' => true
            ]);
        }

        // Create Rooms (3 examples)
        $rooms = [
            Room::create(['name' => 'Ruang Kelas 1A', 'institution_id' => $institution->id]),
            Room::create(['name' => 'Ruang Guru', 'institution_id' => $institution->id]),
            Room::create(['name' => 'Laboratorium Komputer', 'institution_id' => $institution->id]),
        ];

        $admin = User::where('role', 'admin')->first()
            ?? User::where('role', 'super admin')->first()
            ?? User::first();

        // ===============================================
        // 1. ITEMS (pendataan-aset, buku-induk, kir-ruangan) - 3 examples each category
        // ===============================================
        $items = [
            // Example 1
            ['name' => 'Laptop ASUS ROG Strix', 'brand' => 'ASUS', 'code' => 'AST-001', 'stock' => 5, 'unit' => 'Unit', 'condition' => 'B', 'room_id' => $rooms[2]->id, 'institution_id' => $institution->id, 'price' => 15000000, 'purchased_at' => now()->subYear()],
            // Example 2
            ['name' => 'AC Split Panasonic 1PK', 'brand' => 'Panasonic', 'code' => 'AST-002', 'stock' => 10, 'unit' => 'Unit', 'condition' => 'B', 'room_id' => $rooms[0]->id, 'institution_id' => $institution->id, 'price' => 4500000, 'purchased_at' => now()->subMonths(6)],
            // Example 3
            ['name' => 'Meja Siswa Kayu Jati', 'brand' => 'Lokal', 'code' => 'AST-003', 'stock' => 50, 'unit' => 'Unit', 'condition' => 'B', 'room_id' => $rooms[0]->id, 'institution_id' => $institution->id, 'price' => 350000, 'purchased_at' => now()->subYears(2)],
        ];
        foreach ($items as $itemData) {
            Item::create($itemData);
        }
        $firstItem = Item::first();

        // ===============================================
        // 2. VEHICLES (registrasi-kendaraan) - 3 examples
        // ===============================================
        $vehicles = [
            ['name' => 'Toyota Hiace Commuter', 'plate_number' => 'DR 1234 AB', 'type' => 'Minibus', 'brand' => 'Toyota', 'year' => 2022, 'status' => 'available'],
            ['name' => 'Honda Vario 160', 'plate_number' => 'DR 5678 CD', 'type' => 'Sepeda Motor', 'brand' => 'Honda', 'year' => 2023, 'status' => 'available'],
            ['name' => 'Isuzu Elf NLR', 'plate_number' => 'DR 9012 EF', 'type' => 'Truk', 'brand' => 'Isuzu', 'year' => 2021, 'status' => 'available'],
        ];
        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }
        $firstVehicle = Vehicle::first();

        // ===============================================
        // 3. MAINTENANCE LOGS - Based on UrtProcedureController $procedures
        // ===============================================

        // --- GROUP ASET ---

        // monitoring-aset (type: maintenance, category: Aset) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Aset', 'title' => 'Pengecekan Rutin Laptop Inventaris', 'description' => 'Pengecekan performa dan fisik laptop.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Aset', 'title' => 'Update Software Lab Komputer', 'description' => 'Maintenance berkala sistem operasi.', 'status' => 'Dalam Proses', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Aset', 'title' => 'Inventarisasi AC Ruangan', 'description' => 'Pencatatan kondisi AC seluruh gedung.', 'status' => 'Pending', 'institution_id' => $institution->id]);

        // berita-acara-pemeriksaan (type: maintenance, category: Pemeriksaan) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pemeriksaan', 'title' => 'Pemeriksaan Tahunan Gedung Utara', 'description' => 'Audit kelayakan struktur bangunan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pemeriksaan', 'title' => 'Pemeriksaan Instalasi Listrik Masjid', 'description' => 'Pengecekan panel dan kabel utama.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pemeriksaan', 'title' => 'Inspeksi Keamanan Pagar Keliling', 'description' => 'Pemeriksaan struktur dan kekokohan pagar.', 'status' => 'Pending', 'institution_id' => $institution->id]);

        // --- GROUP SARPRAS ---

        // pemeliharaan-gedung (type: maintenance, category: Gedung) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Gedung', 'title' => 'Pengecatan Ulang Koridor Lantai 1', 'description' => 'Perbaikan warna dinding yang kusam.', 'status' => 'Selesai', 'cost' => 1500000, 'institution_id' => $institution->id, 'room_id' => $rooms[0]->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Gedung', 'title' => 'Perbaikan Atap Bocor Ruang Guru', 'description' => 'Penggantian genteng yang pecah.', 'status' => 'Dalam Proses', 'cost' => 2500000, 'institution_id' => $institution->id, 'room_id' => $rooms[1]->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Gedung', 'title' => 'Renovasi Lantai Keramik Aula', 'description' => 'Penggantian keramik yang retak.', 'status' => 'Dijadwalkan', 'cost' => 5000000, 'institution_id' => $institution->id]);

        // pemeliharaan-kamar-mandi (type: maintenance, category: Kamar Mandi) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kamar Mandi', 'title' => 'Pengecekan Kebersihan KM Putra', 'description' => 'Pemeriksaan rutin.', 'kran_air' => 'Baik', 'lampu' => 'Baik', 'closet' => 'Baik', 'pintu' => 'Baik', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kamar Mandi', 'title' => 'Pengurasan Bak Mandi Asrama Putri', 'description' => 'Pembersihan lumut dan kerak air.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kamar Mandi', 'title' => 'Perbaikan Kran Bocor KM Guru', 'description' => 'Penggantian seal kran.', 'kran_air' => 'Perlu Ganti', 'status' => 'Dalam Proses', 'institution_id' => $institution->id]);

        // pemeliharaan-ac (type: maintenance, category: AC) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'AC', 'title' => 'Cuci AC Ruang Kantor TU', 'description' => 'Service berkala.', 'ac_indoor_pc' => 'Bersih', 'ac_outdoor_freon' => 'Penuh', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'AC', 'title' => 'Tambah Freon AC Lab Komputer', 'description' => 'AC kurang dingin.', 'ac_outdoor_freon' => 'Kurang', 'status' => 'Selesai', 'cost' => 350000, 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'AC', 'title' => 'Ganti Kapasitor AC Ruang Kepala', 'description' => 'Kapasitor mati.', 'status' => 'Dalam Proses', 'cost' => 250000, 'institution_id' => $institution->id]);

        // pemeliharaan-pompa (type: maintenance, category: Pompa) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pompa', 'title' => 'Ceklist Bulanan Pompa Air Utama', 'description' => 'Pengecekan rutin pompa.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pompa', 'title' => 'Pelumasan Bearing Pompa Gedung B', 'description' => 'Maintenance preventive.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Pompa', 'title' => 'Penggantian Impeller Pompa Cadangan', 'description' => 'Impeller aus.', 'status' => 'Pending', 'cost' => 750000, 'institution_id' => $institution->id]);

        // pemeliharaan-air-bersih (type: maintenance, category: Air Bersih) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Bersih', 'title' => 'Pembersihan Tandon Utama 5000L', 'description' => 'Pengurasan dan sikat tandon.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Bersih', 'title' => 'Klorinisasi Jalur Pipa Gedung B', 'description' => 'Sterilisasi pipa.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Bersih', 'title' => 'Cek Kualitas Air Sumur Bor', 'description' => 'Test lab PH dan mineral.', 'status' => 'Pending', 'institution_id' => $institution->id]);

        // pemeliharaan-air-minum (type: maintenance, category: Air Minum) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Minum', 'title' => 'Penggantian Filter RO Dispenser', 'description' => 'Filter sudah 6 bulan.', 'status' => 'Selesai', 'cost' => 450000, 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Minum', 'title' => 'Sterilisasi Galon Air Minum', 'description' => 'Cuci galon mingguan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Air Minum', 'title' => 'Cek TDS Air Minum Kantin', 'description' => 'Test kelayakan air minum.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // pemeliharaan-genset (type: maintenance, category: Genset) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Genset', 'title' => 'Pemanasan Rutin Genset Utama', 'description' => 'Running 15 menit tanpa beban.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Genset', 'title' => 'Ganti Oli Genset Tahunan', 'description' => 'Oli sudah 500 jam operasi.', 'status' => 'Selesai', 'cost' => 800000, 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Genset', 'title' => 'Service Aki Genset', 'description' => 'Aki lemah, perlu charging.', 'status' => 'Dalam Proses', 'institution_id' => $institution->id]);

        // pemeliharaan-kipas (type: maintenance, category: Kipas Angin) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kipas Angin', 'title' => 'Cuci Kipas Angin Kelas 1A', 'description' => 'Pembersihan debu.', 'fan_type' => 'Dinding', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kipas Angin', 'title' => 'Pelumasan Dinamo Kipas Kantor', 'description' => 'Bunyi berisik.', 'fan_type' => 'Berdiri', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Kipas Angin', 'title' => 'Ganti Kapasitor Kipas Masjid', 'description' => 'Kipas tidak mau muter.', 'fan_type' => 'Plafon', 'status' => 'Dalam Proses', 'cost' => 50000, 'institution_id' => $institution->id]);

        // pemeliharaan-septik (type: maintenance, category: Septik Tank) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Septik Tank', 'title' => 'Pengecekan Kapasitas Septik Tank A', 'description' => 'Cek level isi.', 'st_baik' => true, 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Septik Tank', 'title' => 'Pemberian Bakteri Pengurai', 'description' => 'Enzyme bulanan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Septik Tank', 'title' => 'Sedot Septik Tank Asrama', 'description' => 'Sudah penuh.', 'st_penuh' => true, 'status' => 'Dijadwalkan', 'cost' => 1500000, 'institution_id' => $institution->id]);

        // pemeliharaan-sarpras (type: maintenance, category: Sarpras) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Sarpras', 'title' => 'Laporan Kerusakan Meja Siswa Kelas 2', 'description' => '5 meja rusak di bagian kaki.', 'status' => 'Pending', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Sarpras', 'title' => 'Pengecekan Kelengkapan UKS Pusat', 'description' => 'Audit inventaris UKS.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Sarpras', 'title' => 'Perbaikan Kursi Rapat Rusak', 'description' => '3 kursi sandaran patah.', 'status' => 'Dalam Proses', 'institution_id' => $institution->id]);

        // pemeliharaan-listrik (type: electrical-maintenance, category: Listrik) - 3 examples
        MaintenanceLog::create(['type' => 'electrical-maintenance', 'category' => 'Listrik', 'title' => 'Cek Termografi Panel Utama Lab', 'description' => 'Memastikan tidak ada panas berlebih.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'electrical-maintenance', 'category' => 'Listrik', 'title' => 'Penggantian MCB Musholla', 'description' => 'MCB trip terus.', 'status' => 'Selesai', 'cost' => 150000, 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'electrical-maintenance', 'category' => 'Listrik', 'title' => 'Instalasi Stop Kontak Tambahan', 'description' => 'Ruang guru butuh outlet baru.', 'status' => 'Pending', 'institution_id' => $institution->id]);

        // --- GROUP PROYEK ---

        // agenda-perbaikan (type: repair) - 3 examples
        MaintenanceLog::create(['type' => 'repair', 'title' => 'Perbaikan Lampu Taman Depan', 'description' => '3 lampu mati.', 'location' => 'Halaman Depan', 'scheduled_at' => now()->addDays(2)->format('Y-m-d'), 'status' => 'Dijadwalkan', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'repair', 'title' => 'Servis Printer TU Utama', 'description' => 'Paper jam terus.', 'location' => 'Kantor TU', 'scheduled_at' => now()->addDay()->format('Y-m-d'), 'status' => 'Dijadwalkan', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'repair', 'title' => 'Perbaikan Pintu Gerbang Otomatis', 'description' => 'Motor penggerak lemah.', 'location' => 'Gerbang Utama', 'scheduled_at' => now()->addDays(3)->format('Y-m-d'), 'status' => 'Dijadwalkan', 'institution_id' => $institution->id]);

        // pengajuan-rab & laporan-proyek (type: project) - 3 examples
        MaintenanceLog::create(['type' => 'project', 'title' => 'Pembangunan Shelter Parkir Karyawan', 'description' => 'Atap parkir motor.', 'budget_amount' => 15000000, 'status' => 'Diusulkan', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'project', 'title' => 'Renovasi Kantin Pusat Tahap 1', 'description' => 'Pelebaran area makan.', 'budget_amount' => 25000000, 'status' => 'Dalam Review', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'project', 'title' => 'Pemasangan CCTV Tambahan', 'description' => '10 titik baru.', 'budget_amount' => 8000000, 'status' => 'Disetujui', 'institution_id' => $institution->id]);

        // --- GROUP LOGISTIK ---

        // pengadaan-sarpras (type: procurement) - 3 examples
        MaintenanceLog::create(['type' => 'procurement', 'title' => 'Pengadaan Kursi Lipat 50 Unit', 'description' => 'Untuk Masjid.', 'cost' => 7500000, 'status' => 'Proses Order', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'procurement', 'title' => 'Pengadaan Proyektor Lab Komputer', 'description' => 'Proyektor 4000 lumens.', 'cost' => 5000000, 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'procurement', 'title' => 'Pengadaan Dispenser Air 5 Unit', 'description' => 'Untuk ruang kelas baru.', 'cost' => 2500000, 'status' => 'Pending', 'institution_id' => $institution->id]);

        // analisis-kebutuhan (type: analysis) - 3 examples
        MaintenanceLog::create(['type' => 'analysis', 'title' => 'Analisis Kebutuhan Meja Baru TA 2026', 'description' => 'Evaluasi jumlah siswa vs kapasitas meja.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'analysis', 'title' => 'Studi Kelayakan AC Ruang Guru', 'description' => 'Analisis BTU per meter persegi.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'analysis', 'title' => 'Kebutuhan Komputer Lab Baru', 'description' => 'Rencana penambahan 20 PC.', 'status' => 'Dalam Review', 'institution_id' => $institution->id]);

        // pemilihan-evaluasi (type: vendor-evaluation) - 3 examples
        MaintenanceLog::create(['type' => 'vendor-evaluation', 'title' => 'Evaluasi Supplier ATK - CV Jaya', 'description' => 'Penilaian kinerja vendor.', 'supplier_product' => 'Kertas & Tinta', 'sc_price' => 4, 'sc_quality' => 5, 'sc_delivery' => 4, 'sc_total' => 13, 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'vendor-evaluation', 'title' => 'Pemilihan Vendor Renovasi Atap', 'description' => 'Tender 3 kontraktor.', 'status' => 'Dalam Proses', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'vendor-evaluation', 'title' => 'Evaluasi Catering - RM Padang Jaya', 'description' => 'Review menu dan harga.', 'supplier_product' => 'Catering Harian', 'sc_quality' => 4, 'sc_price' => 3, 'sc_total' => 7, 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // penerimaan-barang (type: receiving) - 3 examples
        MaintenanceLog::create(['type' => 'receiving', 'title' => 'Penerimaan Kursi Guru 10 Unit', 'description' => 'Sesuai PO-001.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'receiving', 'title' => 'Penerimaan Buku Pelajaran Semester 2', 'description' => '500 buku diterima.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'receiving', 'title' => 'Penerimaan Alat Kebersihan', 'description' => 'Sapu, pel, ember.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // penyerahan-barang (type: handover) - 3 examples
        MaintenanceLog::create(['type' => 'handover', 'title' => 'Serah Terima Kunci Lab Bahasa', 'description' => 'Kepada Guru Bahasa Inggris.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'handover', 'title' => 'Penyerahan Laptop Inventaris', 'description' => 'Untuk guru baru.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'handover', 'title' => 'Serah Terima Proyektor Portable', 'description' => 'Peminjaman 1 minggu.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // jadwal-token (type: maintenance, category: Token) - 3 examples
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Token', 'title' => 'Pembelian Token Gedung A', 'description' => '500 kWh.', 'cost' => 1000000, 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Token', 'title' => 'Monitor Sisa kWh Masjid', 'description' => 'Tinggal 50 kWh.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'maintenance', 'category' => 'Token', 'title' => 'Pengisian Token Asrama Putra', 'description' => '300 kWh.', 'cost' => 600000, 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // --- GROUP KEBERSIHAN ---

        // timeline-kebersihan & laporan-kebersihan (type: cleaning) - 3 examples
        MaintenanceLog::create(['type' => 'cleaning', 'title' => 'Pembersihan Kaca Jendela Lantai 1', 'description' => 'Seluruh gedung utama.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'cleaning', 'title' => 'Fogging Area Asrama Putra', 'description' => 'Pencegahan nyamuk DBD.', 'status' => 'Dijadwalkan', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'cleaning', 'title' => 'Deep Cleaning Aula Utama', 'description' => 'Persiapan acara wisuda.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // jadwal-kebersihan (type: detailed-monitoring) - 3 examples
        MaintenanceLog::create(['type' => 'detailed-monitoring', 'title' => 'Monitoring Harian Masjid', 'description' => 'Cek kebersihan sebelum sholat.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'detailed-monitoring', 'title' => 'Cek Harian Toilet Gedung Belakang', 'description' => 'Jam 07.00 dan 14.00.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'detailed-monitoring', 'title' => 'Inspeksi Kebersihan Kantin', 'description' => 'Cek area dapur dan meja.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // kelengkapan-alat (type: inventory-check) - 3 examples
        MaintenanceLog::create(['type' => 'inventory-check', 'title' => 'Cek Stok Sapu & Alat Pel', 'description' => 'Audit bulanan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'inventory-check', 'title' => 'Audit Bahan Pembersih', 'description' => 'Sabun, karbol, pewangi.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'inventory-check', 'title' => 'Cek Stok Kantong Sampah', 'description' => 'Persediaan 2 bulan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // pemeliharaan-kebersihan (type: periodic) - 3 examples
        MaintenanceLog::create(['type' => 'periodic', 'title' => 'Pembersihan Kaca Mingguan Kantor', 'description' => 'Setiap Senin pagi.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'periodic', 'title' => 'Poles Lantai Aula Utama', 'description' => 'Setiap 2 minggu.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'periodic', 'title' => 'Cuci Karpet Musholla', 'description' => 'Setiap Jumat.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // monitoring-kebersihan (type: monitoring) - 3 examples
        MaintenanceLog::create(['type' => 'monitoring', 'title' => 'Monitoring Toilet Putri SMA', 'description' => 'Cek sabun dan tisu.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'monitoring', 'title' => 'Monitoring Taman Halaman Belakang', 'description' => 'Rumput dan sampah.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'monitoring', 'title' => 'Cek Area Dapur Kantin', 'description' => 'Kebersihan peralatan masak.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // kegiatan-pekanan (type: weekly-activity) - 3 examples
        MaintenanceLog::create(['type' => 'weekly-activity', 'title' => 'Laporan Pekanan Tim CS Pekan 1', 'description' => 'Rangkuman kegiatan cleaning service.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'weekly-activity', 'title' => 'Evaluasi Kinerja Kebersihan', 'description' => 'Meeting mingguan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);
        MaintenanceLog::create(['type' => 'weekly-activity', 'title' => 'Jadwal Piket Kebersihan Staff', 'description' => 'Rotasi area per pekan.', 'status' => 'Selesai', 'institution_id' => $institution->id]);

        // ===============================================
        // 4. ASSET LIFECYCLE LOGS (pelelangan-aset) - 3 examples
        // ===============================================
        AssetLifecycleLog::create(['item_id' => $firstItem->id, 'quantity' => 2, 'type' => 'auction', 'action_date' => now(), 'value' => 5000000, 'reason' => 'Penyusutan dan upgrade sistem.']);
        AssetLifecycleLog::create(['item_id' => $firstItem->id, 'quantity' => 1, 'type' => 'disposal', 'action_date' => now(), 'value' => 0, 'reason' => 'Kerusakan berat tidak ekonomis diperbaiki.']);
        AssetLifecycleLog::create(['item_id' => $firstItem->id, 'quantity' => 3, 'type' => 'auction', 'action_date' => now()->subMonth(), 'value' => 1500000, 'reason' => 'Pergantian model baru.']);

        // ===============================================
        // 5. BORROWING RECORDS (peminjaman-barang) - 3 examples
        // ===============================================
        BorrowingRecord::create(['item_id' => $firstItem->id, 'user_id' => $admin->id, 'institution_id' => $institution->id, 'quantity' => 1, 'borrow_date' => now(), 'expected_return_date' => now()->addDays(3), 'status' => 'borrowed']);
        BorrowingRecord::create(['item_id' => $firstItem->id, 'user_id' => $admin->id, 'institution_id' => $institution->id, 'quantity' => 2, 'borrow_date' => now()->subDays(7), 'actual_return_date' => now()->subDay(), 'status' => 'returned']);
        BorrowingRecord::create(['item_id' => $firstItem->id, 'user_id' => $admin->id, 'institution_id' => $institution->id, 'quantity' => 1, 'borrow_date' => now()->subDays(3), 'expected_return_date' => now()->addDays(4), 'status' => 'borrowed']);

        // ===============================================
        // 6. PARKING LOGS (parkir-area) - 3 examples
        // ===============================================
        ParkingLog::create(['vehicle_type' => 'Mobil', 'plate_number' => 'DR 1000 AA', 'owner_name' => 'Bpk. Ahmad', 'entry_time' => now()->subHours(2), 'gate_name' => 'Gerbang Utama']);
        ParkingLog::create(['vehicle_type' => 'Motor', 'plate_number' => 'DR 2000 BB', 'owner_name' => 'Ibu Siti', 'entry_time' => now()->subHours(4), 'exit_time' => now()->subHour(), 'gate_name' => 'Gerbang Samping']);
        ParkingLog::create(['vehicle_type' => 'Mobil', 'plate_number' => 'DR 3000 CC', 'owner_name' => 'Bpk. Ridwan', 'entry_time' => now()->subHour(), 'gate_name' => 'Gerbang Utama']);

        // ===============================================
        // 7. VEHICLE REQUESTS (penggunaan-kendaraan) - 3 examples
        // ===============================================
        VehicleRequest::create(['user_id' => $admin->id, 'vehicle_id' => $firstVehicle->id, 'destination' => 'Bandara LOP Praya', 'purpose' => 'Antar Tamu Delegasi Yayasan', 'start_time' => now()->addHours(1), 'end_time' => now()->addHours(4), 'status' => 'approved', 'request_date' => now()]);
        VehicleRequest::create(['user_id' => $admin->id, 'vehicle_id' => $firstVehicle->id, 'destination' => 'Mataram Mall', 'purpose' => 'Belanja ATK TU', 'start_time' => now()->subDays(1), 'end_time' => now()->subDays(1)->addHours(2), 'status' => 'completed', 'request_date' => now()->subDays(1)]);
        VehicleRequest::create(['user_id' => $admin->id, 'vehicle_id' => $firstVehicle->id, 'destination' => 'Kantor Dinas Pendidikan', 'purpose' => 'Pengambilan SK Akreditasi', 'start_time' => now()->addDays(2), 'status' => 'pending', 'request_date' => now()]);

        // ===============================================
        // 8. ISO CHECKLISTS (ceklist-iso) - 3 examples
        // ===============================================
        IsoChecklist::create([
            'title' => 'Audit Kebersihan Kelas Pekanan',
            'category' => 'Kebersihan',
            'frequency' => 'weekly',
            'items' => [
                ['task' => 'Lantai disapu dan dipel bersih', 'weight' => 25],
                ['task' => 'Papan tulis dan spidol rapi', 'weight' => 25],
                ['task' => 'Meja kursi tersusun sesuai layout', 'weight' => 25],
                ['task' => 'Tempat sampah dikosongkan', 'weight' => 25],
            ],
            'is_active' => true,
        ]);
        IsoChecklist::create([
            'title' => 'Audit Kelengkapan APAR',
            'category' => 'Safety',
            'frequency' => 'monthly',
            'items' => [
                ['task' => 'Tekanan jarum indikator normal', 'weight' => 50],
                ['task' => 'Pin pengunci segel utuh', 'weight' => 30],
                ['task' => 'Masa berlaku isi belum lewat', 'weight' => 20],
            ],
            'is_active' => true,
        ]);
        IsoChecklist::create([
            'title' => 'Ceklist Harian Satpam',
            'category' => 'Keamanan',
            'frequency' => 'daily',
            'items' => [
                ['task' => 'Pintu gerbang terkunci jam 22.00', 'weight' => 40],
                ['task' => 'Patroli gedung 3x sehari', 'weight' => 30],
                ['task' => 'Log buku tamu terisi lengkap', 'weight' => 30],
            ],
            'is_active' => true,
        ]);
    }
}
