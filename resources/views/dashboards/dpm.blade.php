@extends('layouts.app', ['title' => 'DPM Media Spend'])

@section('content')
<div class="space-y-6" x-data="{ 
    reallocateModal: false,
    fromBudgetId: '{{ $allBudgets->first()->id ?? '' }}',
    toBudgetId: '{{ $allBudgets->skip(1)->first()->id ?? '' }}',
    reallocateAmount: 250000,
    reallocateReason: 'Optimizing funds toward higher ROI digital channels based on weekly pacing analysis.',
    budgets: {{ json_encode($allBudgets->map(fn($b) => [
        'id' => $b->id,
        'channel_name' => $b->channel_name,
        'campaign_title' => $b->campaign->title,
        'allocated' => (float)$b->allocated_amount,
        'spent' => (float)$b->spent_amount,
        'available' => (float)($b->allocated_amount - $b->spent_amount)
    ])) }},
    getSource() {
        return this.budgets.find(b => b.id == this.fromBudgetId) || null;
    },
    getDest() {
        return this.budgets.find(b => b.id == this.toBudgetId) || null;
    }
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
                    class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-semibold shadow-xs transition cursor-pointer flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Reallocate Budget</span>
            </button>
        </div>
    </div>

    <!-- CMO Decision Notifications: Approved Shifts Awaiting DPM Execution -->
    @if(isset($approvedReallocations) && $approvedReallocations->isNotEmpty())
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-300 rounded-xl p-4 sm:p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-emerald-200">
            <div class="flex items-center space-x-2.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    ✓
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center space-x-2">
                        <span>CMO Clearance Granted: Authorized Budget Shifts Ready to Execute</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white animate-pulse">
                            {{ $approvedReallocations->count() }} Actionable
                        </span>
                    </h3>
                    <p class="text-[11px] text-slate-600">CMO David Miller has reviewed and authorized the following budget restructuring. Click below to apply live channel cap updates.</p>
                </div>
            </div>
            <span class="text-[11px] text-emerald-800 font-semibold bg-emerald-100 px-2.5 py-1 rounded-md border border-emerald-200">
                Authorized by CMO
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
            @foreach($approvedReallocations as $appRealloc)
            <div class="bg-white rounded-lg p-3.5 border border-emerald-200 shadow-2xs space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
                        Request #{{ $appRealloc->id }} • Authorized {{ $appRealloc->responded_at?->diffForHumans() ?? 'recently' }}
                    </span>
                    <span class="text-sm font-extrabold text-emerald-600">
                        KES {{ number_format($appRealloc->amount) }}
                    </span>
                </div>

                <div class="flex items-center space-x-2 p-2 rounded-md bg-slate-50 border border-slate-100 text-[11px]">
                    <div class="flex-1 truncate">
                        <span class="text-slate-400 block text-[9px] uppercase font-bold">Deduct From:</span>
                        <span class="font-bold text-slate-800">{{ $appRealloc->fromBudget->channel_name }}</span>
                        <span class="text-slate-500 block truncate text-[10px]">({{ $appRealloc->fromBudget->campaign->title }})</span>
                    </div>
                    <div class="text-slate-400 font-bold px-1 text-sm">→</div>
                    <div class="flex-1 truncate text-right">
                        <span class="text-slate-400 block text-[9px] uppercase font-bold">Add To:</span>
                        <span class="font-bold text-emerald-700">{{ $appRealloc->toBudget->channel_name }}</span>
                        <span class="text-slate-500 block truncate text-[10px]">({{ $appRealloc->toBudget->campaign->title }})</span>
                    </div>
                </div>

                <div class="p-2 rounded bg-emerald-50/70 border border-emerald-100 text-[11px] text-emerald-900">
                    <span class="font-bold block text-[10px] text-emerald-800 uppercase tracking-wider">CMO Strategic Note:</span>
                    <p class="italic">"{{ $appRealloc->cmo_notes ?? 'Authorized by CMO. Proceed with live restructuring.' }}"</p>
                </div>

                <form action="{{ route('dpm.budget.execute', $appRealloc->id) }}" method="POST" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition cursor-pointer flex items-center justify-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Apply & Execute Approved Shift Now →</span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- CMO Decision Notifications: Rejected Shifts with Explanation -->
    @if(isset($rejectedReallocations) && $rejectedReallocations->isNotEmpty())
    <div class="bg-gradient-to-r from-rose-50 to-orange-50 border border-rose-200 rounded-xl p-4 shadow-xs space-y-2">
        <div class="flex items-center justify-between pb-1.5 border-b border-rose-200">
            <div class="flex items-center space-x-2 text-rose-800 font-bold text-xs">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>CMO Governance Decision: Budget Reallocation Declined</span>
            </div>
            <span class="text-[10px] text-rose-600 font-semibold">David Miller (CMO) Feedback</span>
        </div>

        <div class="space-y-2 pt-1 text-xs">
            @foreach($rejectedReallocations as $rej)
            <div class="bg-white/80 p-2.5 rounded-lg border border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="font-bold text-slate-800">KES {{ number_format($rej->amount) }}</span>
                    <span class="text-slate-500">from {{ $rej->fromBudget->channel_name }} → {{ $rej->toBudget->channel_name }}</span>
                    <p class="text-rose-800 font-medium text-[11px] mt-0.5">
                        <strong>CMO Explanation:</strong> "{{ $rej->rejection_reason ?? 'Shift exceeds departmental quarterly tolerance; maintain current retail flighting plan.' }}"
                    </p>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 shrink-0 self-start sm:self-center">
                    Declined by CMO
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

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

    <!-- Approved Creative Assets Cleared for Media Deployment (Direct from IMC Approval) -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-bold text-slate-900">Approved Creative Assets Cleared for Media Deployment</h3>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    Creative deliverables signed off by Sarah Jenkins (IMC Lead). These authorized assets unlock live paid ad flighting across Meta, Google, TikTok, and SMS networks.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $approvedAssets->count() }} Creative Assets Cleared
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($approvedAssets as $appAsset)
            @php
                $digitalBudgets = $appAsset->campaign->budgets->where('channel_icon', '!=', 'posm');
                $hasPendingDigital = $digitalBudgets->where('is_deployed', false)->isNotEmpty();
                $allDigitalDeployed = $digitalBudgets->isNotEmpty() && $digitalBudgets->where('is_deployed', false)->isEmpty();
            @endphp
            <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-[#F7941D] hover:ring-1 hover:ring-[#F7941D]/30 transition flex flex-col justify-between shadow-2xs space-y-3.5">
                
                <div class="space-y-3">
                    <!-- Top Status & Campaign Bar -->
                    <div class="flex items-center justify-between gap-1 text-[11px]">
                        <span class="font-mono font-bold text-[#F7941D] bg-orange-50 px-2 py-0.5 rounded border border-orange-200">
                            {{ $appAsset->campaign->code }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                            @if($hasPendingDigital) bg-amber-50 text-amber-700 border border-amber-200 animate-pulse
                            @elseif($allDigitalDeployed) bg-emerald-50 text-emerald-700 border border-emerald-200
                            @else bg-slate-100 text-slate-700 @endif">
                            {{ $hasPendingDigital ? '⚡ Ready to Deploy' : ($allDigitalDeployed ? '✓ Live In Market' : '✓ Creative Approved') }}
                        </span>
                    </div>

                    <!-- Visual Asset Preview & Title -->
                    <div class="flex items-start space-x-3">
                        @if($appAsset->file_url)
                        <img src="{{ $appAsset->file_url }}" alt="Creative Mockup" 
                             class="w-16 h-16 rounded-lg object-cover border border-slate-200 shrink-0 shadow-2xs">
                        @else
                        <div class="w-16 h-16 rounded-lg bg-orange-50 border border-orange-200 flex items-center justify-center text-[#F7941D] shrink-0 font-bold text-xs">
                            ASSET
                        </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-2">
                                {{ $appAsset->title }}
                            </h4>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                {{ $appAsset->dimensions }} • {{ $appAsset->format }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate">
                                Campaign: <strong class="text-slate-600">{{ $appAsset->campaign->title }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- IMC Approval Stamp & Directive -->
                    <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200/80 text-[11px] text-emerald-900 space-y-1">
                        <div class="flex items-center justify-between font-bold text-emerald-800 text-[10px] uppercase tracking-wider">
                            <span class="flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>IMC Approval Sign-Off</span>
                            </span>
                            <span>{{ $appAsset->approved_at?->diffForHumans() ?? 'Approved' }}</span>
                        </div>
                        <p class="text-slate-700 italic text-[11px] leading-relaxed line-clamp-2">
                            "{{ $appAsset->approval_notes ?? 'Creative compliance verified by Sarah Jenkins (IMC Lead). Cleared for multi-channel live advertising deployment.' }}"
                        </p>
                    </div>

                    <!-- Associated Digital Channels for this Asset's Campaign -->
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-1.5 text-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">
                            Target Digital Ad Channels:
                        </span>
                        <div class="flex flex-wrap gap-1">
                            @forelse($digitalBudgets as $db)
                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-medium
                                @if($db->is_deployed) bg-emerald-100 text-emerald-800 @else bg-amber-100 text-amber-800 @endif">
                                <span>{{ $db->channel_icon === 'meta' ? 'Meta' : ($db->channel_icon === 'google' ? 'Google' : ($db->channel_icon === 'tiktok' ? 'TikTok' : ($db->channel_icon === 'sms' ? 'SMS' : 'Digital'))) }}</span>
                                <span class="font-bold">({{ $db->is_deployed ? 'Live' : 'Pending' }})</span>
                            </span>
                            @empty
                            <span class="text-[10px] text-slate-400 italic">Omnichannel retail distribution</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Deployment Action Button -->
                <div class="pt-2 border-t border-slate-100">
                    @if($hasPendingDigital || $appAsset->campaign->status !== 'active')
                    <form action="{{ route('dpm.campaigns.deploy', $appAsset->campaign_id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full py-2 px-3 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition flex items-center justify-center space-x-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Deploy Live Ads for this Asset →</span>
                        </button>
                    </form>
                    @else
                    <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <span class="flex items-center space-x-1.5 font-semibold text-[11px]">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Flighting Underway</span>
                        </span>
                        <span class="text-[9px] uppercase font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Active</span>
                    </div>
                    @endif
                </div>

            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs space-y-1">
                <p class="font-semibold text-slate-600">No creative assets currently approved.</p>
                <p class="text-[11px]">When Sarah Jenkins (IMC Lead) approves creative assets in the IMC Workspace, they will immediately appear here cleared for digital media deployment.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Campaigns Media Spend & Pacing Table -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Campaign Media Allocation & Pacing</h3>
                <p class="text-xs text-slate-500">Live pacing of all advertising channels. Deployment is strictly gated by IMC creative approval.</p>
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
                        $hasApprovedAssets = $campaign->hasApprovedAssets();
                        $isApproved = $hasApprovedAssets || in_array($campaign->status, ['ready_for_launch', 'active']);
                        $undeployedDigitalChannels = $campaign->budgets->where('channel_icon', '!=', 'posm')->where('is_deployed', false);
                        $hasUndeployedChannels = $undeployedDigitalChannels->isNotEmpty();
                        $canDeploy = $isApproved && ($campaign->status === 'ready_for_launch' || $hasUndeployedChannels || $campaign->budgets->isEmpty());
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        
                        <td class="py-3 px-3">
                            <div class="font-semibold text-slate-900">{{ $campaign->title }}</div>
                            @if($campaign->assets->where('status', 'approved')->isNotEmpty())
                                <div class="text-[11px] text-emerald-700 font-medium flex items-center space-x-1 mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $campaign->assets->where('status', 'approved')->count() }} Approved Creative(s)</span>
                                </div>
                            @elseif($campaign->briefs->isNotEmpty())
                                <div class="text-[11px] text-slate-500 flex items-center space-x-1 mt-0.5">
                                    <span>Brief: {{ $campaign->briefs->first()->title }}</span>
                                </div>
                            @endif
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $campaign->code }} • {{ $campaign->category }}</div>
                        </td>

                        <td class="py-3 px-3 whitespace-nowrap">
                            @if($isApproved)
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Approved by IMC
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    ⏳ Awaiting IMC Sign-Off
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
                            @if($canDeploy)
                                <form action="{{ route('dpm.campaigns.deploy', $campaign->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        <span>Deploy Ads</span>
                                    </button>
                                </form>
                            @elseif($campaign->status === 'active')
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Live In Market</span>
                                </span>
                            @else
                                <!-- Strict Gating: Disabled with Lock Icon -->
                                <button type="button" disabled 
                                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-slate-400 text-xs font-medium cursor-not-allowed opacity-80"
                                        title="Strict Sequential Gating: Ads locked until creative assets are approved by Sarah Jenkins (IMC Lead)">
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

    <!-- Budget Reallocation Governance & Status Log -->
    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Budget Reallocation Governance Log</h3>
                <p class="text-xs text-slate-500">All media budget transfers require formal approval from CMO (David Miller) before funds move.</p>
            </div>
            @if($pendingReallocationsCount > 0)
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                {{ $pendingReallocationsCount }} Shift Request(s) Awaiting CMO Review
            </span>
            @else
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                All Shifts Processed
            </span>
            @endif
        </div>

        @if($reallocations->isEmpty())
        <div class="text-center py-6 text-slate-400 text-xs">
            No budget shifts requested yet. Click "Reallocate Budget" above to submit a shift for CMO sign-off.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                <thead class="text-slate-400 font-bold uppercase text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="py-2 px-3">Date</th>
                        <th class="py-2 px-3">From Source</th>
                        <th class="py-2 px-3">To Destination</th>
                        <th class="py-2 px-3">Amount</th>
                        <th class="py-2 px-3">Justification</th>
                        <th class="py-2 px-3 text-right">CMO Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($reallocations as $realloc)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2.5 px-3 whitespace-nowrap text-slate-400 font-mono text-[11px]">
                            {{ $realloc->created_at->format('M d, H:i') }}
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="font-bold text-slate-900">{{ $realloc->fromBudget->channel_name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $realloc->fromBudget->campaign->title }}</div>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="font-bold text-slate-900">{{ $realloc->toBudget->channel_name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $realloc->toBudget->campaign->title }}</div>
                        </td>
                        <td class="py-2.5 px-3 whitespace-nowrap font-bold text-[#F7941D]">
                            KES {{ number_format($realloc->amount) }}
                        </td>
                        <td class="py-2.5 px-3 text-slate-600 text-xs max-w-xs">
                            {{ $realloc->reason }}
                            @if($realloc->rejection_reason)
                            <div class="text-rose-600 text-[10px] mt-0.5">CMO Note: {{ $realloc->rejection_reason }}</div>
                            @endif
                        </td>
                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $realloc->getStatusBadgeClasses() }}">
                                {{ $realloc->getStatusLabel() }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Reallocate Budget Modal (Requires CMO Approval) -->
    <div x-show="reallocateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-slate-900/50 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white rounded-xl sm:rounded-2xl max-w-lg w-full max-h-[84vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden my-auto" @click.away="reallocateModal = false">
            <div class="px-4 py-2.5 sm:px-5 sm:py-3 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Request Media Budget Reallocation</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500">Requires executive authorization from CMO David Miller before funds move</p>
                </div>
                <button type="button" @click="reallocateModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('dpm.budget.reallocate') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                @csrf

                <div class="p-3.5 sm:p-4 space-y-2.5 overflow-y-auto flex-1 text-xs overscroll-contain">
                    <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-[11px] flex items-center space-x-2">
                        <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Governance policy: Reallocated media funds will be dispatched to CMO David Miller for sign-off.</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Source Channel (Deduct From) *</label>
                        <select name="from_budget_id" x-model="fromBudgetId" required 
                                class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            @foreach($allBudgets as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->channel_name }} — {{ $b->campaign->title }} (Avail: KES {{ number_format($b->allocated_amount - $b->spent_amount) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Destination Channel (Add To) *</label>
                        <select name="to_budget_id" x-model="toBudgetId" required 
                                class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            @foreach($allBudgets as $b)
                            <option value="{{ $b->id }}">
                                {{ $b->channel_name }} — {{ $b->campaign->title }} (Cap: KES {{ number_format($b->allocated_amount) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Transfer Amount (KES) *</label>
                        <input type="number" name="amount" x-model="reallocateAmount" min="5000" step="5000" required 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none font-semibold text-slate-900">
                        <div class="flex items-center space-x-1.5 mt-1.5">
                            <button type="button" @click="reallocateAmount = 100000" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-700 cursor-pointer">100K</button>
                            <button type="button" @click="reallocateAmount = 250000" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-700 cursor-pointer">250K</button>
                            <button type="button" @click="reallocateAmount = 500000" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-semibold text-slate-700 cursor-pointer">500K</button>
                        </div>
                    </div>

                    <!-- Live Restructure Specifications Preview -->
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-2.5 space-y-1.5 text-[11px]" x-show="getSource() && getDest()">
                        <div class="flex items-center justify-between font-bold text-slate-700 uppercase text-[9px] tracking-wider border-b border-slate-200 pb-1">
                            <span>Live Restructuring Specifications Preview</span>
                            <span :class="Number(reallocateAmount) > (getSource() ? getSource().available : 0) ? 'text-rose-600 font-bold' : 'text-emerald-700 font-bold'">
                                <span x-text="Number(reallocateAmount) > (getSource() ? getSource().available : 0) ? '⚠️ Exceeds Available Balance' : '✓ Restructure Valid'"></span>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-0.5 text-[10px]">
                            <!-- Source Restructure -->
                            <div class="p-1.5 rounded bg-white border border-slate-200 space-y-0.5">
                                <span class="text-[9px] font-bold text-rose-700 uppercase block truncate" x-text="'Deduct: ' + (getSource() ? getSource().channel_name : '')"></span>
                                <div class="flex justify-between text-slate-500">
                                    <span>Current:</span>
                                    <span class="font-mono text-slate-700" x-text="'KES ' + Number(getSource() ? getSource().allocated : 0).toLocaleString()"></span>
                                </div>
                                <div class="flex justify-between font-bold text-rose-700">
                                    <span>New Cap:</span>
                                    <span class="font-mono" x-text="'KES ' + Math.max(0, Number(getSource() ? getSource().allocated : 0) - Number(reallocateAmount || 0)).toLocaleString()"></span>
                                </div>
                                <div class="flex justify-between text-slate-400 text-[9px]">
                                    <span>Remaining:</span>
                                    <span class="font-mono" x-text="'KES ' + Math.max(0, Number(getSource() ? getSource().available : 0) - Number(reallocateAmount || 0)).toLocaleString()"></span>
                                </div>
                            </div>

                            <!-- Destination Restructure -->
                            <div class="p-1.5 rounded bg-white border border-slate-200 space-y-0.5">
                                <span class="text-[9px] font-bold text-emerald-700 uppercase block truncate" x-text="'Add: ' + (getDest() ? getDest().channel_name : '')"></span>
                                <div class="flex justify-between text-slate-500">
                                    <span>Current:</span>
                                    <span class="font-mono text-slate-700" x-text="'KES ' + Number(getDest() ? getDest().allocated : 0).toLocaleString()"></span>
                                </div>
                                <div class="flex justify-between font-bold text-emerald-700">
                                    <span>New Cap:</span>
                                    <span class="font-mono" x-text="'KES ' + (Number(getDest() ? getDest().allocated : 0) + Number(reallocateAmount || 0)).toLocaleString()"></span>
                                </div>
                                <div class="flex justify-between text-emerald-600 text-[9px] font-semibold">
                                    <span>Cap Lift:</span>
                                    <span class="font-mono" x-text="'+KES ' + Number(reallocateAmount || 0).toLocaleString()"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Performance Justification *</label>
                        <textarea name="reason" x-model="reallocateReason" rows="2" required placeholder="e.g. TikTok ROAS is 4.2x with high conversion velocity; shifting unspent search funds."
                                  class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none placeholder:text-slate-400 resize-none"></textarea>
                    </div>
                </div>

                <div class="px-4 py-2 sm:px-5 sm:py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                    <button type="button" @click="reallocateModal = false" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-white transition cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs transition cursor-pointer flex items-center space-x-1 shrink-0">
                        <span>Submit for CMO Approval →</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
