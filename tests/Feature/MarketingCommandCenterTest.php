<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Campaign;
use App\Models\StoreTask;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingCommandCenterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_judge_portal_renders_with_four_login_personas(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Judge Portal');
        $response->assertSee('Amina Mwangi');
        $response->assertSee('Kevin Omondi');
        $response->assertSee('Faith Wanjiku');
        $response->assertSee('Peter Mumo');
        $response->assertSee('Login as IMC Lead');
        $response->assertSee('Login as DPM Media Buyer');
        $response->assertSee('Login as OME Store Manager');
        $response->assertSee('Login as Marketing Executive');
    }

    public function test_frictionless_login_for_each_role(): void
    {
        // 1. IMC Lead
        $res1 = $this->get('/login/as/imc_lead');
        $res1->assertRedirect('/imc');
        $this->assertAuthenticatedAs(User::where('role', 'imc_lead')->first());

        // 2. DPM Media Buyer
        $res2 = $this->get('/login/as/dpm_buyer');
        $res2->assertRedirect('/dpm');
        $this->assertAuthenticatedAs(User::where('role', 'dpm_buyer')->first());

        // 3. OME Store Manager
        $res3 = $this->get('/login/as/ome_manager');
        $res3->assertRedirect('/ome');
        $this->assertAuthenticatedAs(User::where('role', 'ome_manager')->first());

        // 4. Marketing Executive
        $res4 = $this->get('/login/as/marketing_exec');
        $res4->assertRedirect('/executive');
        $this->assertAuthenticatedAs(User::where('role', 'marketing_exec')->first());
    }

    public function test_executive_dashboard_displays_all_five_campaigns_and_kpis(): void
    {
        $execUser = User::where('role', 'marketing_exec')->first();

        $response = $this->actingAs($execUser)->get('/executive');

        $response->assertStatus(200);
        $response->assertSee('Executive Marketing Dashboard');
        $response->assertSee('Weekend Produce Promo');
        $response->assertSee('Back to School Mega Sale');
        $response->assertSee('Fresh Harvest Week');
        $response->assertSee('Naivas Super Brand Month');
        $response->assertSee('Customer Appreciation Days');
        $response->assertSee('Live Activity Feed');
    }

    public function test_imc_asset_approval_automatically_unlocks_campaign_to_ready_for_launch(): void
    {
        $imcUser = User::where('role', 'imc_lead')->first();

        // Fresh Harvest Week is seeded with 'pending_approval' and has an asset in 'pending_review'
        $campaign = Campaign::where('code', 'CMP-2026-003')->first();
        $this->assertEquals('pending_approval', $campaign->status);

        $pendingAsset = $campaign->assets()->where('status', 'pending_review')->first();
        $this->assertNotNull($pendingAsset);

        // IMC Lead approves asset
        $response = $this->actingAs($imcUser)->post("/imc/assets/{$pendingAsset->id}/approve", [
            'notes' => 'Quality verified for high-contrast retail visibility.',
        ]);

        $response->assertSessionHas('success');

        // Verify Asset is approved
        $pendingAsset->refresh();
        $this->assertEquals('approved', $pendingAsset->status);
        $this->assertEquals($imcUser->id, $pendingAsset->approved_by_user_id);

        // Sequential interrelation: Verify Campaign status automatically changed to 'ready_for_launch'
        $campaign->refresh();
        $this->assertEquals('ready_for_launch', $campaign->status);

        // Verify Cross-department activity log was created
        $this->assertDatabaseHas('activity_logs', [
            'department' => 'IMC',
            'action_type' => 'ASSET_APPROVED',
            'campaign_id' => $campaign->id,
        ]);
    }

    public function test_dpm_ad_deployment_gating(): void
    {
        $dpmUser = User::where('role', 'dpm_buyer')->first();

        // Campaign 4 (Super Brand Month) is in 'planning' with no approved assets
        $campaign = Campaign::where('code', 'CMP-2026-004')->first();

        // Attempting to deploy without IMC approval should fail/warn
        $resLocked = $this->actingAs($dpmUser)->post("/dpm/campaigns/{$campaign->id}/deploy");
        $resLocked->assertSessionHas('error');
        $campaign->refresh();
        $this->assertNotEquals('active', $campaign->status);

        // Now test on Campaign 1 (Weekend Produce Promo), which is 'ready_for_launch' with approved assets
        $c1 = Campaign::where('code', 'CMP-2026-001')->first();
        $this->assertTrue($c1->hasApprovedAssets());

        $resUnlocked = $this->actingAs($dpmUser)->post("/dpm/campaigns/{$c1->id}/deploy");
        $resUnlocked->assertSessionHas('success');

        $c1->refresh();
        $this->assertEquals('active', $c1->status);

        // Verify activity log
        $this->assertDatabaseHas('activity_logs', [
            'department' => 'DPM',
            'action_type' => 'CAMPAIGN_LAUNCHED',
            'campaign_id' => $c1->id,
        ]);
    }

    public function test_dpm_budget_reallocation_moves_funds_and_recalculates(): void
    {
        $dpmUser = User::where('role', 'dpm_buyer')->first();

        $fromBudget = Budget::whereRaw('allocated_amount - spent_amount >= 200000')->first();
        $toBudget = Budget::where('id', '!=', $fromBudget->id)->first();

        $initialFrom = $fromBudget->allocated_amount;
        $initialTo = $toBudget->allocated_amount;
        $transferAmount = 150000;

        $response = $this->actingAs($dpmUser)->post('/dpm/budget/reallocate', [
            'from_budget_id' => $fromBudget->id,
            'to_budget_id' => $toBudget->id,
            'amount' => $transferAmount,
            'reason' => 'Shifting underutilized SMS funds to high-converting TikTok video ads.',
        ]);

        $response->assertSessionHas('success');

        $fromBudget->refresh();
        $toBudget->refresh();

        $this->assertEquals($initialFrom - $transferAmount, $fromBudget->allocated_amount);
        $this->assertEquals($initialTo + $transferAmount, $toBudget->allocated_amount);

        // Verify Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'department' => 'DPM',
            'action_type' => 'BUDGET_REALLOCATED',
        ]);
    }

    public function test_ome_store_manager_submits_proof_of_execution(): void
    {
        $omeUser = User::where('role', 'ome_manager')->first();

        $task = StoreTask::where('status', 'pending')->where('branch_name', 'Naivas Mega City')->first();
        $this->assertNotNull($task);

        $response = $this->actingAs($omeUser)->post("/ome/tasks/{$task->id}/proof", [
            'preset_photo' => 'gondola',
            'proof_notes' => 'Gondola End Banner mounted at eye level by morning branch merchandising team.',
        ]);

        $response->assertSessionHas('success');

        $task->refresh();
        $this->assertEquals('executed_pending_verification', $task->status);
        $this->assertNotNull($task->proof_image_url);
        $this->assertNotNull($task->executed_at);
        $this->assertEquals($omeUser->id, $task->assigned_to_user_id);

        // Verify Instant sync to Activity Log for Executive visibility
        $this->assertDatabaseHas('activity_logs', [
            'department' => 'OME',
            'action_type' => 'PROOF_SUBMITTED',
            'campaign_id' => $task->campaign_id,
        ]);
    }
}
