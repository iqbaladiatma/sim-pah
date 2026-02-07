<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General
            [
                'key' => 'app_maintenance_mode',
                'label' => 'Maintenance Mode (Internal)',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Matikan akses seluruh aplikasi untuk proses perbaikan.'
            ],
            // Features
            [
                'key' => 'allow_new_requests',
                'label' => 'Pengajuan Baru',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Izinkan lembaga untuk membuat pengajuan barang/perbaikan baru.'
            ],
            [
                'key' => 'enable_inventory_audit',
                'label' => 'Audit Stok Otomatis',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'features',
                'description' => 'Aktifkan validasi stok setiap kali ada transaksi keluar masuk.'
            ],
            // UI
            [
                'key' => 'show_welcome_banner',
                'label' => 'Banner Selamat Datang',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'ui',
                'description' => 'Tampilkan banner informasi di dashboard lembaga.'
            ],
            // Configuration
            [
                'key' => 'system_currency',
                'label' => 'Simbol Mata Uang',
                'value' => 'Rp',
                'type' => 'text',
                'group' => 'ui',
                'description' => 'Simbol mata uang yang digunakan di seluruh laporan biaya.'
            ],
            [
                'key' => 'emergency_contact',
                'label' => 'Kontak Darurat URT',
                'value' => '0812-3456-7890',
                'type' => 'text',
                'group' => 'system',
                'description' => 'Nomor WhatsApp yang muncul di footer jika ada kendala darurat.'
            ],
            [
                'key' => 'default_item_image',
                'label' => 'Placeholder Aset',
                'value' => '/images/placeholder-asset.png',
                'type' => 'text',
                'group' => 'ui',
                'description' => 'Gambar default jika aset tidak memiliki foto.'
            ],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
