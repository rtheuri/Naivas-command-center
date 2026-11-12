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
        Schema::table('store_tasks', function (Blueprint $table) {
            $table->text('redo_instructions')->nullable()->after('proof_notes');
            $table->timestamp('redo_requested_at')->nullable()->after('redo_instructions');
            $table->foreignId('redo_requested_by_user_id')->nullable()->after('redo_requested_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_tasks', function (Blueprint $table) {
            $table->dropForeign(['redo_requested_by_user_id']);
            $table->dropColumn(['redo_instructions', 'redo_requested_at', 'redo_requested_by_user_id']);
        });
    }
};
