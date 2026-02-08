<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Update status enum to 5 values: pending, processed, approved, rejected, completed
     */
    public function up(): void
    {
        // Modify the enum column to include all 5 statuses
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('pending', 'processed', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('pending', 'processed', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
