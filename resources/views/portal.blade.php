<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judge Portal | Naivas Marketing Command Center</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @php
        $manifestPath = public_path('build/manifest.json');
        $cssAsset = 'assets/app-B8V3oR19.css';
        $jsAsset = 'assets/app-oLHBGesV.js';
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $cssAsset = $manifest['resources/css/app.css']['file'] ?? $cssAsset;
            $jsAsset = $manifest['resources/js/app.js']['file'] ?? $jsAsset;
        }
    @endphp
    <link rel="stylesheet" href="/build/{{ $cssAsset }}">
    <script type="module" src="/build/{{ $jsAsset }}"></script>
</head>
<body class="min-h-full flex flex-col justify-between bg-white text-slate-900 antialiased selection:bg-[#F7941D] selection:text-white">

    <!-- Top Minimalist Brand Header -->
    <header class="border-b border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-[#F7941D] flex items-center justify-center text-white font-black text-sm tracking-tighter">
                    N
                </div>
                <div>
                    <span class="text-base font-bold tracking-tight text-slate-900">NAIVAS</span>
                    <span class="text-slate-400 text-xs ml-2">Marketing Command Center</span>
                </div>
            </div>
            <div class="text-xs text-slate-500 font-medium">
                Live Prototype Evaluation
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col justify-center">
        
        <!-- Header Text -->
        <div class="max-w-2xl mb-12">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-[#F7941D] mb-2">
                Judge Review Portal
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Select a department persona to evaluate the workflow.
            </h1>
            <p class="text-sm text-slate-600 mt-3 leading-relaxed">
                Click any role below for instant, frictionless login without email or password. 
                Test the connected sequential loop from campaign planning and creative asset approvals to media spend deployment and in-store proof verification.
            </p>
        </div>

        <!-- 4 Clean Persona Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Persona 1: IMC Lead -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs flex flex-col justify-between transition">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Module 1</span>
                    <h3 class="text-base font-bold text-slate-900">Amina Mwangi</h3>
                    <p class="text-xs font-medium text-[#F7941D] mb-3">IMC Lead</p>
                    <p class="text-xs text-slate-600 leading-relaxed mb-6">
                        Reviews briefs and approves POSM banners. Approving an asset automatically marks campaigns as "Ready for Launch".
                    </p>
                </div>

                <a href="{{ route('login.as', 'imc_lead') }}" 
                   class="w-full py-2.5 px-4 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white font-semibold text-xs text-center transition shadow-xs">
                    Login as IMC Lead
                </a>
            </div>

            <!-- Persona 2: DPM Media Buyer -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs flex flex-col justify-between transition">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Module 2</span>
                    <h3 class="text-base font-bold text-slate-900">Kevin Omondi</h3>
                    <p class="text-xs font-medium text-[#F7941D] mb-3">DPM Media Buyer</p>
                    <p class="text-xs text-slate-600 leading-relaxed mb-6">
                        Manages media spend and pacing. Ad deployment is strictly gated until IMC signs off. Reallocate budgets between channels.
                    </p>
                </div>

                <a href="{{ route('login.as', 'dpm_buyer') }}" 
                   class="w-full py-2.5 px-4 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white font-semibold text-xs text-center transition shadow-xs">
                    Login as DPM Media Buyer
                </a>
            </div>

            <!-- Persona 3: OME Store Manager -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs flex flex-col justify-between transition">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Module 3</span>
                    <h3 class="text-base font-bold text-slate-900">Faith Wanjiku</h3>
                    <p class="text-xs font-medium text-[#F7941D] mb-3">Store Manager</p>
                    <p class="text-xs text-slate-600 leading-relaxed mb-6">
                        Mobile checklist for in-store physical rollouts at Naivas Mega City. Upload proof photos to notify the executive dashboard.
                    </p>
                </div>

                <a href="{{ route('login.as', 'ome_manager') }}" 
                   class="w-full py-2.5 px-4 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white font-semibold text-xs text-center transition shadow-xs">
                    Login as OME Store Manager
                </a>
            </div>

            <!-- Persona 4: Marketing Executive -->
            <div class="bg-white rounded-xl p-6 border border-slate-200 hover:border-slate-300 shadow-xs flex flex-col justify-between transition">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Centralized Hub</span>
                    <h3 class="text-base font-bold text-slate-900">Peter Mumo</h3>
                    <p class="text-xs font-medium text-[#F7941D] mb-3">Chief Marketing Officer</p>
                    <p class="text-xs text-slate-600 leading-relaxed mb-6">
                        Central view of campaign health, budget burn rates, rollout rates, and a live activity feed of actions across all departments.
                    </p>
                </div>

                <a href="{{ route('login.as', 'marketing_exec') }}" 
                   class="w-full py-2.5 px-4 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs text-center transition shadow-xs">
                    Login as Marketing Executive
                </a>
            </div>

        </div>

        <!-- Workflow Summary Bar -->
        <div class="mt-12 p-5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-slate-600">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-900">Sequential Interrelation:</span>
                <span>Campaign Planning</span>
                <span class="text-slate-400">→</span>
                <span>IMC Creative Approval</span>
                <span class="text-slate-400">→</span>
                <span>DPM Media Allocation</span>
                <span class="text-slate-400">→</span>
                <span>OME Store Rollout</span>
            </div>
            <a href="{{ route('demo.reset') }}" class="text-[#F7941D] hover:underline font-semibold whitespace-nowrap">
                Reset Demo Baseline
            </a>
        </div>

    </main>

    <!-- Clean Footer -->
    <footer class="border-t border-slate-200 py-4 text-center text-xs text-slate-400 bg-white">
        Naivas Supermarket Kenya • Marketing Command Center Prototype
    </footer>

</body>
</html>
