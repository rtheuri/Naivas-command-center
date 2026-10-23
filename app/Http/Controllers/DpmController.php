<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Budget;
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

        return view('dashboards.dpm', [
            'campaigns' => $campaigns,
            'allBudgets' => $allBudgets,
            'totalAllocated' => $totalAllocated,
            'totalSpent' => $totalSpent,
            'totalImpressions' => $totalImpressions,
            'totalClicks' => $totalClicks,
            'totalConversions' => $totalConversions,
            'blendedCtr' => $blendedCtr,
            'activeChannelsCount' => $activeChannelsCount,
            'channelStats' => $channelStats,
        ]);
    }

    /**
     * Deploy live advertising channels for a campaign.
     * Enforces strict sequential gating: requires campaign status to be approved_ready_for_media!
     */
    public function deployAds(Request $request, Campaign $campaign)
    {
        // Strict Gating Check: Ensure campaign is in ready_for_launch / approved_ready_for_media status
        $isReady = in_array($campaign->status, ['ready_for_launch', 'approved_ready_for_media']);

        if (!$isReady) {
            return back()->with('error', 'Strict Gating Locked: Cannot deploy advertising for "' . $campaign->title . '". Campaign status is currently "' . $campaign->status . '". It must be approved_ready_for_media (IMC creative asset sign-off required).');
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

        // Advance campaign to active
        $campaign->update([
            'status' => 'active',
        ]);

        ActivityLog::log(
            department: 'DPM',
            actionType: 'CAMPAIGN_LAUNCHED',
            title: 'Ads Deployed: ' . $campaign->title,
            description: Auth::user()->name . ' (DPM Media Buyer) deployed live ad campaigns for "' . $campaign->title . '" across ' . (count($updatedChannels) ?: 'all active') . ' channels. Status upgraded to Active In Market.',
            campaign: $campaign,
            metadata: ['channels' => $updatedChannels]
        );

        return back()->with('success', 'Digital advertising successfully deployed for "' . $campaign->title . '". Live delivery has begun.');
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

        // Execute transfer
        $fromBudget->decrement('allocated_amount', $amount);
        $toBudget->increment('allocated_amount', $amount);

        // Recalculate parent campaign totals
        $fromBudget->campaign->recalculateBudgets();
        if ($fromBudget->campaign_id !== $toBudget->campaign_id) {
            $toBudget->campaign->recalculateBudgets();
        }

        $reason = $validated['reason'] ?: 'Performance-based pacing optimization';

        ActivityLog::log(
            department: 'DPM',
            actionType: 'BUDGET_REALLOCATED',
            title: 'Media Funds Reallocated (KES ' . number_format($amount) . ')',
            description: Auth::user()->name . ' shifted KES ' . number_format($amount) . ' from "' . $fromBudget->channel_name . ' (' . $fromBudget->campaign->title . ')" to "' . $toBudget->channel_name . ' (' . $toBudget->campaign->title . ')". Reason: ' . $reason,
            campaign: $toBudget->campaign,
            metadata: [
                'amount' => $amount,
                'from_channel' => $fromBudget->channel_name,
                'to_channel' => $toBudget->channel_name,
                'from_campaign' => $fromBudget->campaign->title,
                'to_campaign' => $toBudget->campaign->title,
            ]
        );

        return back()->with('success', 'Successfully reallocated KES ' . number_format($amount) . ' from ' . $fromBudget->channel_name . ' to ' . $toBudget->channel_name . '.');
    }
}
