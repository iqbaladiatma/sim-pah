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
        Schema::table('items', function (Blueprint $table) {
            $table->string('no_urut_satker')->nullable()->after('code');
            $table->string('no_urut_pondok')->nullable()->after('no_urut_satker');
            $table->decimal('depreciation_price', 15, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['no_urut_satker', 'no_urut_pondok', 'depreciation_price']);
        });
    }
};
