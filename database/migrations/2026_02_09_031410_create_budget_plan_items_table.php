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
        if (!Schema::hasTable('budget_plan_items')) {
            Schema::create('budget_plan_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_plan_id')->constrained()->cascadeOnDelete();
                $table->string('sub_unit')->nullable();
                $table->string('category')->nullable();
                $table->text('description'); // Uraian
                $table->string('timeline')->nullable();
                $table->string('output')->nullable(); // Output Pembiayaan Kegiatan
                $table->decimal('volume', 10, 2)->default(0);
                $table->string('unit')->default('Unit');
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('total_price', 15, 2)->default(0);
                $table->decimal('realization_amount', 15, 2)->nullable(); // Realisasi
                $table->decimal('percentage', 5, 2)->nullable(); // Persentase
                $table->decimal('remaining_amount', 15, 2)->nullable(); // Sisa Anggaran
                $table->text('remarks')->nullable(); // Keterangan
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_plan_items');
    }
};
