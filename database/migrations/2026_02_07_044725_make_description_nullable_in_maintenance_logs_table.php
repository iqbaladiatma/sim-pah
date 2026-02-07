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
            if (Schema::hasColumn('maintenance_logs', 'description')) {
                $table->text('description')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'remarks')) {
                $table->text('remarks')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'location')) {
                $table->string('location')->nullable()->change();
            }
            if (Schema::hasColumn('maintenance_logs', 'subcategory')) {
                $table->string('subcategory')->nullable()->change();
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
