<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\User;
use App\Models\Institution;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing requests
        Request::truncate();

        // Get institutions and users
        $institutions = Institution::all();
        $admin = User::where('role', 'admin')->orWhere('role', 'super admin')->first();

        if (!$admin || $institutions->isEmpty()) {
            $this->command->warn('No admin user or institutions found. Skipping RequestSeeder.');
            return;
        }

        // Sample requests data - realistic scenarios for a pesantren
        $requests = [
            // === UTILITAS (Utilities) ===
            [
                'type' => 'utilitas',
                'title' => 'Perbaikan Instalasi Listrik Gedung Asrama Putra',
                'description' => 'Terdapat beberapa titik stop kontak yang tidak berfungsi di lantai 2 asrama putra. Perlu pengecekan dan perbaikan instalasi oleh teknisi listrik bersertifikat.',
                'estimated_cost' => 2500000,
                'status' => 'pending',
                'created_at' => now()->subDays(1),
            ],
            [
                'type' => 'utilitas',
                'title' => 'Penggantian Pompa Air Gedung Madrasah',
                'description' => 'Pompa air utama sudah tidak maksimal tekanannya. Air tidak sampai ke lantai 3. Diperlukan penggantian pompa dengan kapasitas lebih besar.',
                'estimated_cost' => 4500000,
                'status' => 'approved',
                'admin_note' => 'Disetujui. Segera koordinasikan dengan vendor untuk pengadaan.',
                'created_at' => now()->subDays(5),
            ],
            [
                'type' => 'utilitas',
                'title' => 'Pemasangan CCTV Tambahan Area Parkir',
                'description' => 'Diperlukan penambahan 4 unit CCTV di area parkir motor untuk meningkatkan keamanan. Beberapa kali terjadi kehilangan helm.',
                'estimated_cost' => 8000000,
                'status' => 'pending',
                'created_at' => now()->subDays(2),
            ],
            [
                'type' => 'utilitas',
                'title' => 'Service AC Ruang Guru dan Lab Komputer',
                'description' => 'AC di ruang guru dan lab komputer sudah 1 tahun tidak di-service. Beberapa unit sudah tidak dingin maksimal.',
                'estimated_cost' => 1500000,
                'status' => 'completed',
                'admin_note' => 'Sudah dilaksanakan oleh CV Sejuk Selalu tanggal 3 Februari 2026.',
                'created_at' => now()->subDays(10),
            ],
            [
                'type' => 'utilitas',
                'title' => 'Perbaikan Atap Bocor Musholla',
                'description' => 'Terdapat kebocoran di bagian belakang musholla yang menyebabkan lantai basah saat hujan. Perlu perbaikan segera sebelum musim hujan bertambah.',
                'estimated_cost' => 3500000,
                'status' => 'processed',
                'admin_note' => 'Sedang dalam proses survey oleh kontraktor.',
                'created_at' => now()->subDays(3),
            ],

            // === BARANG HABIS PAKAI (Consumables) ===
            [
                'type' => 'barang_habis_pakai',
                'title' => 'Pengadaan ATK Semester Genap 2026',
                'description' => 'Kebutuhan ATK untuk seluruh unit meliputi: kertas A4 (50 rim), spidol whiteboard (100 pcs), penghapus (50 pcs), dan perlengkapan tulis lainnya.',
                'estimated_cost' => 5000000,
                'status' => 'approved',
                'admin_note' => 'Disetujui untuk pengadaan bertahap.',
                'created_at' => now()->subDays(7),
            ],
            [
                'type' => 'barang_habis_pakai',
                'title' => 'Pembelian Alat Kebersihan Asrama',
                'description' => 'Stok alat kebersihan asrama sudah menipis. Diperlukan: sapu ijuk (20), sapu lidi (15), pel (10), ember (20), dan cairan pembersih lantai (30 liter).',
                'estimated_cost' => 1200000,
                'status' => 'pending',
                'created_at' => now()->subHours(6),
            ],
            [
                'type' => 'barang_habis_pakai',
                'title' => 'Pengadaan Tinta Printer Kantor TU',
                'description' => 'Stok tinta printer untuk 3 unit printer Epson L3110 di kantor TU habis. Diperlukan masing-masing 2 set (Black + Color).',
                'estimated_cost' => 900000,
                'status' => 'completed',
                'admin_note' => 'Sudah dibeli dan didistribusikan.',
                'created_at' => now()->subDays(14),
            ],
            [
                'type' => 'barang_habis_pakai',
                'title' => 'Pembelian Bahan Praktikum IPA',
                'description' => 'Kebutuhan bahan praktikum untuk laboratorium IPA semester genap: baterai, kabel, lampu LED, dan komponen elektronika dasar.',
                'estimated_cost' => 2000000,
                'status' => 'pending',
                'created_at' => now()->subDays(4),
            ],
            [
                'type' => 'barang_habis_pakai',
                'title' => 'Pengadaan Galon Air Minum Kelas',
                'description' => 'Pengadaan 30 buah galon air minum kosong untuk didistribusikan ke setiap kelas yang belum memiliki fasilitas air minum.',
                'estimated_cost' => 1800000,
                'status' => 'rejected',
                'admin_note' => 'Ditolak. Sudah tersedia dispenser di setiap lorong. Lebih efisien menggunakan galon bersama.',
                'created_at' => now()->subDays(8),
            ],

            // === DARURAT (Emergency) ===
            [
                'type' => 'darurat',
                'title' => 'URGENT: Pipa Air Pecah di Dapur Umum',
                'description' => 'Pipa air utama di dapur umum pecah dan menyebabkan banjir. Memerlukan perbaikan segera untuk operasional dapur.',
                'estimated_cost' => 500000,
                'status' => 'completed',
                'admin_note' => 'Sudah diperbaiki oleh tukang internal. Selesai dalam 2 jam.',
                'created_at' => now()->subDays(2),
            ],
            [
                'type' => 'darurat',
                'title' => 'Kerusakan Genset Saat Mati Listrik',
                'description' => 'Genset utama tidak menyala saat terjadi pemadaman listrik PLN. Perlu pengecekan dan perbaikan segera karena ujian sedang berlangsung.',
                'estimated_cost' => 3000000,
                'status' => 'approved',
                'admin_note' => 'Approved. Prioritas tinggi. Teknisi sudah dihubungi.',
                'created_at' => now()->subHours(12),
            ],
            [
                'type' => 'darurat',
                'title' => 'Penggantian Kaca Jendela Pecah Lab Komputer',
                'description' => 'Kaca jendela lab komputer pecah akibat terkena bola saat jam olahraga. Perlu penggantian segera untuk keamanan.',
                'estimated_cost' => 750000,
                'status' => 'processed',
                'admin_note' => 'Sedang menunggu pengiriman kaca dari supplier.',
                'created_at' => now()->subDays(1),
            ],
            [
                'type' => 'darurat',
                'title' => 'Perbaikan Pintu Gerbang Utama Rusak',
                'description' => 'Motor penggerak pintu gerbang otomatis rusak. Gerbang tidak bisa dibuka/tutup secara otomatis. Mengganggu akses kendaraan.',
                'estimated_cost' => 2500000,
                'status' => 'pending',
                'created_at' => now()->subHours(3),
            ],
            [
                'type' => 'darurat',
                'title' => 'Kebocoran Gas LPG di Dapur Asrama Putri',
                'description' => 'Ditemukan kebocoran pada selang regulator tabung gas di dapur asrama putri. Sudah diamankan, perlu penggantian komponen.',
                'estimated_cost' => 200000,
                'status' => 'completed',
                'admin_note' => 'Sudah diganti dengan regulator dan selang baru. Aman.',
                'created_at' => now()->subDays(6),
            ],
        ];

        foreach ($requests as $index => $requestData) {
            // Distribute requests across different institutions
            $institution = $institutions[$index % $institutions->count()];

            // Get a user from this institution or use admin
            $user = User::where('institution_id', $institution->id)->first() ?? $admin;

            Request::create([
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'type' => $requestData['type'],
                'title' => $requestData['title'],
                'description' => $requestData['description'],
                'estimated_cost' => $requestData['estimated_cost'],
                'status' => $requestData['status'],
                'admin_note' => $requestData['admin_note'] ?? null,
                'created_at' => $requestData['created_at'],
                'updated_at' => $requestData['created_at'],
            ]);
        }

        $this->command->info('✅ RequestSeeder: 15 sample requests created successfully!');
    }
}
