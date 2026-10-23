@extends('layouts.app', ['title' => 'DPM Media Spend'])

@section('content')
<div class="space-y-6" x-data="{ 
    reallocateModal: false,
    fromBudgetId: '{{ $allBudgets->first()->id ?? '' }}',
    toBudgetId: '{{ $allBudgets->skip(1)->first()->id ?? '' }}',
    reallocateAmount: 250000,
    reallocateReason: 'Optimizing funds toward higher ROI digital channels based on weekly pacing analysis.'
}">

    <!-- Header -->
    <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-[#F7941D] uppercase tracking-wider block mb-1">Module 2: Media Spend</span>
            <h1 class="text-2xl font-bold text-slate-900">Media Spend & Pacing Command</h1>
            <p class="text-xs text-slate-500 mt-1">
                Real-time tracking of multi-channel ad delivery. Ad deployment is <strong class="text-slate-800">strictly gated by IMC asset approval</strong>.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <button type="button" @click="reallocateModal = true" 
                    class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-semibold shadow-xs transition">
                Reallocate Budget
            </button>
        </div>
    </div>

    <!-- High-Density Metrics Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        
        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Total Allocated</span>
            <div class="text-lg font-bold text-slate-900 truncate">KES {{ number_format($totalAllocated) }}</div>
            <span class="text-[10px] text-slate-500">100% Channel Cap</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Spent to Date</span>
            <div class="text-lg font-bold text-[#F7941D] truncate">KES {{ number_format($totalSpent) }}</div>
            <span class="text-[10px] font-semibold text-slate-700">
                {{ $totalAllocated > 0 ? round(($totalSpent / $totalAllocated) * 100, 1) : 0 }}% Pacing Burn
            </span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Impressions</span>
            <div class="text-lg font-bold text-slate-900">{{ number_format($totalImpressions / 1000000, 2) }}M</div>
            <span class="text-[10px] text-slate-500">Delivered Across Media</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Clicks</span>
            <div class="text-lg font-bold text-slate-900">{{ number_format($totalClicks) }}</div>
            <span class="text-[10px] text-slate-500">Shopper Engagements</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Blended CTR</span>
            <div class="text-lg font-bold text-slate-900">{{ $blendedCtr }}%</div>
            <span class="text-[10px] text-slate-500">Bench: 2.8%</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block mb-1">Conversions</span>
            <div class="text-lg font-bold text-slate-900">{{ number_format($totalConversions) }}</div>
            <span class="text-[10px] text-slate-500">Baskets & Redemptions</span>
        </div>

    </div>

    <!-- Channel Spend Mini Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        @foreach($channelStats as $cs)
        <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-semibold text-slate-900 truncate">{{ $cs->channel_name }}</span>
            </div>
            <div class="text-sm font-bold text-slate-900">KES {{ number_format($cs->total_spent) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Cap: KES {{ number_format($cs->total_allocated) }}</div>
            <div class="w-full bg-slate-100 rounded-full h-1 mt-2 overflow-hidden">
                <div class="bg-[#F7941D] h-1 rounded-full" style="width: {{ $cs->total_allocated > 0 ? min(100, round(($cs->total_spent / $cs->total_allocated) * 100)) : 0 }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Campaigns Media Spend & Pacing Table -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Campaign Media Allocation & Pacing</h3>
                <p class="text-xs text-slate-500">Deployment requires prior approval from the IMC creative team.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Campaign</th>
                        <th class="py-2.5 px-3">IMC Approval</th>
                        <th class="py-2.5 px-3">Allocated vs Spent</th>
                        <th class="py-2.5 px-3">Pacing Progress</th>
                        <th class="py-2.5 px-3">Active Channels</th>
                        <th class="py-2.5 px-3 text-right">Deployment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($campaigns as $campaign)
                    @php
                        $isApproved = $campaign->hasApprovedAssets() || in_array($campaign->status, ['ready_for_launch', 'active']);
                        $isFullyDeployed = $campaign->status === 'active';
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        
                        <td class="py-3 px-3">
                            <div class="font-semibold text-slate-900">{{ $campaign->title }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $campaign->code }}</div>
                        </td>

                        <td class="py-3 px-3 whitespace-nowrap">
                            @if($isApproved)
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-orange-50 text-[#F7941D] border border-orange-200">
                                    Approved
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                    Awaiting IMC Sign-Off
                                </span>
                            @endif
                        </td>

                        <td class="py-3 px-3 whitespace-nowrap">
                            <div class="font-medium text-slate-900">KES {{ number_format($campaign->spent_budget) }}</div>
                            <div class="text-[11px] text-slate-400">of KES {{ number_format($campaign->allocated_budget) }} cap</div>
                        </td>

                        <td class="py-3 px-3 min-w-[150px]">
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-medium text-slate-700">{{ $campaign->burn_rate }}% Spent</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#F7941D] h-1.5 rounded-full" style="width: {{ min(100, $campaign->burn_rate) }}%"></div>
                            </div>
                        </td>

                        <td class="py-3 px-3">
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @foreach($campaign->budgets as $b)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $b->is_deployed ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-400' }}">
                                    {{ $b->channel_icon === 'meta' ? 'Meta' : ($b->channel_icon === 'google' ? 'Google' : ($b->channel_icon === 'tiktok' ? 'TikTok' : ($b->channel_icon === 'sms' ? 'SMS' : 'POSM'))) }}
                                </span>
                                @endforeach
                            </div>
                        </td>

                        <td class="py-3 px-3 text-right whitespace-nowrap">
                            @if($campaign->status === 'active')
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Live In Market</span>
                                </span>
                            @elseif(in_array($campaign->status, ['ready_for_launch', 'approved_ready_for_media']))
                                <form action="{{ route('dpm.campaigns.deploy', $campaign->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span>Deploy Ads</span>
                                    </button>
                                </form>
                            @else
                                <!-- Strict Gating: Disabled with Lock Icon -->
                                <button type="button" disabled 
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-xs font-medium cursor-not-allowed opacity-80"
                                        title="Strict Sequential Gating: Ads locked until campaign_status is approved_ready_for_media">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span>Locked (Requires IMC)</span>
                                </button>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reallocate Budget Modal -->
    <div x-show="reallocateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="reallocateModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Reallocate Media Budget</h3>
                <button type="button" @click="reallocateModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('dpm.budget.reallocate') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Source Channel (Deduct From) *</label>
                    <select name="from_budget_id" x-model="fromBudgetId" required 
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                        @foreach($allBudgets as $b)
                        <option value="{{ $b->id }}">
                            {{ $b->channel_name }} — {{ $b->campaign->title }} (Avail: KES {{ number_format($b->allocated_amount - $b->spent_amount) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Destination Channel (Add To) *</label>
                    <select name="to_budget_id" x-model="toBudgetId" required 
                            class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                        @foreach($allBudgets as $b)
                        <option value="{{ $b->id }}">
                            {{ $b->channel_name }} — {{ $b->campaign->title }} (Cap: KES {{ number_format($b->allocated_amount) }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Transfer Amount (KES) *</label>
                    <input type="number" name="amount" x-model="reallocateAmount" min="5000" step="5000" required 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none font-semibold text-slate-900">
                    <div class="flex items-center space-x-2 mt-2">
                        <button type="button" @click="reallocateAmount = 100000" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[11px] font-medium text-slate-700">100K</button>
                        <button type="button" @click="reallocateAmount = 250000" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[11px] font-medium text-slate-700">250K</button>
                        <button type="button" @click="reallocateAmount = 500000" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-[11px] font-medium text-slate-700">500K</button>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reason</label>
                    <textarea name="reason" x-model="reallocateReason" rows="2" 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="reallocateModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                        Execute Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
