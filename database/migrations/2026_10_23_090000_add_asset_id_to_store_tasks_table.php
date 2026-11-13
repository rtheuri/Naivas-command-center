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
        if (Schema::hasTable('store_tasks') && !Schema::hasColumn('store_tasks', 'asset_id')) {
            Schema::table('store_tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('asset_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('store_tasks') && Schema::hasColumn('store_tasks', 'asset_id')) {
            Schema::table('store_tasks', function (Blueprint $table) {
                $table->dropColumn('asset_id');
            });
        }
    }
};
