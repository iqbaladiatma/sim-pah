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
            $table->string('standard_check')->nullable();
            $table->string('method_check')->nullable();

            // Ratings: 'sangat_bersih', 'bersih', 'tidak_bersih'
            $table->string('mon_rating')->nullable();
            $table->string('tue_rating')->nullable();
            $table->string('wed_rating')->nullable();
            $table->string('thu_rating')->nullable();
            $table->string('fri_rating')->nullable();
            $table->string('sat_rating')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'standard_check', 'method_check',
                'mon_rating', 'tue_rating', 'wed_rating',
                'thu_rating', 'fri_rating', 'sat_rating'
            ]);
        });
    }
};
