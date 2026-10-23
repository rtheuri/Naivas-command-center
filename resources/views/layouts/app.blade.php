<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Marketing Command Center' }} | Naivas</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        $manifestPath = public_path('build/manifest.json');
        $cssAsset = 'assets/app-zxulFuEb.css';
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
<body class="h-full flex flex-col bg-[#f8fafc] text-slate-800 antialiased selection:bg-[#F7941D] selection:text-white" x-data="{ roleSwitcherOpen: false, mobileNavOpen: false, resetConfirmModal: false }">

    <!-- Clean White Navigation Header with Orange Accent -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand Title -->
                <div class="flex items-center space-x-6">
                    <a href="{{ route('portal') }}" class="flex items-center space-x-3 group">
                        <div class="w-8 h-8 rounded-lg bg-[#F7941D] flex items-center justify-center text-white font-black text-sm tracking-tighter">
                            N
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-base font-bold tracking-tight text-slate-900">NAIVAS</span>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-orange-50 text-[#F7941D] border border-orange-200/60">
                                    Command Center
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- Main Navigation Tabs -->
                    <nav class="hidden lg:flex items-center space-x-1 pl-4 border-l border-slate-200">
                        <a href="{{ route('executive.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('executive.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Executive Hub
                        </a>

                        <a href="{{ route('imc.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('imc.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            IMC Lead
                        </a>

                        <a href="{{ route('designer.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('designer.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} flex items-center space-x-1">
                            <span>AI Studio</span>
                            <span class="text-[10px] text-orange-500">✨</span>
                        </a>

                        <a href="{{ route('agency.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('agency.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Agency Partner
                        </a>

                        <a href="{{ route('dpm.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('dpm.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            DPM Media
                        </a>

                        <a href="{{ route('ome.dashboard') }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ request()->routeIs('ome.*') ? 'bg-orange-50 text-[#F7941D]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Store Mobile
                        </a>
                    </nav>
                </div>

                <!-- Right Side Controls -->
                <div class="flex items-center space-x-3">
                    
                    <!-- Reset Demo Button -->
                    <button type="button" @click="resetConfirmModal = true" 
                            class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 transition">
                        Reset Data
                    </button>

                    <!-- Persona Switcher -->
                    @auth
                    <div class="relative" @click.away="roleSwitcherOpen = false">
                        <button type="button" 
                                @click="roleSwitcherOpen = !roleSwitcherOpen"
                                class="flex items-center space-x-2.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-slate-300 bg-white text-slate-800 transition">
                            <span class="w-6 h-6 rounded-md bg-orange-100 text-[#F7941D] flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="text-left hidden sm:block">
                                <span class="text-xs font-semibold text-slate-900 block leading-none">{{ Auth::user()->name }}</span>
                                <span class="text-[10px] text-slate-500 font-medium block leading-none mt-0.5">{{ Auth::user()->getRoleDisplayName() }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Clean Persona Switcher Menu -->
                        <div x-show="roleSwitcherOpen" 
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-72 rounded-xl bg-white shadow-lg border border-slate-200 divide-y divide-slate-100 z-50 overflow-hidden text-slate-800">
                            
                            <div class="px-4 py-3 bg-slate-50">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Judge Persona Switcher</p>
                                <p class="text-xs text-slate-700 mt-0.5">Select a role to review workflows:</p>
                            </div>

                            <div class="p-1.5 space-y-0.5">
                                <a href="{{ route('switch.role', 'imc_lead') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'imc_lead' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Amina Mwangi</span>
                                            @if(Auth::user()->role === 'imc_lead')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">IMC Lead (Creative Approvals & SLA)</div>
                                    </div>
                                </a>

                                <a href="{{ route('switch.role', 'creative_designer') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'creative_designer' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Wanjiru Karanja ✨</span>
                                            @if(Auth::user()->role === 'creative_designer')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">Creative Designer (AI Copilot & Copy)</div>
                                    </div>
                                </a>

                                <a href="{{ route('switch.role', 'agency_partner') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'agency_partner' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Apex Brand Agency</span>
                                            @if(Auth::user()->role === 'agency_partner')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">External Agency (Scoped Workspace)</div>
                                    </div>
                                </a>

                                <a href="{{ route('switch.role', 'dpm_buyer') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'dpm_buyer' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Kevin Omondi</span>
                                            @if(Auth::user()->role === 'dpm_buyer')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">DPM Media Buyer (Gated Ad Deploy)</div>
                                    </div>
                                </a>

                                <a href="{{ route('switch.role', 'ome_manager') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'ome_manager' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Faith Wanjiku</span>
                                            @if(Auth::user()->role === 'ome_manager')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">OME Store Manager (Mobile & Offline)</div>
                                    </div>
                                </a>

                                <a href="{{ route('switch.role', 'marketing_exec') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-slate-50 transition {{ Auth::user()->role === 'marketing_exec' ? 'bg-orange-50/70 font-semibold text-[#F7941D]' : 'text-slate-700' }}">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span>Peter Mumo</span>
                                            @if(Auth::user()->role === 'marketing_exec')<span class="text-[10px] text-[#F7941D] font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">Chief Marketing Officer (Power BI)</div>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2 bg-slate-50 flex items-center justify-between">
                                <a href="{{ route('portal') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">Portal Landing</a>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-slate-500 hover:text-rose-600 font-medium">Log out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('portal') }}" class="px-3.5 py-1.5 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white font-semibold text-xs transition">
                        Select Persona
                    </a>
                    @endauth

                    <!-- Mobile Navigation Toggle -->
                    <button type="button" @click="mobileNavOpen = !mobileNavOpen" class="md:hidden p-2 rounded-lg border border-slate-200 text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileNavOpen" x-cloak class="md:hidden bg-white px-4 pt-2 pb-4 space-y-1 border-t border-slate-200">
            <a href="{{ route('executive.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Executive Hub</a>
            <a href="{{ route('imc.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">1. IMC Creative</a>
            <a href="{{ route('dpm.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">2. DPM Media</a>
            <a href="{{ route('ome.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">3. OME Store Ops</a>
            <a href="{{ route('demo.reset') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-[#F7941D] hover:bg-orange-50">Reset Demo Data</a>
        </div>
    </header>

    <!-- Clean Workflow Sub-bar -->
    <div class="bg-white border-b border-slate-200/80 hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-slate-700">Core Workflow:</span>
                    <span>1. Campaign Planning</span>
                    <span class="text-slate-300">→</span>
                    <span class="text-slate-800 font-medium">2. IMC Creative Approval</span>
                    <span class="text-slate-300">→</span>
                    <span class="text-slate-800 font-medium">3. DPM Media Spend</span>
                    <span class="text-slate-300">→</span>
                    <span class="text-slate-800 font-medium">4. OME In-Store Rollout</span>
                </div>
                <div class="text-slate-400">
                    Naivas Supermarket Operations
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toasts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-white border border-slate-200 p-4 shadow-sm flex items-start space-x-3 text-slate-800" x-data="{ show: true }" x-show="show">
                <div class="w-2 h-2 rounded-full bg-[#F7941D] mt-1.5 shrink-0"></div>
                <div class="flex-1 text-sm font-medium leading-snug">
                    {{ session('success') }}
                </div>
                <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl bg-white border border-rose-200 p-4 shadow-sm flex items-start space-x-3 text-rose-800" x-data="{ show: true }" x-show="show">
                <div class="w-2 h-2 rounded-full bg-rose-500 mt-1.5 shrink-0"></div>
                <div class="flex-1 text-sm font-medium leading-snug">
                    {{ session('error') }}
                </div>
                <button type="button" @click="show = false" class="text-rose-400 hover:text-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Clean Minimal Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-800">Naivas Supermarket</span>
                <span>• Marketing Command Center</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('portal') }}" class="hover:text-slate-800 font-medium">Judge Portal</a>
                <span>•</span>
                <a href="{{ route('demo.reset') }}" class="text-[#F7941D] hover:underline font-semibold">Reset Demo</a>
            </div>
        </div>
    </footer>

    <!-- Reset Demo Confirmation Modal -->
    <div x-show="resetConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="resetConfirmModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-2">Reset Mock Data?</h3>
            <p class="text-xs text-slate-600 mb-6">
                This will re-seed the campaigns, briefs, assets, and tasks back to the original baseline state.
            </p>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" @click="resetConfirmModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <form action="{{ route('demo.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                        Reset Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
