<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->string('check_standard')->nullable()->after('description');
            $table->string('check_method')->nullable()->after('check_standard');
            $table->string('check_frequency')->nullable()->after('check_method');
            $table->string('year')->nullable()->after('check_frequency');

            // Monthly status columns (Fiscal Year format starting July)
            $table->string('jul')->nullable();
            $table->string('aug')->nullable();
            $table->string('sep')->nullable();
            $table->string('oct')->nullable();
            $table->string('nov')->nullable();
            $table->string('dec')->nullable();
            $table->string('jan')->nullable();
            $table->string('feb')->nullable();
            $table->string('mar')->nullable();
            $table->string('apr')->nullable();
            $table->string('may')->nullable();
            $table->string('jun')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn([
                'check_standard',
                'check_method',
                'check_frequency',
                'year',
                'jul',
                'aug',
                'sep',
                'oct',
                'nov',
                'dec',
                'jan',
                'feb',
                'mar',
                'apr',
                'may',
                'jun'
            ]);
        });
    }
};
