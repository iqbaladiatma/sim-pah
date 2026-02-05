<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah kolom role jadi string dulu supaya tidak error enum
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->change();
        });

        // 2. Update data lama
        DB::table('users')->where('role', 'admin')->update(['role' => 'super admin']);
        DB::table('users')->where('role', 'karyawan')->update(['role' => 'lembaga']);

        // 3. Kembalikan ke enum baru
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super admin', 'admin', 'lembaga'])->default('lembaga')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->change();
        });

        DB::table('users')->where('role', 'super admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'lembaga')->update(['role' => 'karyawan']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'karyawan'])->default('karyawan')->change();
        });
    }
};
