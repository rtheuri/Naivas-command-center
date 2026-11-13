@extends('layouts.app', ['title' => 'External Agency Partner Portal'])

@section('content')
<div class="space-y-6" x-data="{
    openModal: false,
    selectedCampaignId: '{{ $campaigns->first()->id ?? '' }}',
    selectedBriefId: '',
    selectedBriefTitle: '',
    selectedBriefObjective: '',
    selectedBriefSegment: '',
    selectedBriefMessaging: '',
    assetTitle: '',
    assetType: 'posm_banner',
    dimensions: '1920x1080px',
    format: 'High-Res CMYK Print Ready PDF',
    fileUrl: '',

    openDeliverableModal(campaignId, briefId = null, briefTitle = '', objective = '', segment = '', messaging = '', defaultAssetType = 'posm_banner') {
        this.selectedCampaignId = campaignId;
        this.selectedBriefId = briefId || '';
        this.selectedBriefTitle = briefTitle;
        this.selectedBriefObjective = objective;
        this.selectedBriefSegment = segment;
        this.selectedBriefMessaging = messaging;
        this.assetTitle = briefTitle ? briefTitle + ' - Master Artwork' : '';
        this.assetType = defaultAssetType;
        if (defaultAssetType === 'posm_banner') {
            this.dimensions = '3000x2400mm';
            this.format = 'Vinyl Banner Print Ready PDF';
        } else if (defaultAssetType === 'digital_graphic') {
            this.dimensions = '1080x1080px';
            this.format = 'Digital PNG (RGB)';
        } else if (defaultAssetType === 'gondola_header') {
            this.dimensions = '3000x800mm';
            this.format = 'Mounted PVC Board Print';
        } else {
            this.dimensions = '1920x1080px';
            this.format = 'High-Res CMYK Print Ready PDF';
        }
        this.openModal = true;
    }
}" @open-agency-modal.window="openDeliverableModal('{{ $campaigns->first()->id ?? '' }}')">

    <!-- Top Breadcrumb & Scoped Tenant Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>External Partner Workspace</span>
                <span>•</span>
                <span class="text-[#F7941D] font-semibold">Persona 6: Agency Partner</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center space-x-2">
                <span>Apex Brand Agency</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-orange-50 text-[#F7941D] border border-orange-200 font-bold tracking-wider">
                    Agency ID: {{ $agencyId }}
                </span>
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Permission-isolated agency tenant. Scoped exclusively to campaigns explicitly assigned to Apex Agency by Naivas Head of Marketing.
            </p>
        </div>

        <button type="button" @click="openDeliverableModal('{{ $campaigns->first()->id ?? '' }}')" 
                class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs flex items-center space-x-2 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Submit Agency Deliverable</span>
        </button>
    </div>

    <!-- Agency Performance & SLA Scorecard -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Assigned Campaigns</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $campaigns->count() }}</p>
            <span class="text-[11px] text-blue-600 font-semibold mt-1 block">Permission-scoped isolation</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Deliverables</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalDeliverables }}</p>
            <span class="text-[11px] text-slate-500 mt-1 block">{{ $approvedDeliverables }} Approved • {{ $pendingDeliverables }} In Review</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Contracted SLA Compliance</span>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $slaComplianceRate }}%</p>
            <span class="text-[11px] text-emerald-700 font-semibold mt-1 block">Target > 90% (On Track)</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Avg Turnaround Time</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">18.4 hrs</p>
            <span class="text-[11px] text-slate-500 mt-1 block">Contracted benchmark: 24.0 hrs</span>
        </div>

    </div>

    <!-- Assigned Campaigns Section -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Assigned Naivas Campaign Workspaces</h3>
                <p class="text-xs text-slate-500">Only campaigns explicitly allocated to Agency ID <code class="text-blue-600 font-mono">{{ $agencyId }}</code> appear in this view. Click any brief below to craft artwork.</p>
            </div>
            <span class="text-xs text-slate-400 font-medium">Tenant Filter: Active</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @forelse($campaigns as $camp)
            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition space-y-4 flex flex-col justify-between">
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#F7941D] bg-orange-50 px-2.5 py-1 rounded border border-orange-200">{{ $camp->code }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                            @if($camp->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($camp->status === 'ready_for_launch') bg-orange-50 text-[#F7941D] border border-orange-200
                            @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                            {{ str_replace('_', ' ', $camp->status) }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $camp->title }}</h4>
                        <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $camp->description }}</p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-2 px-3 rounded-lg bg-white border border-slate-100 text-xs text-slate-600">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Briefs</span>
                            <span class="font-bold text-slate-800">{{ $camp->briefs->count() }} Active</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Assets</span>
                            <span class="font-bold text-slate-800">{{ $camp->assets->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Target Launch</span>
                            <span class="font-bold text-slate-800">{{ $camp->end_date?->format('M d, Y') ?? 'Active' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Briefs Assigned to Agency in this Campaign -->
                <div class="pt-3 border-t border-slate-200 space-y-2.5">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Assigned Briefs • Click to Create & Submit Asset</span>
                        </span>
                        <span class="text-slate-400 font-mono text-[10px]">{{ $camp->briefs->count() }} Brief(s)</span>
                    </div>

                    <div class="space-y-2">
                        @forelse($camp->briefs as $brief)
                        @php
                            $hasAsset = $brief->assets->isNotEmpty();
                            $latestAsset = $brief->assets->last();
                        @endphp
                        <div @click="openDeliverableModal('{{ $camp->id }}', '{{ $brief->id }}', '{{ addslashes($brief->title) }}', '{{ addslashes($brief->objective) }}', '{{ addslashes($brief->target_segment) }}', '{{ addslashes($brief->key_messaging) }}', '{{ $brief->channel === 'in_store_posm' ? 'posm_banner' : 'digital_graphic' }}')"
                             class="p-3 rounded-lg border border-slate-200 bg-white hover:border-[#F7941D] hover:ring-1 hover:ring-[#F7941D] hover:shadow-sm transition cursor-pointer group space-y-1.5">
                            
                            <div class="flex items-start justify-between gap-2">
                                <h5 class="text-xs font-bold text-slate-900 group-hover:text-[#F7941D] transition flex items-center space-x-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#F7941D] shrink-0"></span>
                                    <span>{{ $brief->title }}</span>
                                </h5>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider shrink-0
                                    @if($brief->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($brief->status === 'pending_review') bg-amber-50 text-amber-700 border border-amber-200 animate-pulse
                                    @else bg-orange-50 text-[#F7941D] border border-orange-200 @endif">
                                    {{ $brief->status === 'approved' ? '✓ Approved by IMC' : ($brief->status === 'pending_review' ? '⏳ In IMC Review' : '⚡ In Design') }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-500 line-clamp-1 italic">
                                "{{ $brief->objective }}"
                            </p>

                            <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1.5 border-t border-slate-100">
                                <span class="flex items-center space-x-1">
                                    <span class="font-semibold text-slate-700">Channel:</span>
                                    <span class="uppercase font-mono text-[9px] px-1.5 py-0.5 bg-slate-100 rounded text-slate-600">{{ str_replace('_', ' ', $brief->channel) }}</span>
                                </span>

                                <span class="text-[#F7941D] font-bold group-hover:underline flex items-center space-x-1 text-[11px]">
                                    <span>{{ $hasAsset ? '↺ Update Deliverable →' : '+ Craft Asset & Submit →' }}</span>
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-center text-[11px] text-slate-400">
                            No specific briefs in this campaign. You can still submit a general deliverable below.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-400 text-xs">No campaigns assigned to this agency ID.</div>
            @endforelse
        </div>
    </div>

    <!-- Agency Deliverables Stream -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Delivered Creative Assets</h3>
                <p class="text-xs text-slate-500">Artwork submissions delivered to Naivas IMC Lead (Sarah Jenkins) for sign-off.</p>
            </div>
            <span class="text-xs text-slate-400 font-mono font-medium">{{ $allAssets->count() }} Total Assets</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase text-[10px] font-bold">
                        <th class="py-2.5">Asset Title</th>
                        <th class="py-2.5">Associated Brief & Campaign</th>
                        <th class="py-2.5">Format & Specs</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5">Sign-Off Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allAssets as $asset)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 font-semibold text-slate-900">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full @if($asset->status === 'approved') bg-emerald-500 @elseif($asset->status === 'pending_review') bg-amber-500 animate-pulse @else bg-slate-400 @endif"></span>
                                <span>{{ $asset->title }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-slate-600">
                            <div class="font-medium text-slate-800">{{ $asset->brief ? $asset->brief->title : 'General Deliverable' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $asset->campaign->title }} ({{ $asset->campaign->code }})</div>
                        </td>
                        <td class="py-3 text-slate-500">{{ $asset->dimensions }} • {{ $asset->format }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if($asset->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($asset->status === 'pending_review') bg-amber-50 text-amber-700 border border-amber-200 animate-pulse
                                @elseif($asset->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ str_replace('_', ' ', $asset->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-500">
                            {{ $asset->approvedBy ? $asset->approvedBy->name : 'Awaiting Sarah Jenkins (IMC Lead)' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">No assets delivered yet. Select a brief above to craft and upload artwork.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agency Deliverable Modal (Supports Clicking Any Brief) -->
    <div x-show="openModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4">
        <div class="bg-white rounded-xl sm:rounded-2xl max-w-lg w-full max-h-[88vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden my-auto" @click.away="openModal = false">
            
            <div class="px-4 py-2.5 sm:px-5 sm:py-3 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
                <div>
                    <h3 class="text-sm font-bold text-slate-900" x-text="selectedBriefTitle ? 'Upload Deliverable for: ' + selectedBriefTitle : 'Upload Agency Deliverable (Apex Agency)'"></h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500">Attach artwork proposal for IMC Lead (Sarah Jenkins) review and sign-off</p>
                </div>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('agency.assets.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                @csrf
                
                <input type="hidden" name="brief_id" :value="selectedBriefId">

                <div class="p-3.5 sm:p-4 space-y-3 overflow-y-auto flex-1 text-xs overscroll-contain">
                    
                    <!-- Brief Requirements Card (Visible when brief is selected) -->
                    <div x-show="selectedBriefTitle" class="p-3 bg-orange-50/70 border border-orange-200 rounded-lg space-y-1.5 text-[11px]">
                        <div class="flex items-center justify-between text-[10px] font-bold text-[#F7941D] uppercase tracking-wider">
                            <span>IMC Brief Specifications</span>
                            <span class="bg-orange-100 text-orange-800 px-1.5 py-0.2 rounded font-mono">Assigned to Apex</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase font-semibold block">Objective:</span>
                            <p class="text-slate-800 font-medium" x-text="selectedBriefObjective || 'Creative brand execution'"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-orange-200/60 text-[10px]">
                            <div>
                                <span class="text-slate-400 uppercase font-semibold block">Target Segment:</span>
                                <span class="text-slate-700 font-medium" x-text="selectedBriefSegment || 'Naivas Shoppers'"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 uppercase font-semibold block">Key Message:</span>
                                <span class="text-slate-700 font-medium truncate block" x-text="selectedBriefMessaging || 'Quality & Savings'"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Assigned Campaign *</label>
                        <select name="campaign_id" x-model="selectedCampaignId" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}">{{ $camp->code }} - {{ $camp->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Artwork / Asset Title *</label>
                        <input type="text" name="title" x-model="assetTitle" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none" placeholder="e.g. Back to School Station Shelf Banner">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Asset Type *</label>
                            <select name="asset_type" x-model="assetType" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                                <option value="posm_banner">POSM Banner</option>
                                <option value="gondola_header">Gondola Header</option>
                                <option value="digital_graphic">Digital Social Graphic</option>
                                <option value="video_reel">TikTok / IG Reel</option>
                                <option value="price_dangler">Aisle Price Dangler</option>
                                <option value="shelf_talker">Shelf Talker Decal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Dimensions *</label>
                            <input type="text" name="dimensions" x-model="dimensions" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Format *</label>
                        <input type="text" name="format" x-model="format" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Deliverable File URL (Optional)</label>
                        <input type="text" name="file_url" x-model="fileUrl" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none" placeholder="Leave blank for automatic high-res mock image">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">If omitted, a high-resolution retail mockup matching asset type will be automatically generated.</span>
                    </div>
                </div>

                <div class="px-4 py-2 sm:px-5 sm:py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                    <span class="text-[10px] text-slate-500 font-medium">Will post directly to Sarah Jenkins (IMC Lead)</span>
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="openModal = false" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-white transition cursor-pointer">Cancel</button>
                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-white bg-[#F7941D] hover:bg-[#e07f0f] rounded-lg shadow-xs transition cursor-pointer shrink-0 flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Post Asset for IMC Review →</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
