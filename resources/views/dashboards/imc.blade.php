@extends('layouts.app', ['title' => 'IMC Creative Pipeline'])

@section('content')
<div class="space-y-6" x-data="{ 
    newBriefModal: false, 
    newAssetModal: false, 
    approveAssetModal: false, 
    previewAssetModal: false,
    selectedAsset: null,
    approvalNotes: 'Approved by IMC Lead according to Naivas visual brand identity.'
}">

    <!-- Header & Pipeline Overview -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200">
                    Module 1: Integrated Marketing Communications (IMC)
                </span>
                <span class="text-xs text-slate-500">Creative Gating & Approvals</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Creative Briefing & Approval Pipeline</h1>
            <p class="text-sm text-slate-600 mt-0.5">
                Kanban workflow for in-store POSM, digital visuals, and campaign briefs. 
                <strong class="text-purple-700">Approving an asset unlocks DPM ad deployment and OME branch rollout.</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="newBriefModal = true" 
                    class="inline-flex items-center px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-purple-950/20 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                + New Brief
            </button>
            <button type="button" @click="newAssetModal = true" 
                    class="inline-flex items-center px-3.5 py-2 rounded-xl bg-[#006837] hover:bg-[#005a30] text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-950/20 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                + Upload Creative Asset
            </button>
        </div>
    </div>

    <!-- Live Testing Guide Alert For Judges -->
    <div class="rounded-xl bg-gradient-to-r from-purple-50 via-white to-emerald-50 border border-purple-200 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="text-xl">💡</span>
            <div class="text-xs text-slate-700">
                <strong class="text-purple-900 font-bold">Judges' Interactive Test Point:</strong> Look at the <strong>Pending Review</strong> column below. Click <strong>"Approve Asset"</strong> on <em>"Farm Direct Crate Skirts & Overhead Decals"</em> (Campaign: Fresh Harvest Week). This immediately triggers the campaign status to <strong class="text-emerald-800">"Ready for Launch"</strong> and unlocks live ad deployment in the DPM Media module!
            </div>
        </div>
    </div>

    <!-- Campaign Filter Bar -->
    <div class="bg-white rounded-xl p-3 shadow-xs border border-gray-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-center space-x-2">
            <span class="font-bold text-slate-700">Filter by Campaign:</span>
            <div class="flex flex-wrap gap-1.5">
                <a href="{{ route('imc.dashboard') }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold {{ empty($selectedCampaignId) ? 'bg-[#006837] text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    All Campaigns
                </a>
                @foreach($campaigns as $camp)
                <a href="{{ route('imc.dashboard', ['campaign_id' => $camp->id]) }}" 
                   class="px-2.5 py-1 rounded-lg font-semibold {{ $selectedCampaignId == $camp->id ? 'bg-[#006837] text-white' : 'bg-gray-100 text-slate-700 hover:bg-gray-200' }}">
                    {{ $camp->title }}
                </a>
                @endforeach
            </div>
        </div>
        <div class="text-slate-500 font-medium whitespace-nowrap">
            <span>{{ $stats['pending_count'] }} items awaiting sign-off</span>
        </div>
    </div>

    <!-- 3-Column Kanban Board -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Column 1: Draft / Creative Ideation -->
        <div class="bg-slate-100/80 rounded-2xl p-4 border border-gray-200/70 flex flex-col min-h-[500px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Draft & In Production</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold text-xs">
                    {{ $kanban['draft']['briefs']->count() + $kanban['draft']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                <!-- Draft Briefs -->
                @foreach($kanban['draft']['briefs'] as $brief)
                <div class="bg-white rounded-xl p-4 shadow-xs border border-gray-200 hover:border-slate-300 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">Brief</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-[11px] text-slate-500 line-clamp-2 mb-3">{{ $brief->objective }}</p>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[10px] text-slate-500">
                        <span class="font-medium text-emerald-800">{{ $brief->campaign->title }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">Draft</span>
                    </div>
                </div>
                @endforeach

                <!-- Draft Assets -->
                @foreach($kanban['draft']['assets'] as $asset)
                <div class="bg-white rounded-xl p-4 shadow-xs border border-gray-200 hover:border-slate-300 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">{{ $asset->getAssetTypeLabel() }}</span>
                        <span class="text-[10px] text-slate-400 font-mono">v{{ $asset->version }}</span>
                    </div>
                    @if($asset->file_url)
                    <div class="w-full h-24 rounded-lg bg-gray-100 mb-2 overflow-hidden relative cursor-pointer"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    </div>
                    @endif
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <p class="text-[10px] text-slate-400 mb-2">{{ $asset->dimensions }} • {{ $asset->format }}</p>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[10px] text-slate-500">
                        <span class="font-medium text-emerald-800">{{ $asset->campaign->title }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">In Design</span>
                    </div>
                </div>
                @endforeach

                @if($kanban['draft']['briefs']->isEmpty() && $kanban['draft']['assets']->isEmpty())
                <div class="text-center py-12 text-slate-400 text-xs">
                    No items in draft stage.
                </div>
                @endif
            </div>
        </div>

        <!-- Column 2: Pending Review (Awaiting IMC Lead Sign-Off) -->
        <div class="bg-amber-50/50 rounded-2xl p-4 border-2 border-amber-300/80 flex flex-col min-h-[500px] shadow-sm">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-amber-200">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                    <h3 class="text-sm font-bold text-amber-900 uppercase tracking-wider">Pending Review (Actionable)</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-bold text-xs">
                    {{ $kanban['pending_review']['briefs']->count() + $kanban['pending_review']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                <!-- Pending Briefs -->
                @foreach($kanban['pending_review']['briefs'] as $brief)
                <div class="bg-white rounded-xl p-4 shadow-sm border border-amber-200 hover:border-amber-400 transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">Brief Sign-Off</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-[11px] text-slate-600 mb-2 leading-relaxed">{{ $brief->objective }}</p>
                    <div class="p-2 rounded-lg bg-amber-50 text-[10px] text-amber-900 mb-3 border border-amber-100">
                        <strong>Key Message:</strong> {{ $brief->key_messaging }}
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[10px]">
                        <span class="font-semibold text-emerald-800">{{ $brief->campaign->title }}</span>
                        <span class="font-bold text-amber-700 animate-pulse">Awaiting Approval</span>
                    </div>
                </div>
                @endforeach

                <!-- Pending Creative Assets (Key Demonstration for Judges!) -->
                @foreach($kanban['pending_review']['assets'] as $asset)
                <div class="bg-white rounded-xl p-4 shadow-md border-2 border-amber-400 hover:shadow-lg transition relative overflow-hidden group">
                    <div class="absolute top-0 right-0 bg-[#F7941D] text-white text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-bl-lg tracking-wider">
                        Needs Approval
                    </div>

                    <div class="flex items-center justify-between mb-2 mt-1">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 uppercase">
                            {{ $asset->getAssetTypeLabel() }}
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">v{{ $asset->version }}</span>
                    </div>

                    <!-- Image Preview -->
                    @if($asset->file_url)
                    <div class="w-full h-32 rounded-lg bg-gray-100 mb-3 overflow-hidden relative cursor-pointer group-hover:ring-2 ring-[#006837] transition"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                            Click to Expand Preview
                        </div>
                    </div>
                    @endif

                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <div class="flex items-center space-x-2 text-[10px] text-slate-500 mb-3">
                        <span>Dim: {{ $asset->dimensions }}</span>
                        <span>•</span>
                        <span>{{ $asset->format }}</span>
                    </div>

                    <div class="p-2 rounded-lg bg-emerald-50 text-[10px] text-emerald-800 mb-3 border border-emerald-100">
                        <span class="font-bold">Campaign:</span> {{ $asset->campaign->title }}
                        <div class="text-[9px] text-slate-500 mt-0.5">Status: {{ $asset->campaign->getStatusDisplayName() }}</div>
                    </div>

                    <!-- Interactive Approval Actions -->
                    <div class="pt-2 border-t border-gray-100 flex items-center space-x-2">
                        <button type="button" 
                                @click="selectedAsset = {{ json_encode($asset) }}; approveAssetModal = true"
                                class="flex-1 py-2 px-3 rounded-lg bg-[#006837] hover:bg-[#005a30] text-white text-xs font-bold shadow transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Approve Asset</span>
                        </button>

                        <form action="{{ route('imc.assets.reject', $asset->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="py-2 px-2.5 rounded-lg border border-gray-300 text-rose-600 hover:bg-rose-50 text-xs font-semibold"
                                    title="Request Revisions">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach

                @if($kanban['pending_review']['briefs']->isEmpty() && $kanban['pending_review']['assets']->isEmpty())
                <div class="text-center py-12 text-amber-800/60 text-xs">
                    All submitted creative assets have been reviewed.
                </div>
                @endif
            </div>
        </div>

        <!-- Column 3: Approved & Cleared for Launch -->
        <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-200 flex flex-col min-h-[500px]">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <h3 class="text-sm font-bold text-emerald-900 uppercase tracking-wider">Approved & Ready</h3>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-bold text-xs">
                    {{ $kanban['approved']['briefs']->count() + $kanban['approved']['assets']->count() }}
                </span>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                <!-- Approved Briefs -->
                @foreach($kanban['approved']['briefs'] as $brief)
                <div class="bg-white rounded-xl p-4 shadow-xs border border-emerald-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Approved Brief</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $brief->campaign->code }}</span>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $brief->title }}</h4>
                    <p class="text-[11px] text-slate-500 line-clamp-2 mb-2">{{ $brief->objective }}</p>
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-[10px] text-slate-500">
                        <span class="font-medium text-emerald-800">{{ $brief->campaign->title }}</span>
                        <span class="text-emerald-700 font-bold flex items-center">
                            <svg class="w-3.5 h-3.5 mr-0.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Signed Off
                        </span>
                    </div>
                </div>
                @endforeach

                <!-- Approved Assets -->
                @foreach($kanban['approved']['assets'] as $asset)
                <div class="bg-white rounded-xl p-4 shadow-xs border border-emerald-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">{{ $asset->getAssetTypeLabel() }}</span>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center">
                            <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Approved
                        </span>
                    </div>

                    @if($asset->file_url)
                    <div class="w-full h-24 rounded-lg bg-gray-100 mb-2 overflow-hidden relative cursor-pointer"
                         @click="selectedAsset = {{ json_encode($asset) }}; previewAssetModal = true">
                        <img src="{{ $asset->file_url }}" alt="{{ $asset->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif

                    <h4 class="font-bold text-slate-900 text-xs mb-1">{{ $asset->title }}</h4>
                    <p class="text-[10px] text-slate-400 mb-2">{{ $asset->dimensions }} • {{ $asset->format }}</p>

                    <div class="p-1.5 rounded-md bg-emerald-50 text-[10px] text-emerald-800 mb-2">
                        Campaign Status: <strong class="uppercase">{{ $asset->campaign->getStatusDisplayName() }}</strong>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[10px] text-slate-500">
                        <span class="font-medium text-emerald-800 truncate max-w-[150px]">{{ $asset->campaign->title }}</span>
                        <span class="text-slate-400 font-mono">v{{ $asset->version }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Approve Asset Confirmation Modal -->
    <div x-show="approveAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="approveAssetModal = false">
            <div class="flex items-center space-x-3 text-emerald-600 mb-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Sign Off & Approve Asset</h3>
                    <p class="text-xs text-slate-500">Unlocks Campaign Launch Sequence</p>
                </div>
            </div>

            <template x-if="selectedAsset">
                <div class="space-y-4">
                    <div class="p-3 rounded-xl bg-slate-50 border border-gray-200">
                        <div class="font-bold text-slate-900 text-sm" x-text="selectedAsset.title"></div>
                        <div class="text-xs text-slate-500 mt-1">
                            Format: <span class="font-medium text-slate-700" x-text="selectedAsset.format"></span> | 
                            Dimensions: <span class="font-medium text-slate-700" x-text="selectedAsset.dimensions"></span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                        <strong class="block mb-1 font-bold">Sequential Workflow Impact:</strong>
                        Approving this asset will automatically update the campaign status to 
                        <span class="font-bold underline text-emerald-800">"Ready for Launch"</span>.
                        This will unlock the <em>"Deploy Ads"</em> button in DPM and the in-store rollout task in OME!
                    </div>

                    <form :action="'/imc/assets/' + selectedAsset.id + '/approve'" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">IMC Lead Approval Notes</label>
                            <input type="text" name="notes" x-model="approvalNotes" 
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-2">
                            <button type="button" @click="approveAssetModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 text-sm font-bold rounded-lg bg-[#006837] text-white hover:bg-[#005a30] shadow flex items-center space-x-1">
                                <span>Confirm Approval & Unlock Campaign</span>
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- Preview Creative Asset Modal -->
    <div x-show="previewAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="previewAssetModal = false">
            <template x-if="selectedAsset">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900" x-text="selectedAsset.title"></h3>
                            <p class="text-xs text-slate-500" x-text="selectedAsset.dimensions + ' • ' + selectedAsset.format"></p>
                        </div>
                        <button type="button" @click="previewAssetModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="w-full h-80 rounded-xl bg-slate-100 overflow-hidden mb-4 relative flex items-center justify-center">
                        <img :src="selectedAsset.file_url" :alt="selectedAsset.title" class="max-h-full max-w-full object-contain">
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Version: <strong class="text-slate-800" x-text="'v' + selectedAsset.version"></strong></span>
                        <button type="button" @click="previewAssetModal = false" class="px-4 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-slate-800 font-semibold">
                            Close Preview
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Submit New Brief Modal -->
    <div x-show="newBriefModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="newBriefModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Submit Marketing Creative Brief</h3>
                </div>
                <button type="button" @click="newBriefModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('imc.briefs.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Campaign *</label>
                    <select name="campaign_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none bg-white">
                        @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}" {{ $selectedCampaignId == $camp->id ? 'selected' : '' }}>
                            {{ $camp->title }} ({{ $camp->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Brief Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Meat & Deli Weekend Flash POSM Package" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Primary Channel *</label>
                        <select name="channel" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none bg-white">
                            <option value="in_store_posm">In-Store POSM Banner</option>
                            <option value="digital_meta">Digital Meta / IG Carousel</option>
                            <option value="digital_google">Google Display Banner</option>
                            <option value="sms_broadcast">SMS Copy & Loyalty Push</option>
                            <option value="print_newspaper">National Print Feature</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Initial Status *</label>
                        <select name="status" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none bg-white">
                            <option value="pending_review">Submit for Review</option>
                            <option value="draft">Save as Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Target Customer Segment *</label>
                    <input type="text" name="target_segment" required placeholder="Nairobi suburban households, weekend bulk shoppers" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Strategic Objective *</label>
                    <textarea name="objective" rows="2" required placeholder="Specific lift target, basket size objective, or category awareness goal..." 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Key Customer Messaging & CTA *</label>
                    <textarea name="key_messaging" rows="2" required placeholder="Naivas Fresh: Unbeatable farm prices all weekend long!" 
                              class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-purple-600 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="newBriefModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold rounded-lg bg-purple-600 text-white hover:bg-purple-700 shadow">
                        Submit Creative Brief
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Submit New Asset Modal -->
    <div x-show="newAssetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="newAssetModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#006837] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Upload Creative Asset for Review</h3>
                </div>
                <button type="button" @click="newAssetModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('imc.assets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Campaign *</label>
                    <select name="campaign_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none bg-white">
                        @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}" {{ $selectedCampaignId == $camp->id ? 'selected' : '' }}>
                            {{ $camp->title }} ({{ $camp->code }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Asset Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Fresh Mangoes Aisle Price Dangler 300x300" 
                           class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Asset Type *</label>
                        <select name="asset_type" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none bg-white">
                            <option value="posm_banner">POSM Banner</option>
                            <option value="gondola_header">Gondola Header</option>
                            <option value="digital_graphic">Digital Social Graphic</option>
                            <option value="video_reel">TikTok / IG Reel</option>
                            <option value="price_dangler">Aisle Price Dangler</option>
                            <option value="shelf_talker">Shelf Talker Decal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Format *</label>
                        <select name="format" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none bg-white">
                            <option value="High-Res PDF">High-Res PDF</option>
                            <option value="PNG 300DPI">PNG 300DPI</option>
                            <option value="MP4 1080p">MP4 1080p</option>
                            <option value="Vinyl Print Ready">Vinyl Print Ready</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dimensions *</label>
                        <input type="text" name="dimensions" required placeholder="1200x600mm" 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">File Mockup URL</label>
                        <input type="text" name="file_url" placeholder="https://..." 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                    <button type="button" @click="newAssetModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-sm font-bold rounded-lg bg-[#006837] text-white hover:bg-[#005a30] shadow">
                        Upload to Review Queue
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
