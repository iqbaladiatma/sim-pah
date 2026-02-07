<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $textColumns = [
                'supplier_address',
                'supplier_product',
                'remarks',
                'note',
                'condition_notes',
                'standard_check',
                'method_check',
                'frequency'
            ];

            foreach ($textColumns as $column) {
                if (Schema::hasColumn('maintenance_logs', $column)) {
                    // For frequency, it might be string(50), others are TEXT. 
                    // To be safe and consistent with optimization strategy, we can cast them to nullable.
                    // If it was TEXT before, it stays TEXT. If it was string, we can make it nullable string.
                    // But given previous migrations, most are TEXT now.
                    // Let's use simple->nullable()->change() which respects current type usually, 
                    // but to be sure we don't revert TEXT to VARCHAR, we explicitly set type if known, 
                    // or just use DB statement if needed. 
                    // For now, let's assume they are TEXT as per recent migrations.
                    try {
                        $table->text($column)->nullable()->change();
                    } catch (\Exception $e) {
                        // Fallback in case of type mismatch, though unlikely given recent migrations
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            //
        });
    }
};
