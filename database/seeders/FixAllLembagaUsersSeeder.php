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
            'sd_pa@simpah.test' => 'SD IT PUTRA',
            'sd_pi@simpah.test' => 'SD IT PUTRI',
            'smp_pa@simpah.test' => 'SMP PUTRA',
            'smp_pi@simpah.test' => 'SMP IT PUTRI',
            'sma_pa@simpah.test' => 'SMA IT PUTRI', // Note: Tidak ada SMA PUTRA di database
            'ma_pa@simpah.test' => 'MA_PLUS',

            // FULLDAY
            'smp_fullday@simpah.test' => 'SMP FULLDAY',
            'sma_fullday@simpah.test' => 'SMA FULLDAY',
            'smp_sma_fullday@simpah.test' => 'SMP & SMA FULLDAY',

            // PONDOK & AKADEMIK
            'diniyah@simpah.test' => 'DINIYAH',
            'pgmi@simpah.test' => 'PGMI',
            'tahfidz@simpah.test' => 'TAHFIDZ',
            'bahasa@simpah.test' => 'BAHASA',

            // ASRAMA
            'asrama_smp_pa@simpah.test' => 'ASPA_SMP',
            'asrama_smp_pi@simpah.test' => 'ASPI_SMP',
            'asrama_ma@simpah.test' => 'AS_MA',
            'asrama_sma_pi@simpah.test' => 'ASPI_SMA',

            // ADMINISTRASI
            'urt@simpah.test' => 'URT',
            'sekretariat@simpah.test' => 'SEKRETARIAT',
            'wadir_akd@simpah.test' => 'WADIR_AKD',
            'wadir_um@simpah.test' => 'WADIR_UM',
            'bk@simpah.test' => 'BK',
            'keuangan@simpah.test' => 'KEU_IT',

            // INFRASTRUKTUR & OPERASIONAL
            'sarpras@simpah.test' => 'Sarpras',
            'kbt@simpah.test' => 'KBT',
            'transport@simpah.test' => 'TRANSPORT',
            'dapur@simpah.test' => 'DAPUR',
            'keamanan@simpah.test' => 'SECURITY',
            'uks@simpah.test' => 'UKS',
            'uup@simpah.test' => 'UUP',
            'perpustakaan@simpah.test' => 'PERPUS',
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
