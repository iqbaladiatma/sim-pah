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
            if (Schema::hasColumn('maintenance_logs', 'action_taken')) {
                $table->text('action_taken')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'before_condition')) {
                $table->text('before_condition')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'after_condition')) {
                $table->text('after_condition')->nullable()->change();
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
