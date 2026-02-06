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
            $table->string('frequency')->nullable();

            // Monthly status: 'V' (OK), 'X' (NG), or null
            $table->string('jul_status')->nullable();
            $table->string('aug_status')->nullable();
            $table->string('sep_status')->nullable();
            $table->string('oct_status')->nullable();
            $table->string('nov_status')->nullable();
            $table->string('dec_status')->nullable();
            $table->string('jan_status')->nullable();
            $table->string('feb_status')->nullable();
            $table->string('mar_status')->nullable();
            $table->string('apr_status')->nullable();
            $table->string('may_status')->nullable();
            $table->string('jun_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'frequency',
                'jul_status', 'aug_status', 'sep_status', 'oct_status', 'nov_status', 'dec_status',
                'jan_status', 'feb_status', 'mar_status', 'apr_status', 'may_status', 'jun_status'
            ]);
        });
    }
};
