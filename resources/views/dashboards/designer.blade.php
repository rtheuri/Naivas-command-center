@extends('layouts.app', ['title' => 'Creative Studio'])

@section('content')
<div class="space-y-6" x-data="{
    briefs: {{ Js::from($briefsData) }},
    filterTab: 'open',
    selectedBriefId: null,

    // Copywriting inputs
    targetAudience: '',
    headline: '',
    bodyCopy: '',
    cta: 'Shop Fresh at Naivas Supermarket Today!',

    // Proposal inputs
    draftTitle: '',
    assetType: 'posm_banner',
    dimensions: '1200x600mm',
    format: 'High-Res PDF Print',
    fileUrl: '',

    // Assistive AI state
    aiChecking: false,
    aiResult: null,
    swahiliGenerating: false,
    swahiliResult: null,

    init() {
        if (this.openBriefs.length > 0) {
            this.filterTab = 'open';
            this.selectBrief(this.openBriefs[0]);
        } else if (this.pendingBriefs.length > 0) {
            this.filterTab = 'pending';
            this.selectBrief(this.pendingBriefs[0]);
        } else if (this.briefs.length > 0) {
            this.selectBrief(this.briefs[0]);
        }
    },

    get openBriefs() {
        return this.briefs.filter(b => !b.is_approved && !b.has_proposal);
    },

    get pendingBriefs() {
        return this.briefs.filter(b => !b.is_approved && b.has_proposal);
    },

    get approvedBriefs() {
        return this.briefs.filter(b => b.is_approved);
    },

    get currentFilteredBriefs() {
        if (this.filterTab === 'open') return this.openBriefs;
        if (this.filterTab === 'pending') return this.pendingBriefs;
        if (this.filterTab === 'approved') return this.approvedBriefs;
        return this.briefs;
    },

    get selectedBrief() {
        return this.briefs.find(b => b.id == this.selectedBriefId) || this.currentFilteredBriefs[0] || null;
    },

    selectBrief(b) {
        if (!b) return;
        this.selectedBriefId = b.id;
        this.targetAudience = b.target_segment || 'Naivas Shoppers';
        this.headline = b.title || '';
        this.bodyCopy = b.body_copy || b.key_messaging || b.objective || '';
        this.draftTitle = b.title ? (b.title + ' - Final Artwork') : '';
        this.fileUrl = '';
        this.aiResult = null;
        this.swahiliResult = null;
    },

    switchTab(tab) {
        this.filterTab = tab;
        let matches = [];
        if (tab === 'open') matches = this.openBriefs;
        else if (tab === 'pending') matches = this.pendingBriefs;
        else if (tab === 'approved') matches = this.approvedBriefs;

        if (matches.length > 0) {
            this.selectBrief(matches[0]);
        }
    },

    async runAiQualityCheck() {
        this.aiChecking = true;
        this.aiResult = null;
        try {
            const res = await fetch('{{ route('designer.ai.quality_check') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    headline: this.headline,
                    body_copy: this.bodyCopy,
                    cta: this.cta,
                    target_audience: this.targetAudience
                })
            });
            this.aiResult = await res.json();
        } catch (e) {
            console.error(e);
        } finally {
            this.aiChecking = false;
        }
    },

    async generateSwahili() {
        this.swahiliGenerating = true;
        this.swahiliResult = null;
        try {
            const res = await fetch('{{ route('designer.ai.swahili_variant') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    headline: this.headline,
                    body_copy: this.bodyCopy,
                    cta: this.cta
                })
            });
            this.swahiliResult = await res.json();
        } catch (e) {
            console.error(e);
        } finally {
            this.swahiliGenerating = false;
        }
    },

    applySwahili() {
        if (this.swahiliResult) {
            this.headline = this.swahiliResult.swahili_headline;
            this.bodyCopy = this.swahiliResult.swahili_body;
            this.cta = this.swahiliResult.swahili_cta;
            this.draftTitle = this.headline + ' (Swahili)';
        }
    }
}">

    <!-- Top Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Integrated Marketing Communications</span>
                <span>•</span>
                <span class="text-[#F7941D] font-semibold">Persona 3: Creative Studio</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center space-x-2">
                <span>Grace Foster</span>
                <span class="text-sm font-normal text-slate-500">• Creative Studio</span>
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Select an assigned marketing brief to draft copy, run AI quality compliance checks, localize into Kenyan Swahili, and submit design proposals for IMC sign-off.
            </p>
        </div>
    </div>

    <!-- Combined Master-Detail Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT PANEL: Brief Assignments (Master Queue) -->
        <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200 shadow-xs p-4 space-y-4">
            
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Brief Assignments</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Filter by status and select a brief</p>
            </div>

            <!-- 3 Category Tabs -->
            <div class="grid grid-cols-3 gap-1 p-1 bg-slate-100 rounded-lg text-[11px] font-semibold text-slate-600">
                <button type="button" @click="switchTab('open')"
                        :class="filterTab === 'open' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'"
                        class="py-1.5 px-2 rounded-md transition text-center flex items-center justify-center space-x-1">
                    <span>In Design</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800" x-text="openBriefs.length"></span>
                </button>
                <button type="button" @click="switchTab('pending')"
                        :class="filterTab === 'pending' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'"
                        class="py-1.5 px-2 rounded-md transition text-center flex items-center justify-center space-x-1">
                    <span>In Review</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-orange-100 text-orange-800" x-text="pendingBriefs.length"></span>
                </button>
                <button type="button" @click="switchTab('approved')"
                        :class="filterTab === 'approved' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'"
                        class="py-1.5 px-2 rounded-md transition text-center flex items-center justify-center space-x-1">
                    <span>Approved</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800" x-text="approvedBriefs.length"></span>
                </button>
            </div>

            <!-- List of Briefs in Current Tab -->
            <div class="space-y-2.5 max-h-[750px] overflow-y-auto pr-0.5">
                <template x-for="b in currentFilteredBriefs" :key="b.id">
                    <div @click="selectBrief(b)"
                         :class="selectedBrief && selectedBrief.id === b.id 
                                ? 'border-[#F7941D] ring-2 ring-orange-200 bg-orange-50/30' 
                                : 'border-slate-200 hover:border-slate-300 bg-white'"
                         class="p-3.5 rounded-xl border transition cursor-pointer flex flex-col justify-between">
                        
                        <div>
                            <div class="flex items-center justify-between text-[11px] mb-1">
                                <span class="font-mono font-bold text-slate-400" x-text="b.campaign_code"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                      :class="{
                                          'bg-amber-100 text-amber-800': !b.is_approved && !b.has_proposal,
                                          'bg-orange-100 text-orange-800': !b.is_approved && b.has_proposal,
                                          'bg-emerald-100 text-emerald-800': b.is_approved
                                      }">
                                    <span x-text="!b.is_approved && !b.has_proposal ? 'In Design' : (!b.is_approved && b.has_proposal ? 'In Review' : 'Approved')"></span>
                                </span>
                            </div>

                            <h3 class="text-xs font-bold text-slate-900 leading-snug" x-text="b.title"></h3>
                            <p class="text-[11px] text-[#F7941D] font-medium mt-0.5" x-text="b.campaign_title"></p>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-1 leading-relaxed" x-text="b.objective"></p>
                        </div>

                        <div class="pt-2.5 mt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span class="truncate max-w-[140px]" x-text="b.assigned_to_name"></span>
                            <span class="text-xs font-bold text-[#F7941D]" x-show="selectedBrief && selectedBrief.id === b.id">Active →</span>
                        </div>
                    </div>
                </template>

                <div x-show="currentFilteredBriefs.length === 0" class="text-center py-10 text-slate-400 text-xs">
                    <p class="font-medium">No briefs in this section</p>
                    <p class="text-[11px] text-slate-400 mt-1">Switch tabs above to browse other briefs.</p>
                </div>
            </div>

        </div>

        <!-- RIGHT PANEL: Master-Detail Target Work Area -->
        <div class="lg:col-span-8 space-y-6">

            <!-- STATE 1: OPEN BRIEF (INTERACTIVE AI COPY STUDIO & PROPOSAL SUBMISSION) -->
            <template x-if="selectedBrief && !selectedBrief.is_approved && !selectedBrief.has_proposal">
                <div class="space-y-6">

                    <!-- AI Copywriting Studio -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-5">
                        
                        <!-- Clean Integrated Header -->
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 font-bold uppercase tracking-wider block" x-text="selectedBrief.campaign_code"></span>
                                <h2 class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedBrief.title"></h2>
                                <p class="text-xs text-[#F7941D] font-medium" x-text="'Campaign: ' + selectedBrief.campaign_title"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                    Open for Copy & Design
                                </span>
                                <div x-show="selectedBrief.sla_remaining_seconds" class="px-2.5 py-1 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-rose-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span x-text="Math.floor(selectedBrief.sla_remaining_seconds / 3600) + 'h ' + Math.floor((selectedBrief.sla_remaining_seconds % 3600) / 60) + 'm SLA'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Objective & Notes if revision requested -->
                        <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-100 text-xs text-slate-700 space-y-1">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Brief Objective</span>
                            <p class="leading-relaxed" x-text="selectedBrief.objective"></p>
                        </div>

                        <div x-show="selectedBrief.feedback_notes" class="p-3.5 rounded-lg bg-amber-50 border border-amber-200 text-xs">
                            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block mb-0.5">IMC Lead Revision Request:</span>
                            <p class="text-amber-900 leading-relaxed italic" x-text="selectedBrief.feedback_notes"></p>
                        </div>
                        
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 text-[#F7941D] flex items-center justify-center font-black text-xs">
                                    AI
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Campaign Copywriting Copilot</h3>
                                    <p class="text-[11px] text-slate-500">Refine retail promotional messaging with compliance checks and Sheng localization</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                Retail Heuristic Engine
                            </span>
                        </div>

                        <!-- Target Audience -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Target Shopper Segment</label>
                            <input type="text" x-model="targetAudience" 
                                   class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]">
                        </div>

                        <!-- Headline -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Headline (POSM / Digital Banner)</label>
                            <input type="text" x-model="headline" @input="draftTitle = headline + ' - Master Artwork'"
                                   class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]">
                        </div>

                        <!-- Body Copy -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-slate-700">Promotional Body Copy & Offer Terms</label>
                                <span class="text-[11px] text-slate-400" x-text="bodyCopy.length + ' chars'"></span>
                            </div>
                            <textarea x-model="bodyCopy" rows="3" 
                                      class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]"></textarea>
                        </div>

                        <!-- Call to Action -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Call to Action (CTA)</label>
                            <input type="text" x-model="cta" 
                                   class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 font-semibold focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]">
                        </div>

                        <!-- AI Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3">
                            <button type="button" @click="runAiQualityCheck()" :disabled="aiChecking"
                                    class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs flex items-center space-x-2 disabled:opacity-50">
                                <svg x-show="!aiChecking" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <svg x-show="aiChecking" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span x-text="aiChecking ? 'Evaluating Copy...' : 'AI Quality Check'"></span>
                            </button>

                            <button type="button" @click="generateSwahili()" :disabled="swahiliGenerating"
                                    class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs flex items-center space-x-2 disabled:opacity-50">
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                                <span x-text="swahiliGenerating ? 'Localizing...' : 'Generate Swahili Variant'"></span>
                            </button>
                        </div>

                        <!-- Results 1: Quality Check Output -->
                        <div x-show="aiResult && !aiChecking" x-cloak class="mt-4 p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Quality Gate Analysis</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold"
                                      :class="aiResult && aiResult.score >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                      x-text="'Score: ' + (aiResult ? aiResult.score : 0) + '/100'"></span>
                            </div>

                            <template x-if="aiResult && aiResult.warnings.length > 0">
                                <div class="space-y-1.5">
                                    <template x-for="w in aiResult.warnings" :key="w">
                                        <div class="text-xs p-2 rounded bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-2">
                                            <span class="font-bold uppercase text-[9px] bg-rose-200 text-rose-800 px-1 rounded">Alert</span>
                                            <span x-text="w"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="aiResult && aiResult.strengths.length > 0">
                                <div class="space-y-1.5">
                                    <template x-for="s in aiResult.strengths" :key="s">
                                        <div class="text-xs p-2 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-2">
                                            <span class="font-bold uppercase text-[9px] bg-emerald-200 text-emerald-800 px-1 rounded">Passed</span>
                                            <span x-text="s"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Results 2: Swahili Localization Output -->
                        <div x-show="swahiliResult && !swahiliGenerating" x-cloak class="mt-4 p-4 rounded-xl border border-orange-200 bg-orange-50/50 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-orange-200/80 pb-2">
                                <span class="font-bold text-orange-900 uppercase text-[11px]">Kenyan Swahili Localization</span>
                                <span class="text-[10px] font-mono text-orange-700">sw-KE</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold text-orange-800 uppercase block">Headline</span>
                                <p class="font-bold text-slate-900 mt-0.5" x-text="swahiliResult ? swahiliResult.swahili_headline : ''"></p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-orange-800 uppercase block">Body Copy</span>
                                <p class="text-slate-700 mt-0.5" x-text="swahiliResult ? swahiliResult.swahili_body : ''"></p>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-orange-800 uppercase block">Call to Action</span>
                                <p class="font-semibold text-slate-900 mt-0.5" x-text="swahiliResult ? swahiliResult.swahili_cta : ''"></p>
                            </div>

                            <button type="button" @click="applySwahili()" 
                                    class="w-full py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs">
                                Apply Swahili Copy to Draft
                            </button>
                        </div>

                    </div>

                    <!-- Integrated Proposal Submission Form -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Submit Design Proposal to IMC Lead</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Attach artwork specifications and forward to Sarah Jenkins for formal review</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                Starts SLA Clock
                            </span>
                        </div>

                        <form action="{{ route('designer.assets.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="campaign_id" :value="selectedBrief.campaign_id">
                            <input type="hidden" name="brief_id" :value="selectedBrief.id">

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Proposal / Draft Title *</label>
                                <input type="text" name="title" x-model="draftTitle" required 
                                       class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 font-medium text-slate-900 focus:outline-none focus:border-[#F7941D]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Type *</label>
                                    <select name="asset_type" x-model="assetType" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 bg-white text-slate-800">
                                        <option value="posm_banner">POSM Banner</option>
                                        <option value="gondola_header">Gondola Header</option>
                                        <option value="digital_graphic">Digital Social Graphic</option>
                                        <option value="shelf_talker">Shelf Talker</option>
                                        <option value="price_dangler">Price Dangler</option>
                                        <option value="video_reel">Video Reel (9:16)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dimensions *</label>
                                    <input type="text" name="dimensions" x-model="dimensions" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Format *</label>
                                    <input type="text" name="format" x-model="format" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mockup Image URL</label>
                                    <input type="text" name="file_url" x-model="fileUrl" placeholder="Leave empty for auto retail preview" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800">
                                </div>
                            </div>

                            <!-- Quick Preset Artwork Mockups -->
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 mb-2">Or select visual mockup preset:</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" @click="fileUrl = 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80'"
                                            class="p-1 rounded-lg border border-slate-200 hover:border-[#F7941D] text-left transition group">
                                        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=300&q=80" alt="Fresh Produce" class="w-full h-12 object-cover rounded">
                                        <span class="block text-[10px] font-medium text-slate-600 mt-1 truncate">Fresh Produce</span>
                                    </button>
                                    <button type="button" @click="fileUrl = 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80'"
                                            class="p-1 rounded-lg border border-slate-200 hover:border-[#F7941D] text-left transition group">
                                        <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=300&q=80" alt="Supermarket Aisle" class="w-full h-12 object-cover rounded">
                                        <span class="block text-[10px] font-medium text-slate-600 mt-1 truncate">Store Aisle</span>
                                    </button>
                                    <button type="button" @click="fileUrl = 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1000&q=80'"
                                            class="p-1 rounded-lg border border-slate-200 hover:border-[#F7941D] text-left transition group">
                                        <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=300&q=80" alt="Groceries & Essentials" class="w-full h-12 object-cover rounded">
                                        <span class="block text-[10px] font-medium text-slate-600 mt-1 truncate">Essentials</span>
                                    </button>
                                    <button type="button" @click="fileUrl = 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1000&q=80'"
                                            class="p-1 rounded-lg border border-slate-200 hover:border-[#F7941D] text-left transition group">
                                        <img src="https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=300&q=80" alt="Social Promo" class="w-full h-12 object-cover rounded">
                                        <span class="block text-[10px] font-medium text-slate-600 mt-1 truncate">Digital Promo</span>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2 px-3 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs flex items-center justify-center space-x-1.5 cursor-pointer">
                                <span>Submit Proposal to IMC Lead</span>
                                <span>→</span>
                            </button>
                        </form>
                    </div>

                </div>
            </template>


            <!-- STATE 2: PENDING APPROVAL OR APPROVED (PROPOSAL SUBMITTED) -->
            <template x-if="selectedBrief && (selectedBrief.has_proposal || selectedBrief.is_approved)">
                <div class="space-y-6">

                    <!-- Brief Specification Card (Read Only) -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-5">
                        
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 font-bold uppercase tracking-wider block" x-text="selectedBrief.campaign_code"></span>
                                <h3 class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedBrief.title"></h3>
                                <p class="text-xs text-[#F7941D] font-medium" x-text="'Campaign: ' + selectedBrief.campaign_title"></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-1 rounded text-xs font-semibold"
                                      :class="selectedBrief.is_approved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-orange-50 text-orange-700 border border-orange-200'"
                                      x-text="selectedBrief.is_approved ? 'Approved by IMC Lead' : 'Proposal in Review'"></span>
                                <span class="px-2.5 py-1 rounded text-xs font-medium bg-slate-100 text-slate-700 capitalize" x-text="selectedBrief.channel.replace(/_/g, ' ')"></span>
                                <button type="button" x-show="!selectedBrief.is_approved" @click="selectedBrief.has_proposal = false"
                                        class="px-3 py-1 rounded-lg border border-[#F7941D] text-[#F7941D] hover:bg-orange-50 text-xs font-bold transition flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Revise Proposal</span>
                                </button>
                            </div>
                        </div>

                        <!-- Objective & Audience -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Brief Objective</span>
                                <p class="text-slate-800 leading-relaxed" x-text="selectedBrief.objective"></p>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Target Shopper Segment</span>
                                <p class="text-slate-800 leading-relaxed" x-text="selectedBrief.target_segment || 'General Naivas Shoppers'"></p>
                            </div>
                        </div>

                        <!-- Key Messaging & Copy (Read Only) -->
                        <div class="space-y-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Approved / Submitted Copy</span>
                            
                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs space-y-2">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Key Headline & Messaging</span>
                                    <p class="font-bold text-slate-900 mt-0.5" x-text="selectedBrief.key_messaging || selectedBrief.title"></p>
                                </div>
                                <div x-show="selectedBrief.body_copy">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Body Copy</span>
                                    <p class="text-slate-700 mt-0.5" x-text="selectedBrief.body_copy"></p>
                                </div>
                            </div>

                            <div x-show="selectedBrief.swahili_copy" class="p-4 rounded-lg bg-orange-50/60 border border-orange-200 text-xs">
                                <span class="text-[10px] font-bold text-orange-800 uppercase block mb-1">Swahili Variant (sw-KE)</span>
                                <p class="text-slate-800 leading-relaxed font-medium" x-text="selectedBrief.swahili_copy"></p>
                            </div>
                        </div>

                        <!-- Review Feedback Notes -->
                        <div x-show="selectedBrief.feedback_notes" class="p-3.5 rounded-lg bg-slate-100 border border-slate-200 text-xs">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">IMC Lead Notes & Feedback</span>
                            <p class="text-slate-700 italic" x-text="selectedBrief.feedback_notes"></p>
                        </div>

                    </div>

                    <!-- Submitted Artwork / Design Proposal Preview (Read Only) -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Submitted Artwork & Design Proposal</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Asset files currently attached to this brief</p>
                            </div>
                            <span class="text-xs text-slate-400" x-text="selectedBrief.assets.length + ' asset(s) linked'"></span>
                        </div>

                        <!-- Asset Cards Loop -->
                        <template x-for="asset in selectedBrief.assets" :key="asset.id">
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-4 items-start">
                                
                                <div class="w-full sm:w-48 h-32 rounded-lg bg-slate-200 overflow-hidden shrink-0">
                                    <img :src="asset.file_url || 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80'" 
                                         :alt="asset.title" 
                                         class="w-full h-full object-cover">
                                </div>

                                <div class="flex-1 space-y-2 text-xs">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-bold text-slate-900 text-sm" x-text="asset.title"></h4>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                              :class="asset.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800'"
                                              x-text="asset.status === 'approved' ? 'Approved' : 'Pending Review'"></span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2 text-slate-600 text-[11px]">
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Type</span>
                                            <span class="font-medium" x-text="asset.asset_type"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Dimensions</span>
                                            <span class="font-medium" x-text="asset.dimensions"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Format</span>
                                            <span class="font-medium" x-text="asset.format"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px]">Version</span>
                                            <span class="font-medium" x-text="'v' + asset.version"></span>
                                        </div>
                                    </div>

                                    <div x-show="asset.approval_notes" class="pt-2 border-t border-slate-200/80 text-[11px] text-slate-500 italic" x-text="asset.approval_notes"></div>
                                </div>

                            </div>
                        </template>

                        <div x-show="selectedBrief.assets.length === 0" class="text-center py-6 text-slate-400 text-xs">
                            No artwork assets attached yet.
                        </div>

                    </div>

                </div>
            </template>

            <!-- Fallback if no brief is selected -->
            <div x-show="!selectedBrief" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 text-xs">
                Select a brief from the left panel to begin.
            </div>

        </div>

    </div>

</div>
@endsection
