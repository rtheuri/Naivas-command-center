<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Budget;
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

        // Activity Logs
        $activityQuery = ActivityLog::with('user', 'campaign')->latest();
        if ($departmentFilter && in_array($departmentFilter, ['IMC', 'Designer', 'Agency', 'DPM', 'OME', 'Executive'])) {
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

        $userName = Auth::user()?->name ?? 'Peter Mumo (CMO)';

        ActivityLog::log(
            department: 'Executive',
            actionType: 'CAMPAIGN_CREATED',
            title: 'New Campaign Created: ' . $campaign->title,
            description: $userName . ' initialized campaign ' . $campaign->code . ' with budget KES ' . number_format($campaign->total_budget) . '. Sequential workflow started in Planning stage.',
            campaign: $campaign
        );

        return back()->with('success', 'Campaign ' . $campaign->code . ' successfully created and entered into Planning pipeline!');
    }
}
