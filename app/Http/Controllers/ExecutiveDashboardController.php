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
        if ($departmentFilter && in_array($departmentFilter, ['IMC', 'DPM', 'OME', 'Executive'])) {
            $activityQuery->where('department', $departmentFilter);
        }
        $activityLogs = $activityQuery->take(15)->get();

        // Chart Data Prep
        $campaignChartLabels = $campaigns->pluck('title')->toArray();
        $campaignChartAllocated = $campaigns->pluck('allocated_budget')->toArray();
        $campaignChartSpent = $campaigns->pluck('spent_budget')->toArray();

        // Channel Spend Aggregation
        $channels = Budget::selectRaw('channel_name, SUM(spent_amount) as total_spent')
            ->groupBy('channel_name')
            ->pluck('total_spent', 'channel_name')
            ->toArray();

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

        ActivityLog::log(
            department: 'Executive',
            actionType: 'CAMPAIGN_CREATED',
            title: 'New Campaign Created: ' . $campaign->title,
            description: Auth::user()->name . ' initialized campaign ' . $campaign->code . ' with budget KES ' . number_format($campaign->total_budget) . '. Sequential workflow started in Planning stage.',
            campaign: $campaign
        );

        return back()->with('success', 'Campaign ' . $campaign->code . ' successfully created and entered into Planning pipeline!');
    }
}
