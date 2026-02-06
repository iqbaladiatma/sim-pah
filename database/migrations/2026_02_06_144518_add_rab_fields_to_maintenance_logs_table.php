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
        Schema::table('maintenance_logs', function (Blueprint $blueprint) {
            $blueprint->float('volume')->nullable()->after('completed_at');
            $blueprint->string('unit')->nullable()->after('volume');
            $blueprint->decimal('unit_price', 15, 2)->nullable()->after('unit');
            $blueprint->decimal('total_price', 15, 2)->nullable()->after('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['volume', 'unit', 'unit_price', 'total_price']);
        });
    }
};
