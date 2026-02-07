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
        // 1. Aggressively Optimize Existing Monthly Columns to prevent Row Size Overload
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
            foreach ($months as $m) {
                // Shorten generic months (from 255 to 20)
                if (Schema::hasColumn('maintenance_logs', $m))
                    $table->string($m, 20)->change();

                // Shorten pump specific months (from 255 to 10)
                foreach (['putra', 'putri', 'lawata'] as $area) {
                    if (Schema::hasColumn('maintenance_logs', "{$m}_{$area}"))
                        $table->string("{$m}_{$area}", 10)->change();
                }
            }
        });

        // 2. Add New Columns
        Schema::table('maintenance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_logs', 'frequency')) {
                $table->string('frequency', 50)->nullable();
            }

            // Monthly status: 'V' (OK), 'X' (NG), or null
            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
            foreach ($months as $month) {
                if (!Schema::hasColumn('maintenance_logs', "{$month}_status")) {
                    $table->string("{$month}_status", 10)->nullable();
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
                'frequency',
                'jul_status',
                'aug_status',
                'sep_status',
                'oct_status',
                'nov_status',
                'dec_status',
                'jan_status',
                'feb_status',
                'mar_status',
                'apr_status',
                'may_status',
                'jun_status'
            ]);
        });
    }
};
