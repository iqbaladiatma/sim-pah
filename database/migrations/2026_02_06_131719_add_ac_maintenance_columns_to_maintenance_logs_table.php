<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            // AC Maintenance Fields (Nullable as requested)
            $table->string('ac_indoor_pc')->nullable();
            $table->string('ac_indoor_sw')->nullable();
            $table->string('ac_outdoor_freon')->nullable();
            $table->string('ac_outdoor_amp')->nullable();
            $table->string('ac_kelistrikan_jaringan')->nullable();
            $table->string('ac_kelistrikan_tegangan')->nullable();
            $table->string('ac_divisi')->nullable(); // Metadata
            $table->string('ac_tgl_bln')->nullable(); // Metadata
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'ac_indoor_pc',
                'ac_indoor_sw',
                'ac_outdoor_freon',
                'ac_outdoor_amp',
                'ac_kelistrikan_jaringan',
                'ac_kelistrikan_tegangan',
                'ac_divisi',
                'ac_tgl_bln'
            ]);
        });
    }
};
