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
            if (Schema::hasColumn('maintenance_logs', 'check_standard')) {
                $table->text('check_standard')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'check_method')) {
                $table->text('check_method')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'check_frequency')) {
                $table->text('check_frequency')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            //
        });
    }
};
