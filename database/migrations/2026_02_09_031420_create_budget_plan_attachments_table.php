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
        if (!Schema::hasTable('budget_plan_attachments')) {
            Schema::create('budget_plan_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('budget_plan_item_id')->constrained()->cascadeOnDelete();
                $table->string('file_path');
                $table->string('file_type')->default('photo'); // photo, receipt
                $table->string('original_name')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_plan_attachments');
    }
};
