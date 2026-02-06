<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LembagaUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = Institution::all();

        foreach ($institutions as $institution) {
            // Check if user already exists for this institution
            $existingUser = User::where('institution_id', $institution->id)
                ->where('role', 'lembaga')
                ->first();

            if (!$existingUser) {
                User::create([
                    'name' => "User {$institution->code}",
                    'email' => strtolower($institution->code) . '@simpah.test',
                    'password' => Hash::make('password'), // Default password
                    'role' => 'lembaga',
                    'institution_id' => $institution->id,
                    'is_super_admin' => false,
                ]);

                $this->command->info("Created user for: {$institution->name}");
            }
            else {
                $this->command->warn("User already exists for: {$institution->name}");
            }
        }

        $this->command->info('Lembaga users seeding completed!');
    }
}
