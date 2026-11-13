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
        if (!Schema::hasTable('budget_reallocations')) {
            Schema::create('budget_reallocations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_budget_id')->constrained('budgets')->cascadeOnDelete();
                $table->foreignId('to_budget_id')->constrained('budgets')->cascadeOnDelete();
                $table->decimal('amount', 12, 2);
                $table->text('reason')->nullable();
                $table->string('status')->default('pending_cmo_approval'); // pending_cmo_approval, approved, rejected
                $table->unsignedBigInteger('requested_by_user_id')->nullable();
                $table->string('requested_by_name')->default('Kevin Parker');
                $table->unsignedBigInteger('approved_by_user_id')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_reallocations');
    }
};
