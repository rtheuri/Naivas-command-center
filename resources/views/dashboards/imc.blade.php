@extends('layouts.app', ['title' => 'IMC Creative Pipeline'])

@section('content')
<div class="space-y-6" x-data="{ 
    newBriefModal: false, 
    newAssetModal: false, 
    approveAssetModal: false, 
    previewAssetModal: false,
    rejectAssetModal: false,
    selectedAsset: null,
    approvalNotes: 'Approved by IMC Lead according to Naivas visual brand identity.',
    rejectNotes: 'Please refine headline copy and adjust imagery for high contrast brand alignment.'
}">

    <!-- Header -->
    <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-[#F7941D] uppercase tracking-wider block mb-1">Module 1: Creative</span>
            <h1 class="text-2xl font-bold text-slate-900">Creative Briefing & Approval Pipeline</h1>
            <p class="text-xs text-slate-500 mt-1">
                Manage briefs and POSM assets. Approving an asset advances the campaign to <strong class="text-slate-800">"Ready for Launch"</strong>.
            </p>
        </div>

        <div>
            <button type="button" @click="newBriefModal = true" 
                    class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ New Brief</span>
            </button>
        </div>
    </div>

    <!-- Campaign Filter Bar -->
    <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2">
            <span class="font-medium text-slate-500">Filter by Campaign:</span>
            <div class="flex flex-wrap gap-1">
                <a href="{{ route('imc.dashboard') }}" 
                   class="px-2.5 py-1 rounded-md font-medium transition {{ empty($selectedCampaignId) ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    All
                </a>
                @foreach($campaigns as $camp)
                <a href="{{ route('imc.dashboard', ['campaign_id' => $camp->id]) }}" 
                   class="px-2.5 py-1 rounded-md font-medium transition {{ $selectedCampaignId == $camp->id ? 'bg-[#F7941D] text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $camp->title }}
                </a>
                @endforeach
            </div>
        </div>
        <div class="text-slate-400">
            {{ $stats['pending_count'] }} awaiting review
        </div>
    </div>

    <!-- Clean 3-Column Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Column 1: Draft -->
        <div class="bg-slate-100/70 rounded-xl p-4 border border-slate-200/80 flex flex-col min-h-[500px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Draft & In Design</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold text-[11px]">
                    {{ $kanban['draft']['briefs']->count() + $kanban['draft']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                @foreach($kanban['draft']['briefs'] as $brief)
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                        <span class="font-semibold uppercase text-slate-500">Brief</span>
                        <span class="font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $brief->objective }}</p>
                    <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-100">
                        {{ $brief->campaign->title }}
                    </div>
                </div>
                @endforeach

                @foreach($kanban['draft']['assets'] as $asset)
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                        <span class="font-semibold text-slate-600 uppercase">{{ $asset->getAssetTypeLabel() }}</span>
                        <span>v{{ $asset->version }}</span>
                    </div>
                    @if($asset->file_url)
                    <div class="w-full h-24 rounded bg-slate-100 mb-2 overflow-hidden cursor-pointer"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <p class="text-[11px] text-slate-500 mb-2">{{ $asset->dimensions }} • {{ $asset->format }}</p>
                    <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-100">
                        {{ $asset->campaign->title }}
                    </div>
                </div>
                @endforeach

                @if($kanban['draft']['briefs']->isEmpty() && $kanban['draft']['assets']->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs">
                    No items in draft.
                </div>
                @endif
            </div>
        </div>

        <!-- Column 2: Pending Review -->
        <div class="bg-slate-100/70 rounded-xl p-4 border border-slate-200/80 flex flex-col min-h-[500px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pending Review</h3>
                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-[#F7941D] font-bold text-[11px]">
                    {{ $kanban['pending_review']['briefs']->count() + $kanban['pending_review']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                @foreach($kanban['pending_review']['briefs'] as $brief)
                @php
                    $briefSla = $brief->sla_expires_at ?: now()->addHours(2)->addMinutes(14);
                    $briefSecs = max(60, now()->diffInSeconds($briefSla, false));
                @endphp
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                        <span class="font-semibold uppercase text-slate-500">Brief</span>
                        <span class="font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-xs text-slate-500 mb-2">{{ $brief->objective }}</p>
                    <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span>{{ $brief->campaign->title }}</span>
                        @if($brief->assignedTo)
                        <span class="text-[#F7941D] font-medium">Assigned: {{ $brief->assignedTo->name }}</span>
                        @endif
                    </div>

                    <!-- SLA Countdown Clock -->
                    <div x-data="slaCountdown({{ $briefSecs }})" class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-1.5"
                             :class="isUrgent ? 'text-rose-600 font-bold animate-pulse' : 'text-amber-600 font-medium'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="formatted + ' Remaining'"></span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                              :class="isUrgent ? 'bg-rose-100 text-rose-800 border border-rose-300 animate-pulse' : 'bg-amber-100 text-amber-800 border border-amber-200'">
                            <span x-text="isUrgent ? 'SLA Risk' : 'SLA Target'"></span>
                        </span>
                    </div>
                </div>
                @endforeach

                @foreach($kanban['pending_review']['assets'] as $asset)
                @php
                    $assetSla = $asset->sla_expires_at ?: now()->addHours(1)->addMinutes(48);
                    $assetSecs = max(60, now()->diffInSeconds($assetSla, false));
                @endphp
                <div class="bg-white rounded-lg p-4 border border-slate-300 shadow-sm relative group">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                        <span class="font-semibold text-slate-700 uppercase">{{ $asset->getAssetTypeLabel() }}</span>
                        <span>v{{ $asset->version }}</span>
                    </div>

                    @if($asset->file_url)
                    <div class="w-full h-32 rounded bg-slate-100 mb-2 overflow-hidden cursor-pointer"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif

                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <p class="text-[11px] text-slate-500 mb-2">{{ $asset->dimensions }} • {{ $asset->format }}</p>

                    <div class="text-[10px] text-slate-500 pb-2 mb-2 border-b border-slate-100">
                        Campaign: <span class="font-medium text-slate-800">{{ $asset->campaign->title }}</span>
                    </div>

                    <!-- Ticking SLA Countdown Clock (Pulsing Red Under 2 Hours) -->
                    <div x-data="slaCountdown({{ $assetSecs }})" class="mb-3 p-2 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-1.5"
                             :class="isUrgent ? 'text-rose-600 font-bold animate-pulse' : 'text-amber-600 font-medium'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="formatted + ' Remaining'"></span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                              :class="isUrgent ? 'bg-rose-100 text-rose-800 border border-rose-300 animate-pulse' : 'bg-amber-100 text-amber-800 border border-amber-200'">
                            <span x-text="isUrgent ? 'Critical SLA' : 'SLA Target'"></span>
                        </span>
                    </div>

                    <!-- Approval Actions (Request Redo opens notes modal) -->
                    <div class="space-y-2 pt-2">
                        <button type="button" 
                                @click="selectedAsset = {{ json_encode($asset) }}; rejectAssetModal = true"
                                class="w-full py-1.5 px-3 rounded-lg border border-slate-300 hover:border-rose-300 bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-semibold transition text-center flex items-center justify-center space-x-1.5 shadow-xs"
                                title="Add notes and send back to Creative Studio for revisions">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <span>Request Redo (Return to Creative Studio)</span>
                        </button>

                        <button type="button" 
                                @click="selectedAsset = {{ json_encode($asset) }}; approveAssetModal = true"
                                class="w-full py-2 px-3 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition text-center flex items-center justify-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Approve Asset</span>
                        </button>
                    </div>
                </div>
                @endforeach

                @if($kanban['pending_review']['briefs']->isEmpty() && $kanban['pending_review']['assets']->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs">
                    All submitted creative assets have been signed off.
                </div>
                @endif
            </div>
        </div>

        <!-- Column 3: Approved -->
        <div class="bg-slate-100/70 rounded-xl p-4 border border-slate-200/80 flex flex-col min-h-[500px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Approved & Ready</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold text-[11px]">
                    {{ $kanban['approved']['briefs']->count() + $kanban['approved']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                @foreach($kanban['approved']['briefs'] as $brief)
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                        <span class="font-semibold uppercase text-slate-500">Approved Brief</span>
                        <span class="font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $brief->objective }}</p>
                    <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-100">
                        {{ $brief->campaign->title }}
                    </div>
                </div>
                @endforeach

                @foreach($kanban['approved']['assets'] as $asset)
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-xs">
                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-2">
                        <span class="font-semibold text-slate-700 uppercase">{{ $asset->getAssetTypeLabel() }}</span>
                        <span class="text-slate-600 font-medium">Approved</span>
                    </div>

                    @if($asset->file_url)
                    <div class="w-full h-24 rounded bg-slate-100 mb-2 overflow-hidden cursor-pointer"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif

                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <p class="text-[11px] text-slate-500 mb-2">{{ $asset->dimensions }} • {{ $asset->format }}</p>

                    <div class="text-[10px] text-slate-400 pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span>{{ $asset->campaign->title }}</span>
                        <span class="font-medium text-slate-700">{{ $asset->campaign->getStatusDisplayName() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Approve Asset Modal -->
    <div x-show="approveAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="approveAssetModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Approve Creative Asset</h3>
            <p class="text-xs text-slate-500 mb-4">Advances parent campaign status to "Ready for Launch".</p>

            <template x-if="selectedAsset">
                <form :action="'/imc/assets/' + selectedAsset.id + '/approve'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs">
                        <div class="font-semibold text-slate-900" x-text="selectedAsset.title"></div>
                        <div class="text-slate-500 mt-1" x-text="selectedAsset.dimensions + ' • ' + selectedAsset.format"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Approval Notes</label>
                        <input type="text" name="notes" x-model="approvalNotes" 
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="approveAssetModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                            Confirm Approval
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Request Redo / Revision Notes Modal -->
    <div x-show="rejectAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="rejectAssetModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                        ↺
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Request Redo / Design Revision</h3>
                        <p class="text-[11px] text-slate-500">Return this task to Grace Foster in the Creative Studio</p>
                    </div>
                </div>
                <button type="button" @click="rejectAssetModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="selectedAsset">
                <form :action="'/imc/assets/' + selectedAsset.id + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Selected Artwork Proposal</span>
                        <div class="font-bold text-slate-900 mt-0.5" x-text="selectedAsset.title"></div>
                        <div class="text-slate-500 mt-0.5" x-text="selectedAsset.dimensions + ' • ' + selectedAsset.format"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Revision Notes & Feedback for Designer *
                        </label>
                        <p class="text-[11px] text-slate-500 mb-1.5">
                            Specify what needs to be changed (e.g. headline text, discount percentage, colors, or image placement).
                        </p>
                        <textarea name="notes" rows="3" required x-model="rejectNotes"
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none text-slate-800"
                                  placeholder="e.g. Please increase discount prominence to 30% and add Naivas Reward Club card point icon."></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2 border-t border-slate-100">
                        <button type="button" @click="rejectAssetModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs flex items-center space-x-1.5">
                            <span>Send Revision to Creative Studio</span>
                            <span>→</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Preview Creative Asset Modal -->
    <div x-show="previewAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="previewAssetModal = false">
            <template x-if="selectedAsset">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-sm font-bold text-slate-900" x-text="selectedAsset.title"></h3>
                        <button type="button" @click="previewAssetModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="w-full h-72 rounded-lg bg-slate-100 overflow-hidden mb-4 flex items-center justify-center">
                        <img :src="selectedAsset.file_url" :alt="selectedAsset.title" class="max-h-full max-w-full object-contain">
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span x-text="selectedAsset.dimensions + ' • ' + selectedAsset.format"></span>
                        <button type="button" @click="previewAssetModal = false" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Submit New Brief Modal -->
    <div x-show="newBriefModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="newBriefModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Submit Creative Brief</h3>
                <button type="button" @click="newBriefModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('imc.briefs.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Target Campaign *</label>
                    <select name="campaign_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                        @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}" {{ $selectedCampaignId == $camp->id ? 'selected' : '' }}>
                            {{ $camp->title }} ({{ $camp->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Brief Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Meat & Deli Weekend Flash POSM" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Channel *</label>
                        <select name="channel" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="in_store_posm">In-Store POSM</option>
                            <option value="digital_meta">Digital Meta Ads</option>
                            <option value="digital_google">Google Ads</option>
                            <option value="sms_broadcast">SMS Loyalty</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="pending_review">Submit for Review</option>
                            <option value="draft">Save as Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Target Customer Segment</label>
                    <input type="text" name="target_segment" required placeholder="Family shoppers, weekend bulk buyers" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Strategic Objective</label>
                    <textarea name="objective" rows="2" required placeholder="Lift targets..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Key Customer Message</label>
                    <textarea name="key_messaging" rows="2" required placeholder="Naivas Fresh: Farm to fork..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="newBriefModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                        Submit Brief
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit New Asset Modal -->
    <div x-show="newAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="newAssetModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Upload Creative Asset</h3>
                <button type="button" @click="newAssetModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('imc.assets.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Campaign *</label>
                    <select name="campaign_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                        @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}" {{ $selectedCampaignId == $camp->id ? 'selected' : '' }}>
                            {{ $camp->title }} ({{ $camp->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Asset Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Fresh Mangoes Aisle Price Dangler" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Asset Type *</label>
                        <select name="asset_type" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="posm_banner">POSM Banner</option>
                            <option value="gondola_header">Gondola Header</option>
                            <option value="digital_graphic">Digital Graphic</option>
                            <option value="video_reel">Video Reel</option>
                            <option value="price_dangler">Price Dangler</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Format *</label>
                        <select name="format" required class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="High-Res PDF">High-Res PDF</option>
                            <option value="PNG 300DPI">PNG 300DPI</option>
                            <option value="MP4 1080p">MP4 1080p</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Dimensions *</label>
                        <input type="text" name="dimensions" required placeholder="1200x600mm" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">File URL</label>
                        <input type="text" name="file_url" placeholder="https://..." 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="newAssetModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                        Upload to Pipeline
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function slaCountdown(initialSecs) {
        return {
            seconds: Math.floor(initialSecs),
            timer: null,
            init() {
                this.timer = setInterval(() => {
                    if (this.seconds > 0) this.seconds--;
                }, 1000);
            },
            get formatted() {
                let s = Math.max(0, Math.floor(this.seconds));
                let h = Math.floor(s / 3600);
                let m = Math.floor((s % 3600) / 60);
                return (h > 0 ? h + 'h ' : '') + m + 'm';
            },
            get isUrgent() {
                return this.seconds <= 7200; // Under 2 hours remaining triggers pulsating red warning
            }
        };
    }
</script>
@endsection
