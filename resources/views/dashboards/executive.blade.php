@extends('layouts.app', ['title' => 'Executive Hub'])

@section('content')
<div class="space-y-6" x-data="{ newCampaignModal: false }">

    <!-- Page Header -->
    <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-[#F7941D] uppercase tracking-wider block mb-1">Centralized Overview</span>
            <h1 class="text-2xl font-bold text-slate-900">Executive Marketing Dashboard</h1>
            <p class="text-xs text-slate-500 mt-1">
                Real-time visibility across IMC creative approvals, DPM media pacing, and OME store rollouts.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <button type="button" @click="newCampaignModal = true" 
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-semibold shadow-xs transition">
                + New Campaign
            </button>
        </div>
    </div>

    <!-- Top Clean Metrics Grid (White & Orange) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Metric 1: Allocated Budget -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 block mb-1">Total Budget Allocated</span>
            <div class="text-2xl font-bold text-slate-900">KES {{ number_format($allocatedBudget) }}</div>
            <div class="mt-2 text-xs text-slate-400">
                Approved Cap: <span class="font-medium text-slate-600">KES {{ number_format($totalBudget) }}</span>
            </div>
        </div>

        <!-- Metric 2: Spent to Date & Burn Rate -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-slate-500">Spent to Date</span>
                <span class="text-xs font-bold text-[#F7941D]">{{ $overallBurnRate }}%</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">KES {{ number_format($spentBudget) }}</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-[#F7941D] h-1.5 rounded-full" style="width: {{ $overallBurnRate }}%"></div>
            </div>
        </div>

        <!-- Metric 3: Store Execution Rate -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-slate-500">Store Rollout Proof</span>
                <span class="text-xs font-bold text-slate-700">{{ $executedTasks }} / {{ $totalTasks }} Tasks</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $storeExecutionRate }}%</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-slate-800 h-1.5 rounded-full" style="width: {{ $storeExecutionRate }}%"></div>
            </div>
        </div>

        <!-- Metric 4: Campaign Pipeline -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <span class="text-xs font-medium text-slate-500 block mb-1">Active Pipeline</span>
            <div class="text-2xl font-bold text-slate-900">{{ $campaigns->count() }} Initiatives</div>
            <div class="mt-2 flex items-center space-x-2 text-xs text-slate-600">
                <span>{{ $statusCounts['active'] }} Active</span>
                <span>•</span>
                <span>{{ $statusCounts['ready_for_launch'] }} Ready</span>
                <span>•</span>
                <span>{{ $statusCounts['pending_approval'] }} In Review</span>
            </div>
        </div>

    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Chart 1: Budget Allocation vs Spent (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Budget vs. Actual Spend by Campaign</h3>
                    <p class="text-xs text-slate-500">Comparing approved caps to current expenditure (KES)</p>
                </div>
                <div class="flex items-center space-x-4 text-xs text-slate-600">
                    <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-sm bg-slate-300 mr-1.5"></span>Allocated</span>
                    <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-sm bg-[#F7941D] mr-1.5"></span>Spent</span>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="campaignBudgetChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Channel Spend Distribution (1 col) -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Spend by Media Channel</h3>
                <p class="text-xs text-slate-500">Distribution across active media channels</p>
                <div class="h-56 w-full mt-2 relative flex items-center justify-center">
                    <canvas id="channelSpendChart"></canvas>
                </div>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 text-center text-xs text-slate-500">
                Pacing optimized weekly across channels
            </div>
        </div>

    </div>

    <!-- Campaigns Master Table & Live Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Campaigns Master Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">Campaign Master Records</h3>
                <span class="text-xs text-slate-500">{{ $campaigns->count() }} initiatives</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                    <thead class="text-slate-500 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Campaign</th>
                            <th class="py-2.5 px-3">Category</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Budget Progress</th>
                            <th class="py-2.5 px-3">Store Tasks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($campaigns as $campaign)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-3">
                                <div class="font-semibold text-slate-900">{{ $campaign->title }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $campaign->code }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $campaign->category }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium {{ $campaign->status === 'active' ? 'bg-orange-50 text-[#F7941D] border border-orange-200' : ($campaign->status === 'ready_for_launch' ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-600 border border-slate-200') }}">
                                    {{ $campaign->getStatusDisplayName() }}
                                </span>
                            </td>
                            <td class="py-3 px-3 min-w-[150px]">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-medium text-slate-900">KES {{ number_format($campaign->spent_budget) }}</span>
                                    <span class="text-slate-400">{{ $campaign->burn_rate }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-[#F7941D] h-1.5 rounded-full" style="width: {{ $campaign->burn_rate }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap text-slate-600">
                                {{ $campaign->storeTasks->whereIn('status', ['executed_pending_verification', 'verified'])->count() }} / {{ $campaign->storeTasks->count() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Clean Audit Activity Feed (1 col) -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex flex-col h-full">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-900">Department Activity Log</h3>
                <span class="text-xs text-slate-400">Live</span>
            </div>

            <!-- Clean Filter Tabs -->
            <div class="flex items-center space-x-1 border-b border-slate-100 pb-2 mb-3 text-xs">
                <a href="{{ route('executive.dashboard') }}" class="px-2.5 py-1 rounded-md font-medium {{ empty($departmentFilter) ? 'bg-slate-100 text-slate-900' : 'text-slate-500 hover:text-slate-800' }}">All</a>
                <a href="{{ route('executive.dashboard', ['department' => 'IMC']) }}" class="px-2.5 py-1 rounded-md font-medium {{ $departmentFilter === 'IMC' ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-500 hover:text-slate-800' }}">IMC</a>
                <a href="{{ route('executive.dashboard', ['department' => 'DPM']) }}" class="px-2.5 py-1 rounded-md font-medium {{ $departmentFilter === 'DPM' ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-500 hover:text-slate-800' }}">DPM</a>
                <a href="{{ route('executive.dashboard', ['department' => 'OME']) }}" class="px-2.5 py-1 rounded-md font-medium {{ $departmentFilter === 'OME' ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-500 hover:text-slate-800' }}">OME</a>
            </div>

            <!-- Feed List -->
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 max-h-[440px]">
                @forelse($activityLogs as $log)
                <div class="p-3 rounded-lg border border-slate-100 hover:border-slate-200 bg-slate-50/50 transition text-xs">
                    <div class="flex items-center justify-between text-[11px] mb-1">
                        <span class="font-semibold text-slate-800">{{ $log->user_name }} ({{ $log->department }})</span>
                        <span class="text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-slate-700 leading-snug">{{ $log->description }}</p>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    No activity recorded for this filter.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Create Campaign Modal -->
    <div x-show="newCampaignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="newCampaignModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Initiate New Campaign</h3>
                <button type="button" @click="newCampaignModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('executive.campaigns.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Campaign Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Easter Holiday Mega Savings" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="Fresh Produce">Fresh Produce</option>
                            <option value="Groceries & FMCG">Groceries & FMCG</option>
                            <option value="Bakery & Deli">Bakery & Deli</option>
                            <option value="Household & Electronics">Household & Electronics</option>
                            <option value="Loyalty & Festive">Loyalty & Festive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Total Budget (KES) *</label>
                        <input type="number" name="total_budget" step="50000" min="50000" required placeholder="3500000" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Audience</label>
                        <input type="text" name="target_audience" placeholder="Nairobi Family Shoppers" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Start Date *</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">End Date *</label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+14 days')) }}" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief objectives..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="newCampaignModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($chartData);

    // 1. Budget Chart: Clean Dual-Bar (Slate-200 vs Orange)
    const ctxBudget = document.getElementById('campaignBudgetChart');
    if (ctxBudget) {
        new window.Chart(ctxBudget, {
            type: 'bar',
            data: {
                labels: chartData.campaignLabels.map(l => l.length > 18 ? l.substring(0, 18) + '...' : l),
                datasets: [
                    {
                        label: 'Allocated (KES)',
                        data: chartData.allocated,
                        backgroundColor: '#e2e8f0', // soft neutral slate
                        hoverBackgroundColor: '#cbd5e1',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Spent (KES)',
                        data: chartData.spent,
                        backgroundColor: '#F7941D', // Naivas orange
                        hoverBackgroundColor: '#e07f0f',
                        borderRadius: 4,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': KES ' + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KES ' + (value / 1000000).toFixed(1) + 'M';
                            },
                            font: { size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Channel Spend Donut: Clean White & Orange Palette
    const ctxChannel = document.getElementById('channelSpendChart');
    if (ctxChannel) {
        new window.Chart(ctxChannel, {
            type: 'doughnut',
            data: {
                labels: chartData.channelLabels,
                datasets: [{
                    data: chartData.channelValues,
                    backgroundColor: [
                        '#F7941D', // Orange
                        '#fb923c', // Warm light orange
                        '#0f172a', // Dark slate
                        '#64748b', // Slate gray
                        '#cbd5e1', // Light slate
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 8,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': KES ' + Number(context.raw).toLocaleString();
                            }
                        }
                    }
                },
                cutout: '72%',
            }
        });
    }
});
</script>
@endpush
