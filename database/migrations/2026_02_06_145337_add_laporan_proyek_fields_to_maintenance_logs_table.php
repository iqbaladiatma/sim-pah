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
            $table->decimal('budget_amount', 15, 2)->nullable()->after('total_price');
            $table->decimal('actual_amount', 15, 2)->nullable()->after('budget_amount');
            $table->float('attainment_percentage')->nullable()->after('actual_amount');
            $table->text('team_members')->nullable()->after('attainment_percentage');
            $table->string('responsible_person')->nullable()->after('team_members');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'budget_amount',
                'actual_amount',
                'attainment_percentage',
                'team_members',
                'responsible_person'
            ]);
        });
    }
};
