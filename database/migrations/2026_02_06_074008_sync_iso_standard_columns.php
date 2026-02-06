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
            if (!Schema::hasColumn('items', 'code'))
                $table->string('code')->nullable()->after('name');
            if (!Schema::hasColumn('items', 'brand'))
                $table->string('brand')->nullable()->after('code');
            if (!Schema::hasColumn('items', 'specification'))
                $table->string('specification')->nullable()->after('brand');
            if (!Schema::hasColumn('items', 'material'))
                $table->string('material')->nullable()->after('specification');
            if (!Schema::hasColumn('items', 'purchased_at'))
                $table->string('purchased_at')->nullable()->after('material');
            if (!Schema::hasColumn('items', 'source'))
                $table->string('source')->nullable()->after('purchased_at');
            if (!Schema::hasColumn('items', 'price'))
                $table->decimal('price', 15, 2)->default(0)->after('source');
            if (!Schema::hasColumn('items', 'condition'))
                $table->enum('condition', ['B', 'KB', 'RB'])->default('B')->after('price');
        });

        Schema::table('maintenance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_logs', 'before_condition'))
                $table->text('before_condition')->nullable()->after('description');
            if (!Schema::hasColumn('maintenance_logs', 'after_condition'))
                $table->text('after_condition')->nullable()->after('before_condition');
            if (!Schema::hasColumn('maintenance_logs', 'action_taken'))
                $table->text('action_taken')->nullable()->after('after_condition');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(array_filter(['code', 'brand', 'specification', 'material', 'purchased_at', 'source', 'price', 'condition'], function ($col) {
                return Schema::hasColumn('items', $col);
            }));
        });

        Schema::table('maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(array_filter(['before_condition', 'after_condition', 'action_taken'], function ($col) {
                return Schema::hasColumn('maintenance_logs', $col);
            }));
        });
    }
};
