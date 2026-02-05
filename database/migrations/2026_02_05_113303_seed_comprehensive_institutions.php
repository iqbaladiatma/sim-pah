<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Institution;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Definisikan data lengkap
        $institutions = [
            ['name' => 'URT (Urusan Rumah Tangga)', 'code' => 'URT'], // Khusus Admin
            ['name' => 'SEKRETARIAT/MUDIR', 'code' => 'SEKRETARIAT'],
            ['name' => 'WADIR AKADEMIK DAN KEPONDOKAN', 'code' => 'WADIR_AKD'],
            ['name' => 'WADIR PELAYANAN UMUM', 'code' => 'WADIR_UM'],
            ['name' => 'BADAN KEHORMATAN', 'code' => 'BK'],
            ['name' => 'KEUANGAN/IT', 'code' => 'KEU_IT'],
            ['name' => 'SD PUTRA', 'code' => 'SD IT PUTRA'],
            ['name' => 'SD PUTRI', 'code' => 'SD IT PUTRI'],
            ['name' => 'SMP PUTRA', 'code' => 'SMP PUTRA'],
            ['name' => 'SMP PUTRI', 'code' => 'SMP IT PUTRI'],
            ['name' => 'MA PLUS', 'code' => 'MA_PLUS'],
            ['name' => 'SMP & SMA FULLDAY', 'code' => 'SMP & SMA FULLDAY'],
            ['name' => 'SMP FULLDAY', 'code' => 'SMP FULLDAY'],
            ['name' => 'SMA FULLDAY', 'code' => 'SMA FULLDAY'],
            ['name' => 'SMA PUTRI', 'code' => 'SMA IT PUTRI'],
            ['name' => 'DINIYAH', 'code' => 'DINIYAH'],
            ['name' => "PGMI/I'DAD", 'code' => 'PGMI'],
            ['name' => 'DEPARTEMEN TAHFIDZ', 'code' => 'TAHFIDZ'],
            ['name' => 'DEPARTEMEN BAHASA', 'code' => 'BAHASA'],
            ['name' => 'ASRAMA SMP PUTRA', 'code' => 'ASPA_SMP'],
            ['name' => 'ASRAMA SMP PUTRI', 'code' => 'ASPI_SMP'],
            ['name' => 'ASRAMA MA PLUS', 'code' => 'AS_MA'],
            ['name' => 'ASRAMA SMA PUTRI', 'code' => 'ASPI_SMA'],
            ['name' => 'SARPRAS', 'code' => 'Sarpras'],
            ['name' => 'KEBERSIHAN & PERTAMANAN', 'code' => 'KBT'],
            ['name' => 'TRANSPORTASI', 'code' => 'TRANSPORT'],
            ['name' => 'DAPUR / UGL', 'code' => 'DAPUR'],
            ['name' => 'KEAMANAN', 'code' => 'SECURITY'],
            ['name' => 'UKS', 'code' => 'UKS'],
            ['name' => 'UUP', 'code' => 'UUP'],
            ['name' => 'PERPUSTAKAAN', 'code' => 'PERPUS'],
        ];

        // 2. Loop dan insert (UpdateOrCreate berdasarkan CODE agar tidak duplicate entry)
        foreach ($institutions as $inst) {
            Institution::updateOrCreate(
            ['code' => $inst['code']],
            [
                'name' => $inst['name'],
                'is_active' => true,
                'description' => 'Unit Kerja Pondok Pesantren Abu Hurairah'
            ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    // Tidak perlu menghapus data saat rollback parsial, biar aman.
    }
};
