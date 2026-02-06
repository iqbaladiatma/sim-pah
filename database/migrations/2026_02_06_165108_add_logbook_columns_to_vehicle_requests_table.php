<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->date('request_date')->nullable();
            $table->string('time_range')->nullable(); // Waktu Jam
            $table->string('institution_name')->nullable(); // Unit Kerja
            $table->string('fuel_level_before')->nullable(); // Ampere BBM sebelum pakai
            $table->string('condition_before')->nullable(); // Kondisi Kendaraan sebelum pakai
            $table->string('responsible_person')->nullable(); // Nama PJ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropColumn([
                'request_date',
                'time_range',
                'institution_name',
                'fuel_level_before',
                'condition_before',
                'responsible_person'
            ]);
        });
    }
};
