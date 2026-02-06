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
                    if (Schema::hasColumn('maintenance_logs', $month . '_' . $area)) {
                        $table->dropColumn($month . '_' . $area);
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
        // No need to reverse cleanup
    }
};
