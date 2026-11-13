<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\BudgetReallocation;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DpmController extends Controller
{
    /**
     * Display the DPM Media Spend & Pacing Tracker Dashboard.
     */
    public function index()
    {
        $campaigns = Campaign::with(['assets', 'budgets', 'briefs'])
            ->orderByRaw("CASE 
                WHEN status = 'active' THEN 1 
                WHEN status = 'ready_for_launch' THEN 2 
                WHEN status = 'pending_approval' THEN 3 
                ELSE 4 END")
            ->get();

        $allBudgets = Budget::with('campaign')->get();

        // Calculate High-Level DPM Metrics
        $totalAllocated = $allBudgets->sum('allocated_amount');
        $totalSpent = $allBudgets->sum('spent_amount');
        $totalImpressions = $allBudgets->sum('impressions');
        $totalClicks = $allBudgets->sum('clicks');
        $totalConversions = $allBudgets->sum('conversions');
        $blendedCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0;
        $activeChannelsCount = $allBudgets->where('is_deployed', true)->count();

        // Group budgets by channel for breakdown cards
        $channelStats = Budget::selectRaw('
                channel_name,
                channel_icon,
                SUM(allocated_amount) as total_allocated,
                SUM(spent_amount) as total_spent,
                SUM(impressions) as total_impressions,
                SUM(clicks) as total_clicks
            ')
            ->groupBy('channel_name', 'channel_icon')
            ->get();

        // Reallocations audit history and pending queue
        $reallocations = BudgetReallocation::with(['fromBudget.campaign', 'toBudget.campaign', 'approvedBy'])
            ->latest()
            ->take(15)
            ->get();
        $pendingReallocationsCount = $reallocations->where('status', 'pending_cmo_approval')->count();

        // CMO Governance Decisions: Approved shifts awaiting DPM execution & recently rejected shifts
        $approvedReallocations = BudgetReallocation::with(['fromBudget.campaign', 'toBudget.campaign', 'approvedBy'])
            ->whereIn('status', ['approved_by_cmo', 'approved'])
            ->latest()
            ->get();

        $rejectedReallocations = BudgetReallocation::with(['fromBudget.campaign', 'toBudget.campaign', 'approvedBy'])
            ->whereIn('status', ['rejected_by_cmo', 'rejected'])
            ->latest()
            ->take(3)
            ->get();

        // Approved Creative Assets Cleared by Sarah Jenkins (IMC Lead) For Live Ad Flighting
        $approvedAssets = Asset::where('status', 'approved')
            ->with(['campaign.budgets', 'brief', 'approvedBy'])
            ->latest('approved_at')
            ->get();

        return view('dashboards.dpm', [
            'campaigns' => $campaigns,
            'approvedAssets' => $approvedAssets,
            'allBudgets' => $allBudgets,
            'totalAllocated' => $totalAllocated,
            'totalSpent' => $totalSpent,
            'totalImpressions' => $totalImpressions,
            'totalClicks' => $totalClicks,
            'totalConversions' => $totalConversions,
            'blendedCtr' => $blendedCtr,
            'activeChannelsCount' => $activeChannelsCount,
            'channelStats' => $channelStats,
            'reallocations' => $reallocations,
            'pendingReallocationsCount' => $pendingReallocationsCount,
            'approvedReallocations' => $approvedReallocations,
            'rejectedReallocations' => $rejectedReallocations,
        ]);
    }

    /**
     * Deploy live advertising channels for a campaign.
     * Enforces strict sequential gating: requires creative asset approval from IMC Lead!
     */
    public function deployAds(Request $request, Campaign $campaign)
    {
        // Gating Check: Ensure campaign has approved assets OR is ready for launch / active
        $hasApproved = $campaign->hasApprovedAssets() || in_array($campaign->status, ['ready_for_launch', 'approved_ready_for_media', 'active']);

        if (!$hasApproved) {
            return back()->with('error', 'Strict Gating Locked: Cannot deploy advertising for "' . $campaign->title . '". Campaign requires creative asset sign-off by Sarah Jenkins (IMC Lead).');
        }

        // Deploy digital media budgets
        $updatedChannels = [];
        foreach ($campaign->budgets as $budget) {
            if (!$budget->is_deployed && $budget->channel_icon !== 'posm') {
                $budget->update([
                    'is_deployed' => true,
                    'deployed_at' => now(),
                    'pacing_status' => 'on_track',
                ]);
                $updatedChannels[] = $budget->channel_name;
            }
        }

        // If campaign has no budgets, automatically create standard digital ad channels
        if ($campaign->budgets->isEmpty()) {
            $cap = $campaign->allocated_budget > 0 ? (float)$campaign->allocated_budget : 1000000;
            $bMeta = Budget::create([
                'campaign_id' => $campaign->id,
                'channel_name' => 'Meta Ads (Facebook & Instagram)',
                'channel_icon' => 'meta',
                'allocated_amount' => $cap * 0.6,
                'spent_amount' => 0,
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'pacing_status' => 'on_track',
                'is_deployed' => true,
                'deployed_at' => now(),
            ]);
            $bGoogle = Budget::create([
                'campaign_id' => $campaign->id,
                'channel_name' => 'Google Ads Local Store Feed',
                'channel_icon' => 'google',
                'allocated_amount' => $cap * 0.4,
                'spent_amount' => 0,
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'pacing_status' => 'on_track',
                'is_deployed' => true,
                'deployed_at' => now(),
            ]);
            $updatedChannels[] = $bMeta->channel_name;
            $updatedChannels[] = $bGoogle->channel_name;
        }

        // Advance campaign to active
        $campaign->update([
            'status' => 'active',
        ]);

        $userName = Auth::user()?->name ?? 'Kevin Parker';

        ActivityLog::log(
            department: 'DPM',
            actionType: 'CAMPAIGN_LAUNCHED',
            title: 'Ads Deployed: ' . $campaign->title,
            description: $userName . ' (DPM Media Buyer) deployed live ad campaigns for "' . $campaign->title . '" across ' . (count($updatedChannels) ? implode(', ', $updatedChannels) : 'active channels') . '. Status upgraded to Active In Market.',
            campaign: $campaign,
            metadata: ['channels' => $updatedChannels]
        );

        return back()->with('success', 'Digital advertising successfully deployed for "' . $campaign->title . '" across ' . (count($updatedChannels) ? implode(', ', $updatedChannels) : 'all active channels') . '! Live media flighting has begun.');
    }

    /**
     * Allocate or restructure campaign media budgets across advertising channels.
     * Takes the macro total budget set by CMO and distributes channel caps (Meta, Google, TikTok, POSM, SMS).
     */
    public function allocateBudget(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'meta_amount' => 'nullable|numeric|min:0',
            'google_amount' => 'nullable|numeric|min:0',
            'tiktok_amount' => 'nullable|numeric|min:0',
            'posm_amount' => 'nullable|numeric|min:0',
            'sms_amount' => 'nullable|numeric|min:0',
        ]);

        $campaign = Campaign::with('budgets')->findOrFail($validated['campaign_id']);

        $channels = [
            [
                'name' => 'Meta Ads (Facebook & Instagram)',
                'icon' => 'meta',
                'amount' => (float)($validated['meta_amount'] ?? 0),
            ],
            [
                'name' => 'Google Ads Local Store Feed',
                'icon' => 'google',
                'amount' => (float)($validated['google_amount'] ?? 0),
            ],
            [
                'name' => 'TikTok In-Feed & Influencer Ads',
                'icon' => 'tiktok',
                'amount' => (float)($validated['tiktok_amount'] ?? 0),
            ],
            [
                'name' => 'In-Store POSM Print & Distribution',
                'icon' => 'posm',
                'amount' => (float)($validated['posm_amount'] ?? 0),
            ],
            [
                'name' => 'SMS Broadcast & WhatsApp Loyalty',
                'icon' => 'sms',
                'amount' => (float)($validated['sms_amount'] ?? 0),
            ],
        ];

        $totalAllocated = 0;
        $updatedChannels = [];

        foreach ($channels as $ch) {
            $amount = $ch['amount'];
            if ($amount <= 0) {
                // If amount is 0 and exists with 0 spend, delete it
                $existing = $campaign->budgets->firstWhere('channel_icon', $ch['icon']);
                if ($existing && $existing->spent_amount == 0) {
                    $existing->delete();
                }
                continue;
            }

            // Find existing by channel_icon or channel_name
            $existing = $campaign->budgets->firstWhere('channel_icon', $ch['icon']) 
                     ?: $campaign->budgets->firstWhere('channel_name', $ch['name']);

            if ($existing) {
                $existing->update([
                    'channel_name' => $ch['name'],
                    'channel_icon' => $ch['icon'],
                    'allocated_amount' => $amount,
                ]);
            } else {
                Budget::create([
                    'campaign_id' => $campaign->id,
                    'channel_name' => $ch['name'],
                    'channel_icon' => $ch['icon'],
                    'allocated_amount' => $amount,
                    'spent_amount' => 0,
                    'impressions' => 0,
                    'clicks' => 0,
                    'conversions' => 0,
                    'pacing_status' => 'pending_deployment',
                    'is_deployed' => false,
                ]);
            }

            $totalAllocated += $amount;
            $updatedChannels[] = $ch['name'] . ' (KES ' . number_format($amount) . ')';
        }

        // Update campaign's allocated_budget
        $campaign->update([
            'allocated_budget' => $campaign->budgets()->sum('allocated_amount'),
        ]);

        $userName = Auth::user()?->name ?? 'Kevin Parker';

        ActivityLog::log(
            department: 'DPM',
            actionType: 'BUDGET_ALLOCATED',
            title: 'Channel Budgets Allocated: ' . $campaign->title,
            description: $userName . ' (DPM Media Buyer) allocated KES ' . number_format($totalAllocated) . ' across ' . count($updatedChannels) . ' channels for "' . $campaign->title . '" (CMO Total Cap: KES ' . number_format($campaign->total_budget) . '). Channels: ' . implode(', ', $updatedChannels) . '.',
            campaign: $campaign,
            metadata: ['channels' => $updatedChannels, 'total_allocated' => $totalAllocated]
        );

        return back()->with('success', 'Media budgets for "' . $campaign->title . '" successfully allocated across ' . count($updatedChannels) . ' channels (Total: KES ' . number_format($totalAllocated) . ')!');
    }

    /**
     * Reallocate budget between channels or campaigns.
     */
    public function reallocateBudget(Request $request)
    {
        $validated = $request->validate([
            'from_budget_id' => 'required|exists:budgets,id',
            'to_budget_id' => 'required|exists:budgets,id|different:from_budget_id',
            'amount' => 'required|numeric|min:5000',
            'reason' => 'nullable|string|max:255',
        ]);

        $fromBudget = Budget::with('campaign')->findOrFail($validated['from_budget_id']);
        $toBudget = Budget::with('campaign')->findOrFail($validated['to_budget_id']);
        $amount = (float) $validated['amount'];

        // Check if source budget has enough remaining allocated funds
        $fromAvailable = $fromBudget->allocated_amount - $fromBudget->spent_amount;
        if ($amount > $fromAvailable) {
            return back()->with('error', 'Cannot reallocate KES ' . number_format($amount) . '. Source channel only has KES ' . number_format($fromAvailable) . ' in unspent funds.');
        }

        $reason = $validated['reason'] ?: 'Performance-based pacing optimization';
        $userName = Auth::user()?->name ?? 'Kevin Parker';
        $userId = Auth::id() ?? 2;

        // Create pending reallocation request awaiting formal CMO approval
        $reallocation = BudgetReallocation::create([
            'from_budget_id' => $fromBudget->id,
            'to_budget_id' => $toBudget->id,
            'amount' => $amount,
            'reason' => $reason,
            'status' => 'pending_cmo_approval',
            'requested_by_user_id' => $userId,
            'requested_by_name' => $userName,
        ]);

        ActivityLog::log(
            department: 'DPM',
            actionType: 'BUDGET_REALLOCATION_REQUESTED',
            title: 'Reallocation Requested: KES ' . number_format($amount) . ' (Awaiting CMO Sign-off)',
            description: $userName . ' requested to reallocate KES ' . number_format($amount) . ' from "' . $fromBudget->channel_name . ' (' . $fromBudget->campaign->title . ')" to "' . $toBudget->channel_name . ' (' . $toBudget->campaign->title . ')". Reason: ' . $reason . '. Awaiting CMO (David Miller) approval before funds are shifted.',
            campaign: $toBudget->campaign,
            metadata: [
                'reallocation_id' => $reallocation->id,
                'amount' => $amount,
                'from_channel' => $fromBudget->channel_name,
                'to_channel' => $toBudget->channel_name,
                'from_campaign' => $fromBudget->campaign->title,
                'to_campaign' => $toBudget->campaign->title,
                'reason' => $reason,
            ]
        );

        return back()->with('success', 'Budget reallocation request of KES ' . number_format($amount) . ' submitted! In accordance with financial governance policy, it is now awaiting formal approval from CMO David Miller before funds are shifted.');
    }

    /**
     * DPM Kevin Parker executes a CMO-authorized budget reallocation.
     * Deducts from source, adds to destination, recalculates campaign totals, and logs execution audit.
     */
    public function executeApprovedReallocation(Request $request, BudgetReallocation $reallocation)
    {
        if (!$reallocation->isApprovedByCmo()) {
            return back()->with('error', 'Cannot execute: This reallocation has not been approved by CMO David Miller.');
        }

        $fromBudget = $reallocation->fromBudget;
        $toBudget = $reallocation->toBudget;
        $amount = (float) $reallocation->amount;

        // Verify source budget still has enough unspent balance
        $fromAvailable = $fromBudget->allocated_amount - $fromBudget->spent_amount;
        if ($amount > $fromAvailable) {
            return back()->with('error', 'Cannot execute: Source channel only has KES ' . number_format($fromAvailable) . ' in remaining unspent funds (requested KES ' . number_format($amount) . ').');
        }

        // Execute live fund transfer
        $fromBudget->decrement('allocated_amount', $amount);
        $toBudget->increment('allocated_amount', $amount);

        // Recalculate campaign budget caches
        $fromBudget->campaign->recalculateBudgets();
        if ($fromBudget->campaign_id !== $toBudget->campaign_id) {
            $toBudget->campaign->recalculateBudgets();
        }

        // Mark reallocation as executed
        $reallocation->update([
            'status' => 'executed',
            'executed_at' => now(),
        ]);

        $userName = Auth::user()?->name ?? 'Kevin Parker (DPM)';

        ActivityLog::log(
            department: 'DPM',
            actionType: 'BUDGET_REALLOCATION_EXECUTED',
            title: 'Budget Shift Executed: KES ' . number_format($amount),
            description: $userName . ' applied CMO-approved shift of KES ' . number_format($amount) . ' from "' . $fromBudget->channel_name . '" to "' . $toBudget->channel_name . '". Ad network spend caps updated live.',
            campaign: $toBudget->campaign,
            metadata: [
                'reallocation_id' => $reallocation->id,
                'amount' => $amount,
                'from_channel' => $fromBudget->channel_name,
                'to_channel' => $toBudget->channel_name,
                'cmo_notes' => $reallocation->cmo_notes,
            ]
        );

        return back()->with('success', 'CMO-approved budget shift of KES ' . number_format($amount) . ' has been successfully executed! Channel spend caps and ad network delivery limits are now live.');
    }
}
