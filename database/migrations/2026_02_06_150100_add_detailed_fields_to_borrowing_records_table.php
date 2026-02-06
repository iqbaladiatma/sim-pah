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
        Schema::table('borrowing_records', function (Blueprint $table) {
            $table->string('borrow_condition')->nullable()->after('borrow_date');
            $table->string('borrower_paraf')->nullable()->after('borrow_condition');
            $table->string('return_condition')->nullable()->after('actual_return_date');
            $table->string('returner_paraf')->nullable()->after('return_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowing_records', function (Blueprint $table) {
            $table->dropColumn([
                'borrow_condition',
                'borrower_paraf',
                'return_condition',
                'returner_paraf'
            ]);
        });
    }
};
