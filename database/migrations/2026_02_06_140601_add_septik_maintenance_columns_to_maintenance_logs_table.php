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
            $table->string('st_baik')->nullable()->after('fan_type');
            $table->string('st_penuh')->nullable()->after('st_baik');
            $table->string('st_bocor')->nullable()->after('st_penuh');
            $table->string('st_bau')->nullable()->after('st_bocor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['st_baik', 'st_penuh', 'st_bocor', 'st_bau']);
        });
    }
};
