<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixAllLembagaUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates users for ALL institutions based on ACTUAL database codes
     * and matches them with the email format from auth.md
     */
    public function run(): void
    {
        // Mapping dari email yang diharapkan (auth.md) ke CODE yang ada di database
        $userMapping = [
            // PENDIDIKAN
            'sd_pa@abhursarpras.com' => 'SD IT PUTRA',
            'sd_pi@abhursarpras.com' => 'SD IT PUTRI',
            'smp_pa@abhursarpras.com' => 'SMP PUTRA',
            'smp_pi@abhursarpras.com' => 'SMP IT PUTRI',
            'sma_pa@abhursarpras.com' => 'SMA IT PUTRI', // Note: Tidak ada SMA PUTRA di database
            'ma_pa@abhursarpras.com' => 'MA_PLUS',

            // FULLDAY
            'smp_fullday@abhursarpras.com' => 'SMP FULLDAY',
            'sma_fullday@abhursarpras.com' => 'SMA FULLDAY',
            'smp_sma_fullday@abhursarpras.com' => 'SMP & SMA FULLDAY',

            // PONDOK & AKADEMIK
            'diniyah@abhursarpras.com' => 'DINIYAH',
            'pgmi@abhursarpras.com' => 'PGMI',
            'tahfidz@abhursarpras.com' => 'TAHFIDZ',
            'bahasa@abhursarpras.com' => 'BAHASA',

            // ASRAMA
            'asrama_smp_pa@abhursarpras.com' => 'ASPA_SMP',
            'asrama_smp_pi@abhursarpras.com' => 'ASPI_SMP',
            'asrama_ma@abhursarpras.com' => 'AS_MA',
            'asrama_sma_pi@abhursarpras.com' => 'ASPI_SMA',

            // ADMINISTRASI
            'urt@abhursarpras.com' => 'URT',
            'sekretariat@abhursarpras.com' => 'SEKRETARIAT',
            'wadir_akd@abhursarpras.com' => 'WADIR_AKD',
            'wadir_um@abhursarpras.com' => 'WADIR_UM',
            'bk@abhursarpras.com' => 'BK',
            'keuangan@abhursarpras.com' => 'KEU_IT',

            // INFRASTRUKTUR & OPERASIONAL
            'sarpras@abhursarpras.com' => 'Sarpras',
            'kbt@abhursarpras.com' => 'KBT',
            'transport@abhursarpras.com' => 'TRANSPORT',
            'dapur@abhursarpras.com' => 'DAPUR',
            'keamanan@abhursarpras.com' => 'SECURITY',
            'uks@abhursarpras.com' => 'UKS',
            'uup@abhursarpras.com' => 'UUP',
            'perpustakaan@abhursarpras.com' => 'PERPUS',
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($userMapping as $email => $institutionCode) {
            $institution = Institution::where('code', $institutionCode)->first();

            if (!$institution) {
                $this->command->warn("⚠️  Institution not found: {$institutionCode} for {$email}");
                $skipped++;
                continue;
            }

            // Check if user exists
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update existing user
                $user->update([
                    'institution_id' => $institution->id,
                    'role' => 'lembaga',
                    'password' => Hash::make('password'),
                ]);
                $this->command->info("✅ Updated: {$email} -> {$institution->name}");
                $updated++;
            } else {
                // Create new user
                User::create([
                    'name' => $institution->name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'lembaga',
                    'institution_id' => $institution->id,
                ]);
                $this->command->info("✨ Created: {$email} -> {$institution->name}");
                $created++;
            }
        }

        $this->command->info("\n=== SEEDING SUMMARY ===");
        $this->command->info("✨ Created: {$created} users");
        $this->command->info("✅ Updated: {$updated} users");
        $this->command->warn("⚠️  Skipped: {$skipped} users");
        $this->command->info("======================\n");
    }
}
