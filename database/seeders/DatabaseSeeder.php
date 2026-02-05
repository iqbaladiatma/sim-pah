<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $institutions = [
            ['name' => 'SD Islam Al-Azhar', 'code' => 'SD-IA'],
            ['name' => 'SMP Islam Al-Azhar', 'code' => 'SMP-IA'],
            ['name' => 'SMA Islam Al-Azhar', 'code' => 'SMA-IA'],
            ['name' => 'Rumah Tangga', 'code' => 'RT'],
            ['name' => 'Kesantrian Putra', 'code' => 'KES-PA'],
            ['name' => 'Kesantrian Putri', 'code' => 'KES-PI'],
        ];

        foreach ($institutions as $inst) {
            \App\Models\Institution::create($inst);
        }

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sim-pah.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'institution_id' => null,
        ]);

        $sd = \App\Models\Institution::where('code', 'SD-IA')->first();
        User::factory()->create([
            'name' => 'Karyawan SD',
            'email' => 'sd@sim-pah.com',
            'password' => bcrypt('password'),
            'role' => 'karyawan',
            'institution_id' => $sd->id,
        ]);
    }
}
