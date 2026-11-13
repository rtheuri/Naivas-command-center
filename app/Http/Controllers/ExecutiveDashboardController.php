<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Budget;
use App\Models\BudgetReallocation;
use App\Models\Campaign;
use App\Models\StoreTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExecutiveDashboardController extends Controller
{
    /**
     * Display the Executive (Marketing Lead) Centralized Dashboard.
     */
    public function index(Request $request)
    {
        $departmentFilter = $request->query('department');

        $campaigns = Campaign::with(['briefs', 'assets', 'budgets', 'storeTasks'])
            ->orderByRaw("CASE 
                WHEN status = 'active' THEN 1 
                WHEN status = 'ready_for_launch' THEN 2 
                WHEN status = 'pending_approval' THEN 3 
                WHEN status = 'planning' THEN 4 
                ELSE 5 END")
            ->orderBy('id', 'desc')
            ->get();

        // Calculate Global KPI Totals
        $totalBudget = $campaigns->sum('total_budget');
        $allocatedBudget = $campaigns->sum('allocated_budget');
        $spentBudget = $campaigns->sum('spent_budget');
        $overallBurnRate = $totalBudget > 0 ? round(($spentBudget / $totalBudget) * 100, 1) : 0;

        $totalTasks = StoreTask::count();
        $executedTasks = StoreTask::whereIn('status', ['executed_pending_verification', 'verified'])->count();
        $storeExecutionRate = $totalTasks > 0 ? round(($executedTasks / $totalTasks) * 100, 1) : 0;

        $statusCounts = [
            'planning' => $campaigns->where('status', 'planning')->count(),
            'pending_approval' => $campaigns->where('status', 'pending_approval')->count(),
            'ready_for_launch' => $campaigns->where('status', 'ready_for_launch')->count(),
            'active' => $campaigns->where('status', 'active')->count(),
            'completed' => $campaigns->where('status', 'completed')->count(),
        ];

        // Activity Logs: Exclude executive self-actions to prevent duplicate activity entries in the feed
        $activityQuery = ActivityLog::with('user', 'campaign')
            ->where('department', '!=', 'Executive')
            ->where(function ($q) {
                $q->whereNull('user_role')
                  ->orWhere(function ($sub) {
                      $sub->where('user_role', 'not like', '%Executive%')
                          ->where('user_role', 'not like', '%Chief Marketing Officer%');
                  });
            })
            ->latest();

        if ($departmentFilter && in_array($departmentFilter, ['IMC', 'Designer', 'Agency', 'DPM', 'OME'])) {
            $activityQuery->where('department', $departmentFilter);
        }
        $activityLogs = $activityQuery->take(20)->get();

        // Chart Data Prep
        $campaignChartLabels = $campaigns->pluck('title')->toArray();
        $campaignChartAllocated = $campaigns->pluck('allocated_budget')->toArray();
        $campaignChartSpent = $campaigns->pluck('spent_budget')->toArray();

        // Channel Spend Aggregation
        $channels = Budget::selectRaw('channel_name, SUM(spent_amount) as total_spent')
            ->groupBy('channel_name')
            ->pluck('total_spent', 'channel_name')
            ->toArray();

        // Budget Reallocations requiring Executive CMO Approval
        $pendingReallocations = BudgetReallocation::with(['fromBudget.campaign', 'toBudget.campaign', 'requestedBy'])
            ->where('status', 'pending_cmo_approval')
            ->latest()
            ->get();

        $recentReallocations = BudgetReallocation::with(['fromBudget.campaign', 'toBudget.campaign', 'approvedBy'])
            ->where('status', '!=', 'pending_cmo_approval')
            ->latest()
            ->take(5)
            ->get();

        // BI Waterfall Data: Pipeline Cycle-Time by Stage (in Days)
        $waterfallLabels = [
            '1. Planning & Brief',
            '2. Creative & Copy',
            '3. IMC Approval',
            '4. Media Setup',
            '5. Store Rollout',
            'Total Velocity'
        ];
        $waterfallDays = [1.6, 2.3, 0.8, 0.5, 1.8, 7.0];

        // BI Multi-Series Time Series Data: Weekly Burn vs Allocation Corridor (in Millions KES)
        $timeSeriesLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6 (Current)'];
        $cumulativeAllocated = [3.5, 7.2, 12.0, 16.5, 20.0, round($totalBudget / 1000000, 1)];
        $cumulativeSpent = [1.8, 4.6, 8.9, 13.2, 16.1, round($spentBudget / 1000000, 1)];
        $pacingTarget = [1.6, 4.2, 8.5, 12.8, 16.2, 19.5];

        return view('dashboards.executive', [
            'campaigns' => $campaigns,
            'totalBudget' => $totalBudget,
            'allocatedBudget' => $allocatedBudget,
            'spentBudget' => $spentBudget,
            'overallBurnRate' => $overallBurnRate,
            'totalTasks' => $totalTasks,
            'executedTasks' => $executedTasks,
            'storeExecutionRate' => $storeExecutionRate,
            'statusCounts' => $statusCounts,
            'activityLogs' => $activityLogs,
            'departmentFilter' => $departmentFilter,
            'pendingReallocations' => $pendingReallocations,
            'recentReallocations' => $recentReallocations,
            'chartData' => [
                'campaignLabels' => $campaignChartLabels,
                'allocated' => $campaignChartAllocated,
                'spent' => $campaignChartSpent,
                'channelLabels' => array_keys($channels),
                'channelValues' => array_values($channels),
                'waterfallLabels' => $waterfallLabels,
                'waterfallDays' => $waterfallDays,
                'timeSeriesLabels' => $timeSeriesLabels,
                'cumulativeAllocated' => $cumulativeAllocated,
                'cumulativeSpent' => $cumulativeSpent,
                'pacingTarget' => $pacingTarget,
            ],
        ]);
    }

    /**
     * Create a new campaign from Executive dashboard modal.
     */
    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'total_budget' => 'required|numeric|min:10000',
            'target_audience' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $count = Campaign::count() + 1;
        $code = 'CMP-2026-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $campaign = Campaign::create([
            'code' => $code,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'planning',
            'total_budget' => $validated['total_budget'],
            'allocated_budget' => 0,
            'spent_budget' => 0,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'target_audience' => $validated['target_audience'] ?? 'Naivas Shoppers',
            'description' => $validated['description'],
        ]);

        $userName = Auth::user()?->name ?? 'David Miller (CMO)';

        ActivityLog::log(
            department: 'Executive',
            actionType: 'CAMPAIGN_CREATED',
            title: 'New Campaign Created: ' . $campaign->title,
            description: $userName . ' initialized campaign ' . $campaign->code . ' with budget KES ' . number_format($campaign->total_budget) . '. Sequential workflow started in Planning stage.',
            campaign: $campaign
        );

        return back()->with('success', 'Campaign ' . $campaign->code . ' successfully created and entered into Planning pipeline!');
    }

    /**
     * Executive verifies and officially approves an in-store display task proof.
     */
    public function verifyStoreTask(Request $request, StoreTask $task)
    {
        $user = Auth::user();
        $userName = $user?->name ?? 'David Miller (CMO)';
        $userId = $user?->id ?? 4;

        $task->update([
            'status' => 'verified',
            'verified_by_user_id' => $userId,
            'verified_at' => now(),
        ]);

        ActivityLog::log(
            department: 'Executive',
            actionType: 'STORE_VERIFIED',
            title: 'Store Execution Verified: ' . $task->branch_name,
            description: $userName . ' inspected photographic proof and verified in-store execution for "' . $task->task_name . '" at ' . $task->branch_name . '. Display is officially verified live.',
            campaign: $task->campaign,
            metadata: [
                'task_id' => $task->id,
                'branch' => $task->branch_name,
                'task_name' => $task->task_name,
            ]
        );

        return back()->with('success', 'Store execution for "' . $task->task_name . '" at ' . $task->branch_name . ' has been officially verified and approved live by CMO ' . $userName . '!');
    }

    /**
     * Executive flags in-store proof as insufficient and sends redo instructions back to OME store manager.
     */
    public function requestRedo(Request $request, StoreTask $task)
    {
        $validated = $request->validate([
            'redo_instructions' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $userName = $user?->name ?? 'David Miller (CMO)';
        $userId = $user?->id ?? 4;

        $task->update([
            'status' => 'redo_requested',
            'redo_instructions' => $validated['redo_instructions'],
            'redo_requested_at' => now(),
            'redo_requested_by_user_id' => $userId,
        ]);

        ActivityLog::log(
            department: 'Executive',
            actionType: 'REDO_REQUESTED',
            title: 'Proof Insufficient - Redo Requested: ' . $task->branch_name,
            description: $userName . ' flagged proof for "' . $task->task_name . '" as insufficient: "' . $validated['redo_instructions'] . '". Redo instructions dispatched to OME Store Manager.',
            campaign: $task->campaign,
            metadata: [
                'task_id' => $task->id,
                'branch' => $task->branch_name,
                'task_name' => $task->task_name,
                'redo_instructions' => $validated['redo_instructions'],
            ]
        );

        return back()->with('warning', 'Redo requested for "' . $task->task_name . '" at ' . $task->branch_name . '. Feedback and instructions have been routed to the OME Store Manager.');
    }

    /**
     * Executive CMO formally approves a pending budget reallocation requested by DPM.
     */
    public function approveBudgetReallocation(Request $request, BudgetReallocation $reallocation)
    {
        if ($reallocation->status !== 'pending_cmo_approval') {
            return back()->with('error', 'This budget reallocation request is no longer pending approval.');
        }

        $fromBudget = $reallocation->fromBudget;
        $toBudget = $reallocation->toBudget;
        $amount = (float) $reallocation->amount;

        // Verify source budget still has enough unspent funds
        $fromAvailable = $fromBudget->allocated_amount - $fromBudget->spent_amount;
        if ($amount > $fromAvailable) {
            return back()->with('error', 'Cannot approve: Source channel now only has KES ' . number_format($fromAvailable) . ' in unspent funds (requested KES ' . number_format($amount) . ').');
        }

        // Execute live fund transfer
        $fromBudget->decrement('allocated_amount', $amount);
        $toBudget->increment('allocated_amount', $amount);

        // Recalculate parent campaign totals
        $fromBudget->campaign->recalculateBudgets();
        if ($fromBudget->campaign_id !== $toBudget->campaign_id) {
            $toBudget->campaign->recalculateBudgets();
        }

        $user = Auth::user();
        $userName = $user?->name ?? 'David Miller (CMO)';
        $userId = $user?->id ?? 4;

        $reallocation->update([
            'status' => 'approved',
            'approved_by_user_id' => $userId,
            'responded_at' => now(),
        ]);

        ActivityLog::log(
            department: 'Executive',
            actionType: 'BUDGET_REALLOCATION_APPROVED',
            title: 'Budget Reallocation Approved by CMO (KES ' . number_format($amount) . ')',
            description: $userName . ' authorized DPM Kevin Parker\'s request to shift KES ' . number_format($amount) . ' from "' . $fromBudget->channel_name . ' (' . $fromBudget->campaign->title . ')" to "' . $toBudget->channel_name . ' (' . $toBudget->campaign->title . ')". Reason: ' . $reallocation->reason . '. Media spend limits updated live.',
            campaign: $toBudget->campaign,
            metadata: [
                'reallocation_id' => $reallocation->id,
                'amount' => $amount,
                'from_channel' => $fromBudget->channel_name,
                'to_channel' => $toBudget->channel_name,
                'from_campaign' => $fromBudget->campaign->title,
                'to_campaign' => $toBudget->campaign->title,
            ]
        );

        return back()->with('success', 'Budget reallocation of KES ' . number_format($amount) . ' officially approved by CMO ' . $userName . '! Live channel caps have been updated.');
    }

    /**
     * Executive CMO rejects a pending budget reallocation requested by DPM.
     */
    public function rejectBudgetReallocation(Request $request, BudgetReallocation $reallocation)
    {
        if ($reallocation->status !== 'pending_cmo_approval') {
            return back()->with('error', 'This budget reallocation request is no longer pending approval.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $userName = $user?->name ?? 'David Miller (CMO)';
        $userId = $user?->id ?? 4;

        $rejectionReason = $validated['rejection_reason'] ?: 'Shift exceeds departmental quarterly tolerance; maintain current retail flighting plan.';

        $reallocation->update([
            'status' => 'rejected',
            'approved_by_user_id' => $userId,
            'rejection_reason' => $rejectionReason,
            'responded_at' => now(),
        ]);

        ActivityLog::log(
            department: 'Executive',
            actionType: 'BUDGET_REALLOCATION_REJECTED',
            title: 'Budget Reallocation Declined by CMO (KES ' . number_format($reallocation->amount) . ')',
            description: $userName . ' declined DPM\'s budget shift of KES ' . number_format($reallocation->amount) . ' from "' . $reallocation->fromBudget->channel_name . '" to "' . $reallocation->toBudget->channel_name . '". Reason: ' . $rejectionReason,
            campaign: $reallocation->toBudget->campaign,
            metadata: [
                'reallocation_id' => $reallocation->id,
                'amount' => $reallocation->amount,
                'rejection_reason' => $rejectionReason,
            ]
        );

        return back()->with('warning', 'Budget reallocation request of KES ' . number_format($reallocation->amount) . ' was rejected by CMO. Original channel budgets remain intact.');
    }
}
