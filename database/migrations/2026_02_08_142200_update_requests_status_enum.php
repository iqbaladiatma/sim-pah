<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Update status enum to only 4 values: pending, processed, approved, rejected
     */
    public function up(): void
    {
        // First update any 'completed' status to 'approved'
        DB::table('requests')
            ->where('status', 'completed')
            ->update(['status' => 'approved']);

        // Modify the enum column
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('pending', 'processed', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM('pending', 'processed', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
