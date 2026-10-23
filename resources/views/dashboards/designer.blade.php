@extends('layouts.app', ['title' => 'Creative Designer & AI Studio'])

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'ai_studio',
    headline: 'Weekend Farm Fresh Harvest',
    bodyCopy: 'Enjoy 30% off crisp farm greens and Rift Valley avocados directly from our local farmers. Okoa Zaidi!',
    cta: 'Shop Fresh at Naivas Supermarket Today!',
    targetAudience: 'Household shoppers, families, fresh produce buyers',
    
    // AI Quality Check state
    aiChecking: false,
    aiResult: null,
    
    // Swahili localization state
    swahiliGenerating: false,
    swahiliResult: null,

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
        }
    }
}">

    <!-- Top Breadcrumb & Persona Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>Integrated Marketing Communications</span>
                <span>•</span>
                <span class="text-[#F7941D] font-semibold">Persona 5: Creative Designer</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center space-x-2">
                <span>Wanjiru Karanja</span>
                <span class="text-sm font-normal text-slate-500">• Creative Design & Assistive AI Studio</span>
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Draft POSM creative copy, run assistive AI compliance checks, localize copy into Kenyan Swahili, and upload print-ready assets for IMC sign-off.
            </p>
        </div>

        <!-- Quick Switch Tabs -->
        <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs">
            <button type="button" @click="activeTab = 'ai_studio'" 
                    :class="activeTab === 'ai_studio' ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-3.5 py-1.5 rounded-lg transition flex items-center space-x-1.5">
                <span>AI Copy Studio</span>
            </button>
            <button type="button" @click="activeTab = 'briefs'" 
                    :class="activeTab === 'briefs' ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-3.5 py-1.5 rounded-lg transition">
                <span>Briefs Queue ({{ $briefs->count() }})</span>
            </button>
            <button type="button" @click="activeTab = 'upload'" 
                    :class="activeTab === 'upload' ? 'bg-white font-bold text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="px-3.5 py-1.5 rounded-lg transition">
                <span>+ Upload Draft</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: AI COPY & CREATIVE STUDIO -->
    <div x-show="activeTab === 'ai_studio'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Copy Input Studio -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-orange-100 text-[#F7941D] flex items-center justify-center font-black text-xs">
                        AI
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Campaign Copywriting Copilot</h2>
                        <p class="text-[11px] text-slate-500">Draft promotional text with real-time heuristic & retail rule checking</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Gemini Heuristic Engine
                </span>
            </div>

            <!-- Target Audience -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Audience & Shopper Segment</label>
                <input type="text" x-model="targetAudience" 
                       class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]"
                       placeholder="e.g. Budget-conscious mothers, loyalty card shoppers...">
            </div>

            <!-- Headline -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Campaign Headline (POSM / Digital Header)</label>
                <input type="text" x-model="headline" 
                       class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 font-bold text-slate-900 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]"
                       placeholder="e.g. Weekend Fresh Farm Harvest">
            </div>

            <!-- Body Copy -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700">Promotional Body Copy & Offer Terms</label>
                    <span class="text-[11px] text-slate-400" x-text="bodyCopy.length + ' chars'"></span>
                </div>
                <textarea x-model="bodyCopy" rows="3" 
                          class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]"
                          placeholder="Describe the offer, discounts, price point, and Naivas store advantage..."></textarea>
            </div>

            <!-- Call to Action -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Call to Action (CTA)</label>
                <input type="text" x-model="cta" 
                       class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800 font-semibold focus:outline-none focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D]"
                       placeholder="e.g. Shop Fresh at Naivas Supermarket Today!">
            </div>

            <!-- Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-3">
                
                <!-- AI Quality Check Button -->
                <button type="button" @click="runAiQualityCheck()" :disabled="aiChecking"
                        class="px-4 py-2.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs flex items-center space-x-2 disabled:opacity-50">
                    <svg x-show="!aiChecking" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg x-show="aiChecking" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span x-text="aiChecking ? 'Analyzing Copy...' : 'AI Quality Check'"></span>
                </button>

                <!-- Generate Swahili Variant Button -->
                <button type="button" @click="generateSwahili()" :disabled="swahiliGenerating"
                        class="px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs flex items-center space-x-2 disabled:opacity-50">
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span x-text="swahiliGenerating ? 'Localizing...' : 'Generate Swahili Variant'"></span>
                </button>
            </div>
        </div>

        <!-- Right Column: AI Analysis & Localization Results -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- AI Quality Analysis Box -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">AI Quality Check Results</h3>
                    <template x-if="aiResult">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                              :class="aiResult.score >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                              x-text="'Score: ' + aiResult.score + '/100'"></span>
                    </template>
                </div>

                <!-- Empty State before running -->
                <div x-show="!aiResult && !aiChecking" class="text-center py-8">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-2 text-xs font-bold">
                        AI
                    </div>
                    <p class="text-xs font-semibold text-slate-700">No Quality Check Executed Yet</p>
                    <p class="text-[11px] text-slate-500 mt-1 max-w-xs mx-auto">
                        Click "AI Quality Check" to test this draft against Naivas retail marketing guidelines, target audience clarity, and CTA strength.
                    </p>
                </div>

                <!-- Loading Spinner -->
                <div x-show="aiChecking" class="text-center py-10 space-y-2">
                    <div class="w-8 h-8 border-2 border-[#F7941D] border-t-transparent rounded-full animate-spin mx-auto"></div>
                    <p class="text-xs font-medium text-slate-600">Simulating retail rule check & NLP analysis...</p>
                </div>

                <!-- Results Output -->
                <div x-show="aiResult && !aiChecking" x-cloak class="space-y-4">
                    
                    <!-- Status Banner -->
                    <div class="p-3 rounded-lg flex items-center justify-between"
                         :class="aiResult && aiResult.score >= 80 ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : 'bg-amber-50 border border-amber-200 text-amber-900'">
                        <div class="flex items-center space-x-2 text-xs font-bold">
                            <span class="w-2 h-2 rounded-full" :class="aiResult && aiResult.score >= 80 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                            <span x-text="aiResult ? aiResult.status : ''"></span>
                        </div>
                        <span class="text-[10px] text-slate-500" x-text="aiResult ? aiResult.timestamp : ''"></span>
                    </div>

                    <!-- Warning Badges -->
                    <template x-if="aiResult && aiResult.warnings.length > 0">
                        <div class="space-y-2">
                            <p class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Identified Warnings</p>
                            <template x-for="warn in aiResult.warnings" :key="warn">
                                <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start space-x-2">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-rose-200 text-rose-800">Alert</span>
                                    <span x-text="warn"></span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Strengths -->
                    <template x-if="aiResult && aiResult.strengths.length > 0">
                        <div class="space-y-2">
                            <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Compliance Highlights</p>
                            <template x-for="str in aiResult.strengths" :key="str">
                                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-xs flex items-center space-x-2">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800">Passed</span>
                                    <span x-text="str"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Swahili Variant Box -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Kenyan Swahili Variant</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">sw-KE</span>
                </div>

                <div x-show="!swahiliResult && !swahiliGenerating" class="text-center py-6 text-slate-400 text-xs">
                    Click "Generate Swahili Variant" to create localized retail Sheng/Swahili marketing copy.
                </div>

                <div x-show="swahiliGenerating" class="text-center py-6">
                    <div class="w-6 h-6 border-2 border-slate-900 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    <p class="text-xs text-slate-600">Localizing copy to Kenyan supermarket phrasing...</p>
                </div>

                <div x-show="swahiliResult && !swahiliGenerating" x-cloak class="space-y-3">
                    <div class="p-3 rounded-lg bg-orange-50/60 border border-orange-200 space-y-2 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-orange-700 uppercase block">Headline (Swahili)</span>
                            <p class="font-bold text-slate-900 mt-0.5" x-text="swahiliResult ? swahiliResult.swahili_headline : ''"></p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-orange-700 uppercase block">Body Copy (Swahili)</span>
                            <p class="text-slate-800 mt-0.5 leading-relaxed" x-text="swahiliResult ? swahiliResult.swahili_body : ''"></p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-orange-700 uppercase block">CTA (Swahili)</span>
                            <p class="font-semibold text-slate-900 mt-0.5" x-text="swahiliResult ? swahiliResult.swahili_cta : ''"></p>
                        </div>
                    </div>

                    <button type="button" @click="applySwahili()" 
                            class="w-full py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs">
                        Apply Swahili Variant to Draft
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- TAB 2: ACTIVE BRIEFS QUEUE -->
    <div x-show="activeTab === 'briefs'" x-cloak class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-1">Incoming Creative Briefs Assigned to Wanjiru</h3>
            <p class="text-xs text-slate-500 mb-6">Briefs requiring POSM concepts, artwork creation, and draft submission.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($briefs as $brief)
                <div class="p-5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white shadow-xs flex flex-col justify-between transition">
                    <div>
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-slate-400">{{ $brief->campaign->code }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                @if($brief->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($brief->status === 'pending_review') bg-amber-50 text-amber-700 border border-amber-200
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ str_replace('_', ' ', $brief->status) }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $brief->title }}</h4>
                        <p class="text-xs text-[#F7941D] font-medium mt-0.5 mb-2">{{ $brief->campaign->title }}</p>
                        <p class="text-xs text-slate-600 line-clamp-3 mb-4 leading-relaxed">{{ $brief->objective }}</p>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-400 text-[11px]">{{ $brief->created_at->diffForHumans() }}</span>
                        <button type="button" @click="
                            headline = '{{ addslashes($brief->title) }}';
                            bodyCopy = '{{ addslashes($brief->key_messaging) }}';
                            targetAudience = '{{ addslashes($brief->target_segment) }}';
                            activeTab = 'ai_studio';
                        " class="text-xs font-bold text-[#F7941D] hover:underline">
                            Open in Studio →
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-8 text-center text-slate-400 text-xs">No active briefs assigned.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 3: UPLOAD ASSET DRAFT -->
    <div x-show="activeTab === 'upload'" x-cloak class="max-w-2xl bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 mb-1">Submit Finished Creative Draft to IMC Lead</h3>
        <p class="text-xs text-slate-500 mb-5">Submitting initiates the SLA countdown clock for IMC Lead Amina Mwangi to review and approve.</p>

        <form action="{{ route('designer.assets.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Campaign</label>
                <select name="campaign_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2 text-slate-800">
                    @foreach($campaigns as $camp)
                    <option value="{{ $camp->id }}">{{ $camp->code }} - {{ $camp->title }} ({{ ucfirst($camp->status) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Title</label>
                <input type="text" name="title" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2" placeholder="e.g. Produce Aisle Gondola Banner 1200x600">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Type</label>
                    <select name="asset_type" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                        <option value="gondola_header">Gondola Header</option>
                        <option value="posm_banner">POSM Banner</option>
                        <option value="digital_graphic">Digital Social Graphic</option>
                        <option value="price_dangler">Price Dangler</option>
                        <option value="shelf_talker">Shelf Talker</option>
                        <option value="video_reel">Video Reel (9:16)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="1200x600mm" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Format</label>
                    <input type="text" name="format" value="High-Res PDF Print" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mockup Image URL (Optional)</label>
                    <input type="text" name="file_url" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2" placeholder="Leave blank for auto retail mockup">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs">
                Upload & Send to IMC Lead (Start SLA Clock)
            </button>
        </form>
    </div>

</div>
@endsection
