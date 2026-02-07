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
            // Optimize existing columns to TEXT to save row space
            if (Schema::hasColumn('maintenance_logs', 'check_standard'))
                $table->text('check_standard')->change();
            if (Schema::hasColumn('maintenance_logs', 'check_method'))
                $table->text('check_method')->change();
            if (Schema::hasColumn('maintenance_logs', 'check_frequency'))
                $table->text('check_frequency')->change();
            if (Schema::hasColumn('maintenance_logs', 'supplier_address'))
                $table->text('supplier_address')->change();
            if (Schema::hasColumn('maintenance_logs', 'supplier_product'))
                $table->text('supplier_product')->change();
            if (Schema::hasColumn('maintenance_logs', 'remarks'))
                $table->text('remarks')->change();
            if (Schema::hasColumn('maintenance_logs', 'description'))
                $table->text('description')->change();

            // Allow type change if needed
            $table->string('type')->change();
        });

        Schema::table('maintenance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_logs', 'brand')) {
                $table->string('brand')->nullable()->after('subcategory');
            }
            if (!Schema::hasColumn('maintenance_logs', 'size')) {
                $table->string('size')->nullable()->after('brand');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['brand', 'size']);
        });
    }
};
