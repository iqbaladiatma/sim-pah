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
            $table->date('request_date')->nullable()->after('completed_at');
            $table->string('damage_type')->nullable()->after('request_date');
            $table->date('follow_up_date')->nullable()->after('damage_type');
            $table->text('remarks')->nullable()->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['request_date', 'damage_type', 'follow_up_date', 'remarks']);
        });
    }
};
