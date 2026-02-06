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
        // Change type column from enum to string
        DB::statement('ALTER TABLE requests MODIFY COLUMN type VARCHAR(50) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum
        DB::statement("ALTER TABLE requests MODIFY COLUMN type ENUM('utilitas', 'barang_habis_pakai', 'darurat') NOT NULL");
    }
};
