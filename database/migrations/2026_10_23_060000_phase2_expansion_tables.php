<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Phase 2 expansion.
     * Columns are defined directly in the primary table schemas to ensure 100%
     * cross-database compatibility with serverless SQLite runtimes (e.g. Vercel)
     * where ALTER TABLE foreign-key table-rebuild pragma functions are unsupported.
     */
    public function up(): void
    {
        // Safe no-op / compatibility check
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'agency_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('agency_id')->nullable();
            });
        }

        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'agency_id')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->string('agency_id')->nullable();
                $table->string('agency_name')->nullable();
                $table->timestamp('sla_deadline')->nullable();
            });
        }

        if (Schema::hasTable('briefs') && !Schema::hasColumn('briefs', 'assigned_to_user_id')) {
            Schema::table('briefs', function (Blueprint $table) {
                $table->unsignedBigInteger('assigned_to_user_id')->nullable();
                $table->text('body_copy')->nullable();
                $table->text('swahili_copy')->nullable();
                $table->json('ai_feedback')->nullable();
                $table->timestamp('sla_expires_at')->nullable();
            });
        }

        if (Schema::hasTable('assets') && !Schema::hasColumn('assets', 'sla_expires_at')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->timestamp('sla_expires_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
