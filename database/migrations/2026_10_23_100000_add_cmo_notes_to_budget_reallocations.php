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
        if (Schema::hasTable('budget_reallocations')) {
            Schema::table('budget_reallocations', function (Blueprint $table) {
                if (!Schema::hasColumn('budget_reallocations', 'cmo_notes')) {
                    $table->text('cmo_notes')->nullable();
                }
                if (!Schema::hasColumn('budget_reallocations', 'executed_at')) {
                    $table->timestamp('executed_at')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('budget_reallocations')) {
            Schema::table('budget_reallocations', function (Blueprint $table) {
                if (Schema::hasColumn('budget_reallocations', 'cmo_notes')) {
                    $table->dropColumn('cmo_notes');
                }
                if (Schema::hasColumn('budget_reallocations', 'executed_at')) {
                    $table->dropColumn('executed_at');
                }
            });
        }
    }
};
