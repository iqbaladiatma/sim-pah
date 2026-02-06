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
        $months = ['jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'jan', 'feb', 'mar', 'apr', 'may', 'jun'];
        $areas = ['putra', 'putri', 'lawata'];

        foreach ($months as $month) {
            foreach ($areas as $area) {
                $column = $month . '_' . $area;
                try {
                    DB::statement("ALTER TABLE maintenance_logs DROP COLUMN IF EXISTS $column");
                } catch (\Exception $e) {
                    // Ignore if IF EXISTS not supported, or column doesn't exist
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
