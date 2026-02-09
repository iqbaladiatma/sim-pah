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
            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
            $areas = ['putra', 'putri', 'lawata'];

            foreach ($months as $month) {
                foreach ($areas as $area) {
                    $column = $month . '_' . $area;
                    if (!Schema::hasColumn('maintenance_logs', $column)) {
                        $table->string($column, 10)->nullable();
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
            $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
            $areas = ['putra', 'putri', 'lawata'];

            foreach ($months as $month) {
                foreach ($areas as $area) {
                    $table->dropColumn($month . '_' . $area);
                }
            }
        });
    }
};
