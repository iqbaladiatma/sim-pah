<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->enum('type', ['utilitas', 'barang_habis_pakai', 'darurat']);
            $table->string('title');
            $table->text('description');
            $table->string('photo_evidence')->nullable();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->enum('status', ['pending', 'processed', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
