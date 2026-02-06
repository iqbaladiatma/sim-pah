<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->string('kran_air')->nullable();
            $table->string('lampu')->nullable();
            $table->string('fiting_lampu')->nullable();
            $table->string('saklar_lampu')->nullable();
            $table->string('ember')->nullable();
            $table->string('gayung')->nullable();
            $table->string('closet')->nullable();
            $table->string('pintu')->nullable();
            $table->string('grendel_pintu')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'kran_air',
                'lampu',
                'fiting_lampu',
                'saklar_lampu',
                'ember',
                'gayung',
                'closet',
                'pintu',
                'grendel_pintu'
            ]);
        });
    }
};
