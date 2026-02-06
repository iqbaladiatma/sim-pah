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
            $table->text('supplier_address')->nullable()->after('procurement_type');
            $table->string('supplier_contact')->nullable()->after('supplier_address');
            $table->string('supplier_product')->nullable()->after('supplier_contact');
            $table->float('sc_price')->nullable()->after('supplier_product');
            $table->float('sc_quality')->nullable()->after('sc_price');
            $table->float('sc_delivery')->nullable()->after('sc_quality');
            $table->float('sc_service')->nullable()->after('sc_delivery');
            $table->float('sc_legal')->nullable()->after('sc_service');
            $table->float('sc_total')->nullable()->after('sc_legal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'supplier_address',
                'supplier_contact',
                'supplier_product',
                'sc_price',
                'sc_quality',
                'sc_delivery',
                'sc_service',
                'sc_legal',
                'sc_total'
            ]);
        });
    }
};
