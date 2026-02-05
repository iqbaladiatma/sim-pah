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
        Schema::table('items', function (Blueprint $table) {
            $table->string('room')->nullable()->after('institution_id');
            $table->string('brand')->nullable()->after('name');
            $table->date('purchase_date')->nullable()->after('brand');
            $table->string('source')->nullable()->after('stock');
            $table->string('condition')->nullable()->after('source');
            $table->string('responsible_person')->nullable()->after('condition');
            $table->text('note')->nullable()->after('responsible_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['room', 'brand', 'purchase_date', 'source', 'condition', 'responsible_person', 'note']);
        });
    }
};
