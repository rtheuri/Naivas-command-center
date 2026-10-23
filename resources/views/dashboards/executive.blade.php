@extends('layouts.app', ['title' => 'Executive Hub'])

@section('content')
<div class="space-y-6" x-data="{ newCampaignModal: false }">

    <!-- Page Header & Persona Greeting -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                    Marketing Lead Command
                </span>
                <span class="text-xs text-slate-500">Naivas Supermarket HQ</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Executive Marketing Dashboard</h1>
            <p class="text-sm text-slate-600 mt-0.5">
                Centralized visibility across IMC Creative Sign-Offs, DPM Media Spend Pacing, and OME Store Rollouts.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <button type="button" @click="newCampaignModal = true" 
                    class="inline-flex items-center px-4 py-2.5 rounded-xl bg-[#006837] hover:bg-[#005a30] text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-950/20 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Initiate Campaign
            </button>
        </div>
    </div>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Metric 1: Total Budget Allocated -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Allocated Budget</span>
                <span class="p-2 rounded-lg bg-emerald-50 text-[#006837]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">KES {{ number_format($allocatedBudget) }}</div>
            <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                <span>Total Approved Cap:</span>
                <span class="font-semibold text-slate-700">KES {{ number_format($totalBudget) }}</span>
            </div>
        </div>

        <!-- Metric 2: Spent to Date & Burn Rate -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Media Burn Rate</span>
                <span class="p-2 rounded-lg bg-amber-50 text-[#F7941D]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">KES {{ number_format($spentBudget) }}</div>
            <div class="mt-2 flex items-center justify-between">
                <div class="w-2/3 bg-gray-100 rounded-full h-2 overflow-hidden mr-2">
                    <div class="bg-[#F7941D] h-2 rounded-full" style="width: {{ $overallBurnRate }}%"></div>
                </div>
                <span class="text-xs font-bold text-amber-700">{{ $overallBurnRate }}% Spent</span>
            </div>
        </div>

        <!-- Metric 3: Store Execution Rate -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Store Rollout Proof</span>
                <span class="p-2 rounded-lg bg-purple-50 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $storeExecutionRate }}%</div>
            <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                <span>Executed / Verified:</span>
                <span class="font-semibold text-emerald-700">{{ $executedTasks }} of {{ $totalTasks }} Tasks</span>
            </div>
        </div>

        <!-- Metric 4: Campaign Status Distribution -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Pipeline</span>
                <span class="p-2 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $campaigns->count() }} Campaigns</div>
            <div class="mt-2 flex items-center space-x-1.5 text-[11px] font-semibold">
                <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Active In Market">{{ $statusCounts['active'] }} Active</span>
                <span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800" title="Ready for Launch">{{ $statusCounts['ready_for_launch'] }} Ready</span>
                <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" title="Pending IMC Sign-Off">{{ $statusCounts['pending_approval'] }} In Review</span>
            </div>
        </div>

    </div>

    <!-- Charts & Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart 1: Budget Allocation vs Spent (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Campaign Budget Allocation vs. Spend</h3>
                    <p class="text-xs text-slate-500">Compares approved budget caps against live media and POSM spend (in KES)</p>
                </div>
                <div class="flex items-center space-x-3 text-xs">
                    <span class="flex items-center"><span class="w-3 h-3 rounded bg-[#006837] mr-1.5"></span>Allocated</span>
                    <span class="flex items-center"><span class="w-3 h-3 rounded bg-[#F7941D] mr-1.5"></span>Spent to Date</span>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full">
                <canvas id="campaignBudgetChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Channel Spend Distribution (1 col) -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-gray-200/80 flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Spend by Media Channel</h3>
                <p class="text-xs text-slate-500">Distribution across Meta, Google, TikTok, SMS, POSM</p>
                <div class="h-56 sm:h-64 w-full mt-2 relative flex items-center justify-center">
                    <canvas id="channelSpendChart"></canvas>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                <span class="text-xs text-slate-500 font-medium">Highest Performing: <strong class="text-[#006837]">Meta Ads & In-Store POSM</strong></span>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Campaigns Table & Real-Time Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Campaigns Master Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-xs border border-gray-200/80">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Active Campaign Master Records</h3>
                    <p class="text-xs text-slate-500">Sequential status tracking across all 5 pre-seeded marketing initiatives</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    5 Campaigns
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-slate-600 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-3">Campaign</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Budget Progress</th>
                            <th class="py-3 px-3">Store Tasks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-slate-700 font-medium">
                        @foreach($campaigns as $campaign)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900 text-sm">{{ $campaign->title }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $campaign->code }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px]">
                                    {{ $campaign->category }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $campaign->getStatusBadgeClasses() }}">
                                    {{ $campaign->getStatusDisplayName() }}
                                </span>
                            </td>
                            <td class="py-3 px-3 min-w-[150px]">
                                <div class="flex items-center justify-between text-[11px] mb-1">
                                    <span class="font-bold text-slate-900">KES {{ number_format($campaign->spent_budget) }}</span>
                                    <span class="text-slate-400">/ {{ number_format($campaign->total_budget) }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-[#006837] to-[#F7941D] h-1.5 rounded-full" style="width: {{ $campaign->burn_rate }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-xs font-semibold text-slate-800">{{ $campaign->storeTasks->whereIn('status', ['executed_pending_verification', 'verified'])->count() }} / {{ $campaign->storeTasks->count() }}</span>
                                    @if($campaign->storeTasks->where('status', 'executed_pending_verification')->count() > 0)
                                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-ping" title="Proof uploaded awaiting verification"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Real-Time Cross-Department Activity Feed (1 col) -->
        <div class="bg-white rounded-2xl p-6 shadow-xs border border-gray-200/80 flex flex-col h-full">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Live Activity Feed</h3>
                    <p class="text-xs text-slate-500">Actions across IMC, DPM & OME</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <!-- Department Filter Pills -->
            <div class="flex items-center space-x-1.5 overflow-x-auto pb-2 text-[11px] border-b border-gray-100 mb-3">
                <a href="{{ route('executive.dashboard') }}" class="px-2.5 py-1 rounded-lg font-semibold {{ empty($departmentFilter) ? 'bg-[#006837] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">All</a>
                <a href="{{ route('executive.dashboard', ['department' => 'IMC']) }}" class="px-2.5 py-1 rounded-lg font-semibold {{ $departmentFilter === 'IMC' ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">IMC</a>
                <a href="{{ route('executive.dashboard', ['department' => 'DPM']) }}" class="px-2.5 py-1 rounded-lg font-semibold {{ $departmentFilter === 'DPM' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">DPM</a>
                <a href="{{ route('executive.dashboard', ['department' => 'OME']) }}" class="px-2.5 py-1 rounded-lg font-semibold {{ $departmentFilter === 'OME' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">OME</a>
            </div>

            <!-- Feed Items -->
            <div class="flex-1 overflow-y-auto space-y-3.5 pr-1 max-h-[460px]">
                @forelse($activityLogs as $log)
                <div class="p-3 rounded-xl bg-slate-50 border border-gray-200/60 hover:bg-emerald-50/30 transition text-xs">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center space-x-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $log->getDepartmentBadgeClasses() }}">
                                {{ $log->department }}
                            </span>
                            <span class="font-bold text-slate-800">{{ $log->user_name }}</span>
                        </div>
                        <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="font-semibold text-slate-900 text-xs mb-1">{{ $log->title }}</div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">{{ $log->description }}</p>

                    @if($log->campaign)
                    <div class="mt-2 pt-1.5 border-t border-gray-200/50 text-[10px] text-slate-500 flex items-center justify-between">
                        <span class="font-medium text-emerald-800">{{ $log->campaign->title }}</span>
                        <span class="text-slate-400 font-mono">{{ $log->campaign->code }}</span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">
                    No activity recorded for this department filter yet.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Create Campaign Modal -->
    <div x-show="newCampaignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="newCampaignModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#006837] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Initiate New Marketing Campaign</h3>
                </div>
                <button type="button" @click="newCampaignModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('executive.campaigns.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campaign Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Easter Holiday Mega Savings" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none bg-white">
                            <option value="Fresh Produce">Fresh Produce</option>
                            <option value="Groceries & FMCG">Groceries & FMCG</option>
                            <option value="Bakery & Deli">Bakery & Deli</option>
                            <option value="Household & Electronics">Household & Electronics</option>
                            <option value="Loyalty & Festive">Loyalty & Festive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none bg-white">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Total Budget (KES) *</label>
                        <input type="number" name="total_budget" step="50000" min="50000" required placeholder="3500000" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Audience</label>
                        <input type="text" name="target_audience" placeholder="Nairobi Family Shoppers" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Start Date *</label>
                        <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">End Date *</label>
                        <input type="date" name="end_date" required value="{{ date('Y-m-d', strtotime('+14 days')) }}" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Strategic Description</label>
                    <textarea name="description" rows="2" placeholder="Brief objectives, expected turnover uplift, and key product categories..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="newCampaignModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold rounded-lg bg-[#006837] text-white hover:bg-[#005a30] shadow">
                        Launch Planning Stage
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

    // 1. Budget Chart (Dual Bar)
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
                        backgroundColor: '#006837',
                        borderRadius: 6,
                        barPercentage: 0.7,
                    },
                    {
                        label: 'Spent (KES)',
                        data: chartData.spent,
                        backgroundColor: '#F7941D',
                        borderRadius: 6,
                        barPercentage: 0.7,
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
                            }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Channel Spend Donut Chart
    const ctxChannel = document.getElementById('channelSpendChart');
    if (ctxChannel) {
        new window.Chart(ctxChannel, {
            type: 'doughnut',
            data: {
                labels: chartData.channelLabels,
                datasets: [{
                    data: chartData.channelValues,
                    backgroundColor: [
                        '#1877F2', // Meta Blue
                        '#EA4335', // Google Red
                        '#000000', // TikTok Black
                        '#006837', // Naivas Green
                        '#F7941D', // Naivas Orange
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
                            boxWidth: 10,
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
                cutout: '68%',
            }
        });
    }
});
</script>
@endpush
