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
        // 1. Optimize existing columns to TEXT FIRST (Separate Schema::table call)
        Schema::table('maintenance_logs', function (Blueprint $table) {
            // 1. Optimize existing columns to TEXT to prevent Row Size Too Large errors
            if (Schema::hasColumn('maintenance_logs', 'check_standard'))
                $table->text('check_standard')->change();
            if (Schema::hasColumn('maintenance_logs', 'check_method'))
                $table->text('check_method')->change();
            if (Schema::hasColumn('maintenance_logs', 'check_frequency'))
                $table->text('check_frequency')->change();
            if (Schema::hasColumn('maintenance_logs', 'supplier_address'))
                $table->text('supplier_address')->change();
            if (Schema::hasColumn('maintenance_logs', 'supplier_product'))
                $table->text('supplier_product')->change();
            if (Schema::hasColumn('maintenance_logs', 'remarks'))
                $table->text('remarks')->change();
            if (Schema::hasColumn('maintenance_logs', 'description'))
                $table->text('description')->change();
            if (Schema::hasColumn('maintenance_logs', 'note'))
                $table->text('note')->change();
            if (Schema::hasColumn('maintenance_logs', 'condition_notes'))
                $table->text('condition_notes')->change();
            if (Schema::hasColumn('maintenance_logs', 'action_taken'))
                $table->text('action_taken')->change();
        });

        // 2. Add Missing Columns (Separate Schema::table call)
        Schema::table('maintenance_logs', function (Blueprint $table) {
            // 2. Add Missing Columns (brand, size) if they don't exist
            if (!Schema::hasColumn('maintenance_logs', 'brand')) {
                $table->string('brand')->nullable()->after('subcategory');
            }
            if (!Schema::hasColumn('maintenance_logs', 'size')) {
                $table->string('size')->nullable()->after('brand');
            }

            // 3. Add Detailed Monitoring Columns (Safe Check)
            if (!Schema::hasColumn('maintenance_logs', 'standard_check')) {
                $table->string('standard_check')->nullable();
            }
            if (!Schema::hasColumn('maintenance_logs', 'method_check')) {
                $table->string('method_check')->nullable();
            }

            // Ratings: 'sangat_bersih', 'bersih', 'tidak_bersih'
            $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
            foreach ($days as $day) {
                if (!Schema::hasColumn('maintenance_logs', "{$day}_rating")) {
                    $table->string("{$day}_rating")->nullable();
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
            $table->dropColumn([
                'standard_check',
                'method_check',
                'mon_rating',
                'tue_rating',
                'wed_rating',
                'thu_rating',
                'fri_rating',
                'sat_rating'
            ]);
        });
    }
};
