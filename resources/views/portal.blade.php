<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal | Naivas Marketing Command Center</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between text-slate-100 bg-gradient-to-br from-slate-950 via-[#002815] to-slate-950 antialiased selection:bg-[#F7941D] selection:text-white">

    <!-- Top Glow Ribbon -->
    <div class="h-2 bg-gradient-to-r from-[#006837] via-[#F7941D] to-[#006837] w-full"></div>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16 flex flex-col justify-center">
        
        <!-- Header & Brand Presentation -->
        <div class="text-center max-w-3xl mx-auto mb-10">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-semibold text-emerald-300 backdrop-blur-xs mb-4">
                <span class="w-2 h-2 rounded-full bg-[#F7941D] animate-ping"></span>
                <span>Judging Panel Live Evaluation Portal</span>
                <span class="text-white/40">•</span>
                <span>Frictionless 1-Click Access</span>
            </div>

            <!-- Logo Mark -->
            <div class="flex items-center justify-center space-x-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-lg shadow-emerald-950/50">
                    <svg class="w-7 h-7 text-[#006837]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3h-6c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z"/>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white uppercase">
                    NAIVAS <span class="text-[#F7941D]">Command Center</span>
                </h1>
            </div>

            <p class="text-base sm:text-lg text-emerald-100/90 font-medium">
                Retail Marketing Orchestration Prototype for Kenya's Leading Supermarket Chain
            </p>

            <div class="mt-4 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-xs max-w-2xl mx-auto text-xs sm:text-sm text-slate-300 leading-relaxed">
                <span class="font-bold text-white">Core Sequential Workflow:</span>
                <span class="text-emerald-300 font-semibold">Campaign Planning</span> $\rightarrow$ 
                <span class="text-purple-300 font-semibold">IMC Creative Approval</span> $\rightarrow$ 
                <span class="text-blue-300 font-semibold">DPM Media Allocation</span> $\rightarrow$ 
                <span class="text-amber-300 font-semibold">OME In-Store Proof</span>
            </div>
        </div>

        <!-- 4 Distinct Persona Login Cards for Judges -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto w-full">
            
            <!-- Persona 1: IMC Lead -->
            <div class="relative group bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-6 border border-white/15 hover:border-purple-400/60 shadow-xl transition-all duration-200 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-purple-500/20 text-purple-300 border border-purple-400/30">
                            Module 1: Creative
                        </span>
                        <span class="text-2xl">🎨</span>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-purple-600/30 border border-purple-400/40 flex items-center justify-center font-black text-lg text-purple-200 mb-3">
                        IMC
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-purple-200 transition">Amina Mwangi</h3>
                    <p class="text-xs text-purple-300 font-medium mb-3">Integrated Marketing Communications Lead</p>

                    <p class="text-xs text-slate-300 leading-relaxed mb-6">
                        Manages the briefing pipeline & creative assets Kanban board. Approving an asset automatically transitions campaigns to <strong class="text-white">"Ready for Launch"</strong>.
                    </p>
                </div>

                <a href="{{ route('login.as', 'imc_lead') }}" 
                   class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs uppercase tracking-wider text-center shadow-lg shadow-purple-900/50 transition-all flex items-center justify-center space-x-2 group-hover:shadow-purple-500/30">
                    <span>Login as IMC Lead</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Persona 2: DPM Media Buyer -->
            <div class="relative group bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-6 border border-white/15 hover:border-blue-400/60 shadow-xl transition-all duration-200 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-blue-500/20 text-blue-300 border border-blue-400/30">
                            Module 2: Media Spend
                        </span>
                        <span class="text-2xl">📈</span>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-blue-600/30 border border-blue-400/40 flex items-center justify-center font-black text-lg text-blue-200 mb-3">
                        DPM
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-blue-200 transition">Kevin Omondi</h3>
                    <p class="text-xs text-blue-300 font-medium mb-3">Digital & Performance Marketing Lead</p>

                    <p class="text-xs text-slate-300 leading-relaxed mb-6">
                        Monitors digital media pacing, impressions, and CTR. Gated ad deployment (<strong class="text-white">locked until IMC approves</strong>) and dynamic budget reallocation.
                    </p>
                </div>

                <a href="{{ route('login.as', 'dpm_buyer') }}" 
                   class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs uppercase tracking-wider text-center shadow-lg shadow-blue-900/50 transition-all flex items-center justify-center space-x-2 group-hover:shadow-blue-500/30">
                    <span>Login as DPM Media Buyer</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Persona 3: OME Store Manager -->
            <div class="relative group bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-6 border border-white/15 hover:border-emerald-400/60 shadow-xl transition-all duration-200 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Module 3: Store Ops
                        </span>
                        <span class="text-2xl">📱</span>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-[#006837]/60 border border-emerald-400/40 flex items-center justify-center font-black text-lg text-emerald-200 mb-3">
                        OME
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-200 transition">Faith Wanjiku</h3>
                    <p class="text-xs text-emerald-300 font-medium mb-3">Store Operations Manager (Naivas Mega City)</p>

                    <p class="text-xs text-slate-300 leading-relaxed mb-6">
                        Mobile-optimized store floor checklist. Uploads photographic proof of execution (gondola ends, archways) syncing directly to Executive oversight.
                    </p>
                </div>

                <a href="{{ route('login.as', 'ome_manager') }}" 
                   class="w-full py-3 px-4 rounded-xl bg-[#006837] hover:bg-[#005a30] text-white font-bold text-xs uppercase tracking-wider text-center shadow-lg shadow-emerald-950/60 transition-all flex items-center justify-center space-x-2 group-hover:shadow-emerald-500/30 border border-emerald-500/50">
                    <span>Login as OME Store Manager</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Persona 4: Marketing Executive -->
            <div class="relative group bg-white/10 hover:bg-white/15 backdrop-blur-md rounded-2xl p-6 border border-white/15 hover:border-amber-400/60 shadow-xl transition-all duration-200 flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wider uppercase bg-amber-500/20 text-amber-300 border border-amber-400/30">
                            Global Executive Hub
                        </span>
                        <span class="text-2xl">👑</span>
                    </div>

                    <div class="w-12 h-12 rounded-xl bg-[#F7941D]/40 border border-amber-400/40 flex items-center justify-center font-black text-lg text-amber-200 mb-3">
                        CMO
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-amber-200 transition">Peter Mumo</h3>
                    <p class="text-xs text-amber-300 font-medium mb-3">Chief Marketing Officer (CMO)</p>

                    <p class="text-xs text-slate-300 leading-relaxed mb-6">
                        High-level dashboard with campaign status health, budget burn rates charts, in-store rollout KPIs, and real-time cross-department activity feed.
                    </p>
                </div>

                <a href="{{ route('login.as', 'marketing_exec') }}" 
                   class="w-full py-3 px-4 rounded-xl bg-[#F7941D] hover:bg-[#dc760f] text-white font-bold text-xs uppercase tracking-wider text-center shadow-lg shadow-amber-950/60 transition-all flex items-center justify-center space-x-2 group-hover:shadow-amber-500/30">
                    <span>Login as Marketing Executive</span>
                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>

        <!-- Demonstration Guide for Judges -->
        <div class="mt-12 max-w-4xl mx-auto w-full p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
            <h4 class="text-xs font-bold text-[#F7941D] uppercase tracking-wider mb-2 flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Suggested Live Review Script for Judges:
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs text-slate-300 mt-3">
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="font-bold text-purple-300 block mb-1">1. Test IMC Sign-Off</span>
                    Log in as <strong class="text-white">IMC Lead</strong>. View "Fresh Harvest Week". Click <em>"Approve Asset"</em>. Watch the campaign status auto-advance to "Ready for Launch".
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="font-bold text-blue-300 block mb-1">2. Test DPM Gating</span>
                    Switch to <strong class="text-white">DPM Media Buyer</strong>. Notice ads for "Fresh Harvest Week" are now unlocked! Click <em>"Deploy Ads"</em>, then test <em>"Reallocate Budget"</em>.
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="font-bold text-emerald-300 block mb-1">3. Test Mobile Proof</span>
                    Switch to <strong class="text-white">OME Store Manager</strong>. Click <em>"Upload Proof"</em> on a task. Select a simulated supermarket photo and submit.
                </div>
                <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                    <span class="font-bold text-amber-300 block mb-1">4. Verify Executive Hub</span>
                    Switch to <strong class="text-white">Marketing Executive</strong>. Observe the live activity feed, budget charts, and store rollout rate reflecting all actions!
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                <span>Pre-seeded with 5 realistic Naivas campaigns and KES 22.3M in mock marketing budgets.</span>
                <a href="{{ route('demo.reset') }}" class="text-[#F7941D] hover:underline font-semibold flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Reset mock data to initial state
                </a>
            </div>
        </div>

    </main>

    <!-- Simple Footer -->
    <footer class="py-4 text-center text-xs text-slate-500 border-t border-white/5">
        Naivas Supermarket Marketing Command Center Prototype • Production Ready for Render
    </footer>

</body>
</html>
