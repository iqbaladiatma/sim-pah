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
        Schema::table('asset_lifecycle_logs', function (Blueprint $table) {
            // No Nama Barang, Kode Barang, Jumlah (Unit), Tahun Perolehan, Kondisi, Nilai Perkiraan (Rp), Keterangan
            // item_id already exists ( Nama Barang, Kode Barang, Tahun Perolehan, Kondisi are in Items )
            // type already exists ('auction', 'disposal')
            // action_date already exists
            // value already exists ( Nilai Perkiraan )
            // reason already exists ( Keterangan )

            // Adding quantity for unit count if not using the one in items table directly
            $table->integer('quantity')->default(1)->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_lifecycle_logs', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
