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
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->string('week_name')->nullable(); // Pekanan (e.g. Pekan I)
            $table->boolean('is_checked')->default(false); // Ceklist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['week_name', 'is_checked']);
        });
    }
};
