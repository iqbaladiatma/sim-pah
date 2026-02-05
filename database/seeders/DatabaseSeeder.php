<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Institution;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Super Admin (Akses Penuh)
        User::updateOrCreate(
        ['email' => 'admin@sim-pah.com'],
        [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'role' => 'super admin',
            'institution_id' => null,
        ]
        );

        // 2. Admin URT (Operasional Pusat)
        $urt = Institution::where('code', 'URT')->first();
        if ($urt) {
            User::updateOrCreate(
            ['email' => 'urt@sim-pah.com'],
            [
                'name' => 'Admin URT',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'institution_id' => $urt->id,
            ]
            );
        }

        // 3. Contoh User Lembaga (SD Putra)
        $sd = Institution::where('code', 'SD_PA')->first();
        if ($sd) {
            User::updateOrCreate(
            ['email' => 'sdputra@sim-pah.com'],
            [
                'name' => 'Admin SD Putra',
                'password' => bcrypt('password'),
                'role' => 'lembaga',
                'institution_id' => $sd->id,
            ]
            );
        }
    }
}
