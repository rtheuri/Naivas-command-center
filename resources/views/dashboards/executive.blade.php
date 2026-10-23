@extends('layouts.app', ['title' => 'Executive Hub • Power BI Simulation'])

@section('content')
<div class="space-y-6" x-data="{ newCampaignModal: false, activePbiTab: 'bi_overview' }">

    <!-- Embedded Power BI Header / Control Ribbon -->
    <div class="bg-slate-900 text-white rounded-xl p-4 shadow-sm border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-amber-400 text-slate-950 flex items-center justify-center font-black text-sm shadow-xs">
                BI
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold tracking-tight text-white uppercase">Microsoft Power BI Embedded</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>DirectQuery: Live</span>
                    </span>
                </div>
                <p class="text-[11px] text-slate-400">Naivas Marketing Intelligence Cockpit • Cross-Department Enterprise Analytics</p>
            </div>
        </div>

        <div class="flex items-center space-x-2 text-xs">
            <!-- Simulated Power BI Toolbar Buttons -->
            <button type="button" @click="location.reload()" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition flex items-center space-x-1 border border-slate-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Refresh (5s)</span>
            </button>
            <button type="button" @click="newCampaignModal = true" 
                    class="px-3.5 py-1.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white font-bold transition shadow-xs">
                + New Campaign
            </button>
        </div>
    </div>

    <!-- Power BI KPI Scorecards Ribbon -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Scorecard 1: Total Allocated -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span>Total Media Allocation</span>
                <span class="text-[10px] font-bold text-slate-400">YTD Cap</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900">KES {{ number_format($allocatedBudget) }}</div>
            <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                <span>Approved Ceiling:</span>
                <span class="font-bold text-slate-700">KES {{ number_format($totalBudget) }}</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-200"></div>
        </div>

        <!-- Scorecard 2: Spent & Burn Rate -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span>Actual Spend Pacing</span>
                <span class="text-xs font-bold text-[#F7941D]">{{ $overallBurnRate }}%</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900">KES {{ number_format($spentBudget) }}</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-[#F7941D] h-1.5 rounded-full" style="width: {{ $overallBurnRate }}%"></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#F7941D]"></div>
        </div>

        <!-- Scorecard 3: Cycle-Time Velocity -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span>End-to-End Velocity</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">-50.0% Time</span>
            </div>
            <div class="text-2xl font-extrabold text-emerald-600">7.0 Days</div>
            <div class="mt-2 text-xs text-slate-500 flex items-center justify-between">
                <span>Retail Industry Benchmark:</span>
                <span class="font-bold text-slate-700">14.0 Days</span>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-500"></div>
        </div>

        <!-- Scorecard 4: Store Execution Rate -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                <span>In-Store Rollout Proof</span>
                <span class="text-xs font-bold text-slate-700">{{ $executedTasks }}/{{ $totalTasks }} Tasks</span>
            </div>
            <div class="text-2xl font-extrabold text-slate-900">{{ $storeExecutionRate }}%</div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3 overflow-hidden">
                <div class="bg-slate-800 h-1.5 rounded-full" style="width: {{ $storeExecutionRate }}%"></div>
            </div>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-800"></div>
        </div>

    </div>

    <!-- Power BI Visuals Row: Waterfall Velocity & Pacing Corridor Line Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- BI Visual 1: Cycle-Time Waterfall Chart (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-slate-900">Marketing Pipeline Cycle-Time (Waterfall)</h3>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">Velocity Metric</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Duration spent across sequential handover stages (in Days)</p>
                </div>
                <div class="text-right text-xs">
                    <span class="text-slate-400">Total Velocity:</span>
                    <span class="font-extrabold text-emerald-600 ml-1">7.0 Days</span>
                </div>
            </div>

            <!-- Waterfall Visual Canvas -->
            <div class="h-64 w-full">
                <canvas id="velocityWaterfallChart"></canvas>
            </div>

            <!-- Benchmark Explanation Banner -->
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between text-xs text-slate-600">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-800">Key Finding:</span>
                    <span>AI Copilot copywriting and automatic IMC unlocking accelerates launch time from 14.0 days to 7.0 days.</span>
                </div>
                <span class="font-extrabold text-[#F7941D] whitespace-nowrap">2X Faster</span>
            </div>
        </div>

        <!-- BI Visual 2: Cumulative Budget Burn vs. Allocation Line Chart (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Budget Burn vs. Target Corridor</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Weekly cumulative spend vs. approved allocation (KES Millions)</p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-[#F7941D] border border-orange-200">
                    Pacing OK
                </span>
            </div>

            <div class="h-64 w-full">
                <canvas id="burnCorridorLineChart"></canvas>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 mr-1.5"></span>Allocated Cap</span>
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-[#F7941D] mr-1.5"></span>Actual Burn</span>
                <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span>Target Pacing</span>
            </div>
        </div>

    </div>

    <!-- Campaigns Master Table & Live Global Activity Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Campaigns Master Records (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Live Campaign Pipeline Governance</h3>
                    <p class="text-xs text-slate-500">Cross-department status, expenditure, and agency assignments.</p>
                </div>
                <span class="text-xs text-slate-500 font-semibold">{{ $campaigns->count() }} Active Initiatives</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                    <thead class="text-slate-400 font-bold uppercase text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Campaign</th>
                            <th class="py-2.5 px-3">Agency ID</th>
                            <th class="py-2.5 px-3">Workflow Status</th>
                            <th class="py-2.5 px-3">Burn Rate</th>
                            <th class="py-2.5 px-3 text-right">In-Store Rollout</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($campaigns as $campaign)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-900">{{ $campaign->title }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $campaign->code }} • {{ $campaign->category }}</div>
                            </td>
                            <td class="py-3 px-3">
                                @if($campaign->agency_id)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $campaign->agency_id }}
                                </span>
                                @else
                                <span class="text-slate-400 text-[11px]">Internal Only</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    @if($campaign->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($campaign->status === 'ready_for_launch') bg-orange-50 text-[#F7941D] border border-orange-200
                                    @elseif($campaign->status === 'pending_approval') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ str_replace('_', ' ', $campaign->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 min-w-[130px]">
                                <div class="flex items-center justify-between text-[11px] mb-1 font-medium">
                                    <span>KES {{ number_format($campaign->spent_budget / 1000000, 1) }}M</span>
                                    <span class="text-[#F7941D]">{{ $campaign->burn_rate }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                    <div class="bg-[#F7941D] h-1 rounded-full" style="width: {{ min(100, $campaign->burn_rate) }}%"></div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-right font-medium text-slate-700">
                                {{ $campaign->storeTasks->whereIn('status', ['executed_pending_verification', 'verified'])->count() }} / {{ $campaign->storeTasks->count() }} Proofs
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Global Activity Feed with Personas (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex flex-col h-full">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-slate-900">Global Activity Feed</h3>
                </div>
                <span class="text-[11px] text-slate-400">All 6 Personas Live</span>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center space-x-1 border-b border-slate-100 pb-2 mb-3 text-[11px] overflow-x-auto">
                <a href="{{ route('executive.dashboard') }}" class="px-2.5 py-1 rounded-md font-semibold transition {{ empty($departmentFilter) ? 'bg-slate-900 text-white' : 'text-slate-500 hover:text-slate-800' }}">All</a>
                <a href="{{ route('executive.dashboard', ['department' => 'IMC']) }}" class="px-2.5 py-1 rounded-md font-semibold transition {{ $departmentFilter === 'IMC' ? 'bg-[#F7941D] text-white' : 'text-slate-500 hover:text-slate-800' }}">IMC</a>
                <a href="{{ route('executive.dashboard', ['department' => 'Agency']) }}" class="px-2.5 py-1 rounded-md font-semibold transition {{ $departmentFilter === 'Agency' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:text-slate-800' }}">Agency</a>
                <a href="{{ route('executive.dashboard', ['department' => 'DPM']) }}" class="px-2.5 py-1 rounded-md font-semibold transition {{ $departmentFilter === 'DPM' ? 'bg-[#F7941D] text-white' : 'text-slate-500 hover:text-slate-800' }}">DPM</a>
                <a href="{{ route('executive.dashboard', ['department' => 'OME']) }}" class="px-2.5 py-1 rounded-md font-semibold transition {{ $departmentFilter === 'OME' ? 'bg-[#F7941D] text-white' : 'text-slate-500 hover:text-slate-800' }}">OME</a>
            </div>

            <!-- Feed Stream -->
            <div class="flex-1 overflow-y-auto space-y-3 pr-1 max-h-[460px]">
                @forelse($activityLogs as $log)
                <div class="p-3 rounded-lg border border-slate-100 hover:border-slate-200 bg-slate-50/60 transition text-xs space-y-1">
                    <div class="flex items-center justify-between text-[11px]">
                        <div class="flex items-center space-x-1.5">
                            <span class="font-bold text-slate-900">{{ $log->user_name }}</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase
                                @if($log->department === 'IMC') bg-purple-100 text-purple-700
                                @elseif($log->department === 'Agency') bg-blue-100 text-blue-700
                                @elseif($log->department === 'DPM') bg-sky-100 text-sky-700
                                @elseif($log->department === 'OME') bg-emerald-100 text-emerald-700
                                @else bg-slate-200 text-slate-800 @endif">
                                {{ $log->department }}
                            </span>
                        </div>
                        <span class="text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-slate-700 font-medium leading-relaxed">{{ $log->description }}</p>
                    @if($log->campaign)
                    <div class="text-[10px] text-slate-400 pt-1 flex items-center space-x-1">
                        <span>Campaign:</span>
                        <span class="text-slate-600 font-semibold">{{ $log->campaign->title }}</span>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-8 text-slate-400 text-xs">No activity logged for this filter.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Create Campaign Modal -->
    <div x-show="newCampaignModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="newCampaignModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Initiate New Campaign</h3>
                <button type="button" @click="newCampaignModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
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
                    <label class="block font-semibold text-slate-700 mb-1">Campaign Strategy & Deliverable Scope</label>
                    <textarea name="description" rows="2" placeholder="Describe strategic retail objectives..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="newCampaignModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">Create Campaign</button>
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

    // 1. Cycle-Time Waterfall Chart (Bar Chart with Stage Durations)
    const ctxWaterfall = document.getElementById('velocityWaterfallChart');
    if (ctxWaterfall && window.Chart) {
        new window.Chart(ctxWaterfall, {
            type: 'bar',
            data: {
                labels: chartData.waterfallLabels,
                datasets: [{
                    label: 'Stage Duration (Days)',
                    data: chartData.waterfallDays,
                    backgroundColor: [
                        '#e2e8f0', // Planning
                        '#fb923c', // Creative (Orange tint)
                        '#c084fc', // IMC Lead (Purple tint)
                        '#38bdf8', // Media Setup (Sky blue)
                        '#34d399', // Store Execution (Emerald)
                        '#F7941D', // Total Velocity (Naivas orange)
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.55,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' Days duration';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return v + 'd'; },
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

    // 2. Budget Burn vs Allocation Multi-Series Line Chart
    const ctxBurn = document.getElementById('burnCorridorLineChart');
    if (ctxBurn && window.Chart) {
        new window.Chart(ctxBurn, {
            type: 'line',
            data: {
                labels: chartData.timeSeriesLabels,
                datasets: [
                    {
                        label: 'Allocated Cap (KES M)',
                        data: chartData.cumulativeAllocated,
                        borderColor: '#cbd5e1',
                        backgroundColor: 'transparent',
                        borderDash: [5, 5],
                        borderWidth: 2,
                        tension: 0.3,
                        pointRadius: 3,
                    },
                    {
                        label: 'Actual Spend (KES M)',
                        data: chartData.cumulativeSpent,
                        borderColor: '#F7941D', // Naivas orange
                        backgroundColor: 'rgba(247, 148, 29, 0.08)',
                        fill: true,
                        borderWidth: 2.5,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#F7941D',
                    },
                    {
                        label: 'Target Trajectory (KES M)',
                        data: chartData.pacingTarget,
                        borderColor: '#10b981', // Emerald
                        backgroundColor: 'transparent',
                        borderWidth: 1.5,
                        tension: 0.3,
                        pointRadius: 2,
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
                                return context.dataset.label + ': KES ' + context.raw + 'M';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return 'KES ' + v + 'M'; },
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
});
</script>
@endpush
