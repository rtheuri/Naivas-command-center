@extends('layouts.app', ['title' => 'Executive Hub'])

@section('content')
<div class="space-y-6" x-data="{ newCampaignModal: false, activePbiTab: 'bi_overview', proofVerificationModal: false, selectedTask: null, showRedoForm: false }">

    <!-- Executive Hub Header Bar -->
    <div class="bg-white rounded-xl p-4 shadow-xs border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-base font-bold text-slate-900">Executive Hub</h1>
        </div>

        <div class="flex items-center space-x-2 text-xs">
            <button type="button" @click="location.reload()" 
                    class="px-3 py-1.5 rounded-lg bg-white hover:bg-slate-50 text-slate-700 transition flex items-center space-x-1.5 border border-slate-200 shadow-xs font-medium">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
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
                @php
                    $task = $log->store_task;
                @endphp
                <div class="p-3 rounded-lg border transition text-xs space-y-1.5 {{ $task && $task->status === 'executed_pending_verification' ? 'border-amber-300 bg-amber-50/50 shadow-2xs' : 'border-slate-100 hover:border-slate-200 bg-slate-50/60' }}">
                    <div class="flex items-center justify-between text-[11px]">
                        <div class="flex items-center space-x-1.5 flex-wrap gap-y-1">
                            <span class="font-bold text-slate-900">{{ $log->user_name }}</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase
                                @if($log->department === 'IMC') bg-purple-100 text-purple-700
                                @elseif($log->department === 'Agency') bg-blue-100 text-blue-700
                                @elseif($log->department === 'DPM') bg-sky-100 text-sky-700
                                @elseif($log->department === 'OME') bg-emerald-100 text-emerald-700
                                @else bg-slate-200 text-slate-800 @endif">
                                {{ $log->department }}
                            </span>
                            @if($task)
                                @if($task->status === 'executed_pending_verification')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Action Required</span>
                                    </span>
                                @elseif($task->status === 'redo_requested')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-100 text-rose-800 border border-rose-300 flex items-center space-x-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Redo Sent to OME</span>
                                    </span>
                                @elseif($task->status === 'verified')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✓ Verified Live
                                    </span>
                                @endif
                            @endif
                        </div>
                        <span class="text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-slate-700 font-medium leading-relaxed">{{ $log->description }}</p>

                    @if($log->campaign)
                    <div class="text-[10px] text-slate-400 pt-0.5 flex items-center space-x-1">
                        <span>Campaign:</span>
                        <span class="text-slate-600 font-semibold">{{ $log->campaign->title }}</span>
                    </div>
                    @endif

                    @if($task)
                    <div class="pt-2 mt-1.5 border-t border-slate-200/60 flex items-center justify-between">
                        <div class="flex items-center space-x-1.5 text-[11px] text-slate-600 font-medium">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="truncate max-w-[120px]">{{ $task->branch_name }}</span>
                        </div>

                        <button type="button" 
                                @click="selectedTask = {{ json_encode($task) }}; showRedoForm = false; proofVerificationModal = true"
                                class="px-2.5 py-1 rounded-md text-[11px] font-bold transition flex items-center space-x-1 shadow-2xs {{ $task->status === 'executed_pending_verification' ? 'bg-[#F7941D] hover:bg-[#e07f0f] text-white' : ($task->status === 'redo_requested' ? 'bg-rose-100 hover:bg-rose-200 text-rose-800' : 'bg-slate-100 hover:bg-slate-200 text-slate-700') }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>{{ $task->status === 'executed_pending_verification' ? 'Inspect Proof & Verify' : ($task->status === 'redo_requested' ? 'View Redo Status' : 'View Audit Details') }}</span>
                        </button>
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
    <div x-show="newCampaignModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto p-5 sm:p-6 shadow-xl border border-slate-200 text-slate-800 my-auto" @click.away="newCampaignModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Initiate New Campaign</h3>
                <button type="button" @click="newCampaignModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">Close</button>
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

    <!-- Executive In-Store Proof Verification Modal -->
    <div x-show="proofVerificationModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md sm:max-w-lg w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden my-auto" @click.away="proofVerificationModal = false">
            
            <!-- Sticky Modal Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-white shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs border border-emerald-200">
                        ✓
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900">In-Store Proof Verification</h3>
                        <p class="text-[10px] text-slate-500">Executive HQ Review & Field Sign-Off</p>
                    </div>
                </div>
                <button type="button" @click="proofVerificationModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Scrollable Modal Body -->
            <template x-if="selectedTask">
                <div class="overflow-y-auto p-4 space-y-3 text-xs flex-1">
                    
                    <!-- Task Overview Card -->
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="font-bold text-slate-500 uppercase tracking-wider truncate max-w-[240px]" x-text="selectedTask.branch_name + ' • ' + selectedTask.zone"></span>
                            <span class="px-1.5 py-0.5 rounded font-bold uppercase tracking-wider text-[9px] shrink-0"
                                  :class="selectedTask.status === 'verified' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                  x-text="selectedTask.status === 'verified' ? 'Verified Live' : 'Pending Verification'">
                            </span>
                        </div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900" x-text="selectedTask.task_name"></h4>
                        <div class="text-[10px] text-slate-500 flex items-center space-x-1">
                            <span>Campaign:</span>
                            <span class="font-semibold text-slate-700 truncate" x-text="selectedTask.campaign ? selectedTask.campaign.title : 'Active Campaign'"></span>
                        </div>
                    </div>

                    <!-- Photographic Proof Inspection Frame (Compact) -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-[10px] font-semibold text-slate-600">
                            <span>Photographic Proof of Installation:</span>
                            <span class="text-[9px] text-slate-400 font-normal">Captured in-store via mobile audit</span>
                        </div>
                        <div class="w-full h-40 sm:h-44 rounded-lg bg-slate-100 border border-slate-200 overflow-hidden relative shadow-inner">
                            <template x-if="selectedTask.proof_image_url">
                                <img :src="selectedTask.proof_image_url" :alt="selectedTask.task_name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!selectedTask.proof_image_url">
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                    No proof photo attached.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Store Manager Notes -->
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 text-[11px] space-y-0.5">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Store Manager Field Notes:</span>
                        <p class="text-slate-700 italic leading-snug" x-text="selectedTask.proof_notes || 'Physical POSM installed securely per branch merchandising guidelines.'"></p>
                    </div>

                    <!-- Compliance Checkpoints (Compact) -->
                    <div class="p-2.5 rounded-lg bg-emerald-50/60 border border-emerald-200 text-[10px] text-emerald-900 space-y-1">
                        <div class="font-bold flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Executive Audit Checkpoints</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 text-[9px] text-emerald-800 font-medium">
                            <div class="flex items-center space-x-1"><span>✓</span><span>Brand colors</span></div>
                            <div class="flex items-center space-x-1"><span>✓</span><span>Aisle clear</span></div>
                            <div class="flex items-center space-x-1"><span>✓</span><span>Promos stocked</span></div>
                        </div>
                    </div>

                    <!-- Redo Notice Banner if already requested -->
                    <template x-if="selectedTask.status === 'redo_requested'">
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-xs space-y-1">
                            <div class="flex items-center space-x-1.5 text-rose-800 font-bold text-[11px]">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Redo Dispatched: Awaiting Corrected Proof from OME</span>
                            </div>
                            <p class="text-rose-900 font-medium italic text-[11px]" x-text="'Instructions: &quot;' + (selectedTask.redo_instructions || 'Please retake with compliant brand visibility and clear pricing.') + '&quot;'"></p>
                            <span class="text-[10px] text-rose-600 block">The store manager has received feedback on mobile to re-upload compliant proof.</span>
                        </div>
                    </template>

                    <!-- Redo Instructions Input Box (When clicked) -->
                    <div x-show="showRedoForm" x-transition class="p-3 bg-rose-50/90 border border-rose-200 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between text-rose-900 font-bold">
                            <span class="flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Insufficient Proof • Specify Redo Instructions</span>
                            </span>
                            <button type="button" @click="showRedoForm = false" class="text-rose-500 hover:text-rose-700 text-[11px] font-medium">Cancel</button>
                        </div>
                        <form :action="'/executive/store-tasks/' + selectedTask.id + '/redo'" method="POST" class="space-y-2">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-medium text-rose-800 mb-1">Feedback & Correction Instructions for Store Manager:</label>
                                <textarea name="redo_instructions" rows="2" required 
                                          placeholder="e.g. The photo angle is tilted and the promotional price card is obscured. Please retake an eye-level photo showing full fixture and clear pricing."
                                          class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-rose-300 focus:outline-none focus:ring-1 focus:ring-rose-500 bg-white text-slate-800 placeholder-slate-400"></textarea>
                            </div>
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" @click="showRedoForm = false" class="px-2.5 py-1 rounded-md text-[11px] font-semibold border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">Cancel</button>
                                <button type="submit" class="px-3 py-1 rounded-md text-[11px] font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-2xs transition flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span>Dispatch Redo Notice to OME</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </template>

            <!-- Sticky Modal Footer Actions -->
            <template x-if="selectedTask">
                <div class="px-4 py-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
                    <button type="button" @click="proofVerificationModal = false" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50">
                        Close
                    </button>

                    <div class="flex items-center space-x-2">
                        <template x-if="selectedTask.status === 'executed_pending_verification' && !showRedoForm">
                            <button type="button" 
                                    @click="showRedoForm = true"
                                    class="px-3 py-1.5 text-xs font-bold rounded-lg border border-rose-300 text-rose-700 bg-rose-50 hover:bg-rose-100 transition flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Request Redo</span>
                            </button>
                        </template>

                        <template x-if="selectedTask.status === 'executed_pending_verification'">
                            <form :action="'/executive/store-tasks/' + selectedTask.id + '/verify'" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-1.5 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition flex items-center space-x-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>Verify & Approve Display</span>
                                </button>
                            </form>
                        </template>

                        <template x-if="selectedTask.status === 'redo_requested'">
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-rose-100 text-rose-800 text-[11px] font-bold border border-rose-200">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Redo Dispatched to OME</span>
                            </span>
                        </template>

                        <template x-if="selectedTask.status === 'verified'">
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-[11px] font-bold">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Display Officially Verified Live</span>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

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
