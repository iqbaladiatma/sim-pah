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
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ensure we have some base data
        $institution = Institution::where('code', 'SD_PA')->first() ?? Institution::create([
            'name' => 'SD IT Abu Hurairah Putra',
            'code' => 'SD_PA',
            'is_active' => true
        ]);

        $room = Room::first() ?? Room::create([
            'name' => 'Ruang Kelas 1A',
            'code' => 'RK1A',
            'institution_id' => $institution->id
        ]);

        $admin = User::where('role', 'admin')->first() ?? User::where('role', 'super admin')->first() ?? User::first();

        // 1. ITEMS (Aset: pendataan-aset, buku-induk, kir-ruangan)
        $items = [
            [
                'name' => 'Laptop ASUS ROG',
                'brand' => 'ASUS',
                'code' => 'AST-001',
                'stock' => 5,
                'unit' => 'Unit',
                'condition' => 'B',
                'room_id' => $room->id,
                'institution_id' => $institution->id,
                'price' => 15000000,
                'purchased_at' => now()->subYear(),
            ],
            [
                'name' => 'AC Split Panasonic 1PK',
                'brand' => 'Panasonic',
                'code' => 'AST-002',
                'stock' => 10,
                'unit' => 'Unit',
                'condition' => 'B',
                'room_id' => $room->id,
                'institution_id' => $institution->id,
                'price' => 4500000,
                'purchased_at' => now()->subMonths(6),
            ]
        ];
        foreach ($items as $itemData) {
            Item::create($itemData);
        }
        $firstItem = Item::first();

        // 2. VEHICLES (registrasi-kendaraan)
        $vehicles = [
            [
                'name' => 'Toyota Hiace',
                'plate_number' => 'DR 1234 AB',
                'type' => 'Minibus',
                'brand' => 'Toyota',
                'year' => 2022,
                'status' => 'available',
            ],
            [
                'name' => 'Honda Vario 160',
                'plate_number' => 'DR 5678 CD',
                'type' => 'Sepeda Motor',
                'brand' => 'Honda',
                'year' => 2023,
                'status' => 'available',
            ]
        ];
        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }
        $firstVehicle = Vehicle::first();

        // 3. MAINTENANCE LOGS (Most Procedures)
        $logs = [
            // monitoring-aset (Aset group)
            [
                'type' => 'maintenance',
                'category' => 'Aset',
                'item_id' => $firstItem->id,
                'title' => 'Pengecekan Rutin Laptop',
                'description' => 'Pengecekan performa dan fisik laptop inventaris.',
                'status' => 'completed',
                'completed_at' => now(),
                'performed_by' => $admin->id,
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Aset',
                'item_id' => $firstItem->id,
                'title' => 'Update Software Lab Komputer',
                'description' => 'Maintenance berkala sistem operasi dan aplikasi.',
                'status' => 'pending',
                'institution_id' => $institution->id,
            ],

            // berita-acara-pemeriksaan
            [
                'type' => 'maintenance',
                'category' => 'Pemeriksaan',
                'title' => 'Pemeriksaan Tahunan Gedung Utara',
                'description' => 'Audit kelayakan struktur bangunan.',
                'status' => 'completed',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Pemeriksaan',
                'title' => 'Pemeriksaan Instalasi Listrik Masjid',
                'description' => 'Pengecekan panel dan kabel utama.',
                'status' => 'pending',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-gedung
            [
                'type' => 'maintenance',
                'category' => 'Gedung',
                'title' => 'Pengecatan Ulang Koridor',
                'description' => 'Perbaikan warna dinding yang kusam.',
                'status' => 'Selesai',
                'cost' => 1500000,
                'institution_id' => $institution->id,
                'room_id' => $room->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Gedung',
                'title' => 'Perbaikan Atap Bocor',
                'description' => 'Penggantian genteng yang pecah di R. Guru.',
                'status' => 'Dalam Proses',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-kamar-mandi
            [
                'type' => 'maintenance',
                'category' => 'Kamar Mandi',
                'title' => 'Pengecekan Kebersihan KM Putra',
                'kran_air' => 'Baik',
                'lampu' => 'Baik',
                'closet' => 'Baik',
                'pintu' => 'Baik',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Kamar Mandi',
                'title' => 'Pengurasan Bak Mandi Asrama',
                'description' => 'Pembersihan lumut dan kerak air.',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-ac
            [
                'type' => 'maintenance',
                'category' => 'AC',
                'title' => 'Cuci AC Ruang Kantor',
                'ac_indoor_pc' => 'Bersih',
                'ac_outdoor_freon' => 'Penuh',
                'status' => 'Completed',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'AC',
                'title' => 'Tambah Freon AC Lab',
                'description' => 'AC kurang dingin karena kebocoran halus.',
                'status' => 'Pending',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-pompa (USING 'V' for fixed char(2) fields)
            [
                'type' => 'maintenance',
                'category' => 'Pompa',
                'title' => 'Ceklist Bulanan Pompa Air Utama',
                'jan_putra' => 'V',
                'feb_putra' => 'V',
                'mar_putra' => 'V',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Pompa',
                'title' => 'Pelumasan Bearing Pompa 1',
                'jan_putri' => 'V',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-air-bersih
            [
                'type' => 'maintenance',
                'category' => 'Air Bersih',
                'title' => 'Pembersihan Tandon Utama',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Air Bersih',
                'title' => 'Klorinisasi Jalur Pipa Gedung B',
                'status' => 'Pending',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-air-minum
            [
                'type' => 'maintenance',
                'category' => 'Air Minum',
                'title' => 'Penggantian Filter RO',
                'status' => 'Selesai',
                'cost' => 450000,
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Air Minum',
                'title' => 'Cek Kualitas Air PH Lab Kesehatan',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-genset
            [
                'type' => 'maintenance',
                'category' => 'Genset',
                'title' => 'Pemanasan Rutin Genset Utama',
                'description' => 'Dijalankan selama 15 menit tanpa beban.',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Genset',
                'title' => 'Penggantian Oli & Filter Tahunan',
                'status' => 'Pending',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-kipas
            [
                'type' => 'maintenance',
                'category' => 'Kipas Angin',
                'title' => 'Cuci Kipas Angin Kelas 1A',
                'fan_type' => 'Dinding',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Kipas Angin',
                'title' => 'Pemberian Pelumas Dinamo Kipas Kantor',
                'fan_type' => 'Berdiri',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-septik
            [
                'type' => 'maintenance',
                'category' => 'Septik Tank',
                'title' => 'Pengecekan Kapasitas Septik Tank A',
                'st_baik' => true,
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Septik Tank',
                'title' => 'Pemberian Bakteri Pengurai Berkala',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-sarpras
            [
                'type' => 'maintenance',
                'category' => 'Sarpras',
                'title' => 'Laporan Kerusakan Meja Siswa Kls 2',
                'description' => '5 meja di kls 2 rusak di bagian kaki.',
                'status' => 'Pending',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Sarpras',
                'title' => 'Pengecekan Kelengkapan UKS Pusat',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-listrik
            [
                'type' => 'electrical-maintenance',
                'category' => 'Listrik',
                'title' => 'Cek Termografi Panel Utama Lab',
                'description' => 'Memastikan tidak ada panas berlebih di sambungan.',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'electrical-maintenance',
                'category' => 'Listrik',
                'title' => 'Penggantian MCB yang Lemah di Musholla',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // agenda-perbaikan & rekapan-pengajuan
            [
                'type' => 'repair',
                'title' => 'Perbaikan Lampu Taman Depan',
                'location' => 'Halaman Depan',
                'scheduled_at' => now()->addDays(2),
                'status' => 'Dijadwalkan',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'repair',
                'title' => 'Servis Printer TU Utama',
                'location' => 'Kantor TU',
                'scheduled_at' => now()->addDay(),
                'status' => 'Dijadwalkan',
                'institution_id' => $institution->id,
            ],

            // pengajuan-rab & laporan-proyek
            [
                'type' => 'project',
                'title' => 'Pembangunan Shelter Parkir Karyawan',
                'budget_amount' => 15000000,
                'status' => 'Diusulkan',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'project',
                'title' => 'Renovasi Kantin Pusat Tahap 1',
                'budget_amount' => 25000000,
                'status' => 'Dalam Review',
                'institution_id' => $institution->id,
            ],

            // pengadaan-sarpras
            [
                'type' => 'procurement',
                'title' => 'Pengadaan Kursi Lipat 50 Unit Masjid',
                'cost' => 7500000,
                'status' => 'Proses Order',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'procurement',
                'title' => 'Pengadaan Proyektor Baru Lab Komputer',
                'cost' => 5000000,
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // analisis-kebutuhan
            [
                'type' => 'analysis',
                'title' => 'Analisis Kebutuhan Meja Baru TA 2026',
                'description' => 'Evaluasi jumlah siswa berbanding kapasitas meja.',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'analysis',
                'title' => 'Studi Kelayakan AC Ruang Guru',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemilihan-evaluasi
            [
                'type' => 'vendor-evaluation',
                'title' => 'Evaluasi Supplier ATK - CV Jaya',
                'supplier_product' => 'Kertas & Tinta',
                'sc_price' => 4,
                'sc_quality' => 5,
                'sc_delivery' => 4,
                'sc_total' => 13,
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'vendor-evaluation',
                'title' => 'Pemilihan Vendor Renovasi Atap',
                'status' => 'Dalam Proses',
                'institution_id' => $institution->id,
            ],

            // penerimaan-barang
            [
                'type' => 'receiving',
                'title' => 'Penerimaan Kursi Guru - 10 Unit',
                'description' => 'Datang sesuai pesanan nomor PO-001.',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'receiving',
                'title' => 'Penerimaan Buku Induk Inventaris',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // penyerahan-barang
            [
                'type' => 'handover',
                'title' => 'Serah Terima Kunci Lab Bahasa',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'handover',
                'title' => 'Penyerahan Laptop Inventaris ke Guru Baru',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // jadwal-token (Token category)
            [
                'type' => 'maintenance',
                'category' => 'Token',
                'title' => 'Pembelian Token Gedung A',
                'cost' => 1000000,
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'maintenance',
                'category' => 'Token',
                'title' => 'Monitor Sisa KWH Masjid Agung',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // timeline-kebersihan, laporan-kebersihan
            [
                'type' => 'cleaning',
                'title' => 'Pembersihan Kaca Jendela Lantai 1',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'cleaning',
                'title' => 'Fogging Area Asrama Putra',
                'status' => 'Dijadwalkan',
                'institution_id' => $institution->id,
            ],

            // jadwal-kebersihan
            [
                'type' => 'detailed-monitoring',
                'title' => 'Monitoring Harian Masjid - Kebersihan',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'detailed-monitoring',
                'title' => 'Cek Harian Toilet Gedung Belakang',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // kelengkapan-alat
            [
                'type' => 'inventory-check',
                'title' => 'Cek Stok Sapu & Alat Pel Bulanan',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'inventory-check',
                'title' => 'Audit Bahan Pembersih (Sabun, Karbol)',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // pemeliharaan-kebersihan
            [
                'type' => 'periodic',
                'title' => 'Pembersihan Kaca Mingguan Area Kantor',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'periodic',
                'title' => 'Poles Lantai Aula Utama',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // monitoring-kebersihan
            [
                'type' => 'monitoring',
                'title' => 'Monitoring Toilet Putri Unit SMA',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'monitoring',
                'title' => 'Monitoring Taman & Halaman Belakang',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],

            // kegiatan-pekanan
            [
                'type' => 'weekly-activity',
                'title' => 'Laporan Pekanan Tim Cleaning Service',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
            [
                'type' => 'weekly-activity',
                'title' => 'Evaluasi Kinerja Kebersihan Pekan 1',
                'status' => 'Selesai',
                'institution_id' => $institution->id,
            ],
        ];

        foreach ($logs as $logData) {
            MaintenanceLog::create($logData);
        }

        // 4. ASSET LIFECYCLE (pelelangan-aset)
        AssetLifecycleLog::create([
            'item_id' => $firstItem->id,
            'quantity' => 2,
            'type' => 'auction',
            'action_date' => now(),
            'value' => 5000000,
            'reason' => 'Penyusutan dan upgrade sistem berkala.',
        ]);
        AssetLifecycleLog::create([
            'item_id' => $firstItem->id,
            'quantity' => 1,
            'type' => 'disposal',
            'action_date' => now(),
            'reason' => 'Kerusakan berat akibat tegangan listrik tidak stabil.',
        ]);

        // 5. BORROWING RECORDS (peminjaman-barang)
        BorrowingRecord::create([
            'item_id' => $firstItem->id,
            'user_id' => $admin->id,
            'institution_id' => $institution->id,
            'quantity' => 1,
            'borrow_date' => now(),
            'expected_return_date' => now()->addDays(3),
            'status' => 'borrowed',
        ]);
        BorrowingRecord::create([
            'item_id' => $firstItem->id,
            'user_id' => $admin->id,
            'institution_id' => $institution->id,
            'quantity' => 1,
            'borrow_date' => now()->subDays(7),
            'actual_return_date' => now()->subDay(),
            'status' => 'returned',
        ]);

        // 6. PARKING LOGS (parkir-area)
        ParkingLog::create([
            'vehicle_type' => 'Mobil',
            'plate_number' => 'DR 1000 AA',
            'owner_name' => 'Bpk. Ahmad',
            'entry_time' => now()->subHours(2),
            'gate_name' => 'Gerbang Utama',
        ]);
        ParkingLog::create([
            'vehicle_type' => 'Motor',
            'plate_number' => 'DR 2000 BB',
            'owner_name' => 'Ibu Siti',
            'entry_time' => now()->subHours(4),
            'exit_time' => now()->subHour(),
            'gate_name' => 'Gerbang Samping',
        ]);

        // 7. VEHICLE REQUESTS (penggunaan-kendaraan)
        VehicleRequest::create([
            'user_id' => $admin->id,
            'vehicle_id' => $firstVehicle->id,
            'destination' => 'Bandara LOP Praya',
            'purpose' => 'Antar Tamu Delegasi Yayasan',
            'start_time' => now()->addHours(1),
            'end_time' => now()->addHours(4),
            'status' => 'approved',
            'request_date' => now(),
        ]);
        VehicleRequest::create([
            'user_id' => $admin->id,
            'vehicle_id' => $firstVehicle->id,
            'destination' => 'Mataram Mall - Toko Buku',
            'purpose' => 'Belanja ATK dan Peralatan TU',
            'start_time' => now()->subDays(1),
            'end_time' => now()->subDays(1)->addHours(2),
            'status' => 'completed',
            'request_date' => now()->subDays(1),
        ]);

        // 8. ISO CHECKLIST (ceklist-iso)
        IsoChecklist::create([
            'title' => 'Audit Kebersihan Kelas Pekanan',
            'category' => 'Kebersihan',
            'frequency' => 'Weekly',
            'items' => [
                ['task' => 'Lantai disapu dan dipel bersih', 'weight' => 25],
                ['task' => 'Papan tulis dan spidol rapi', 'weight' => 25],
                ['task' => 'Meja kursi tersusun sesuai layout', 'weight' => 25],
                ['task' => 'Tempat sampah dikosongkan', 'weight' => 25],
            ],
            'is_active' => true,
        ]);
        IsoChecklist::create([
            'title' => 'Audit Kelengkapan APAR Pusat',
            'category' => 'Safety',
            'frequency' => 'Monthly',
            'items' => [
                ['task' => 'Tekanan jarum indikator normal', 'weight' => 50],
                ['task' => 'Pin pengunci segel utuh', 'weight' => 30],
                ['task' => 'Masa berlaku isi belum lewat', 'weight' => 20],
            ],
            'is_active' => true,
        ]);
    }
}
