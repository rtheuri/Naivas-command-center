<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Phase 2 expansion.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('agency_id')->nullable()->after('branch');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('agency_id')->nullable()->after('target_audience');
            $table->string('agency_name')->nullable()->after('agency_id');
            $table->timestamp('sla_deadline')->nullable()->after('agency_name');
        });

        Schema::table('briefs', function (Blueprint $table) {
            $table->foreignId('assigned_to_user_id')->nullable()->after('created_by_user_id')->constrained('users')->nullOnDelete();
            $table->text('body_copy')->nullable()->after('key_messaging');
            $table->text('swahili_copy')->nullable()->after('body_copy');
            $table->json('ai_feedback')->nullable()->after('swahili_copy');
            $table->timestamp('sla_expires_at')->nullable()->after('approved_at');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->timestamp('sla_expires_at')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('sla_expires_at');
        });

        Schema::table('briefs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_to_user_id');
            $table->dropColumn(['body_copy', 'swahili_copy', 'ai_feedback', 'sla_expires_at']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['agency_id', 'agency_name', 'sla_deadline']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('agency_id');
        });
    }
};
