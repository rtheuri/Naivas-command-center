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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('General');
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('planning'); // planning, pending_approval, ready_for_launch, active, completed
            $table->decimal('total_budget', 14, 2)->default(0);
            $table->decimal('allocated_budget', 14, 2)->default(0);
            $table->decimal('spent_budget', 14, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('target_audience')->nullable();
            $table->string('banner_image')->nullable();
            $table->timestamps();
        });

        Schema::create('briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('objective')->nullable();
            $table->string('target_segment')->nullable();
            $table->text('key_messaging')->nullable();
            $table->string('channel')->default('in_store_posm'); // in_store_posm, digital_meta, digital_google, sms_broadcast, print_newspaper
            $table->string('status')->default('draft'); // draft, pending_review, approved, rejected
            $table->text('feedback_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('brief_id')->nullable()->constrained('briefs')->nullOnDelete();
            $table->string('title');
            $table->string('asset_type')->default('posm_banner'); // posm_banner, gondola_header, digital_graphic, video_reel, price_dangler, shelf_talker
            $table->string('file_url')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('format')->default('PNG');
            $table->integer('version')->default(1);
            $table->string('status')->default('draft'); // draft, pending_review, approved, rejected
            $table->text('approval_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('channel_name'); // Meta Ads, Google Ads & YouTube, TikTok Ads, SMS Broadcast, In-Store POSM
            $table->string('channel_icon')->default('meta'); // meta, google, tiktok, sms, posm
            $table->decimal('allocated_amount', 14, 2)->default(0);
            $table->decimal('spent_amount', 14, 2)->default(0);
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->bigInteger('conversions')->default(0);
            $table->string('pacing_status')->default('on_track'); // on_track, overpacing, underpacing, pending_deployment
            $table->boolean('is_deployed')->default(false);
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('branch_name'); // Naivas Mega City, Naivas Westlands, Naivas Junction, etc.
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('task_name');
            $table->string('zone')->default('Fresh Produce');
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, executed_pending_verification, verified
            $table->string('proof_image_url')->nullable();
            $table->text('proof_notes')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->default('System');
            $table->string('user_role')->nullable();
            $table->string('department')->default('Executive'); // IMC, DPM, OME, Executive
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('action_type'); // ASSET_APPROVED, BUDGET_REALLOCATED, CAMPAIGN_LAUNCHED, PROOF_SUBMITTED, etc.
            $table->string('title');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('store_tasks');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('briefs');
        Schema::dropIfExists('campaigns');
    }
};
