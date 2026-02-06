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
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->boolean('mon_status')->default(false);
            $table->boolean('tue_status')->default(false);
            $table->boolean('wed_status')->default(false);
            $table->boolean('thu_status')->default(false);
            $table->boolean('fri_status')->default(false);
            $table->boolean('sat_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['mon_status', 'tue_status', 'wed_status', 'thu_status', 'fri_status', 'sat_status']);
        });
    }
};
