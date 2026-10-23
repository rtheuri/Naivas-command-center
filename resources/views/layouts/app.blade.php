<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Marketing Command Center' }} | Naivas Supermarket</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="h-full flex flex-col text-slate-800 antialiased selection:bg-[#F7941D] selection:text-white" x-data="{ roleSwitcherOpen: false, mobileNavOpen: false, resetConfirmModal: false }">

    <!-- Top Naivas Brand Bar -->
    <header class="bg-[#006837] text-white shadow-md sticky top-0 z-40 border-b-4 border-[#F7941D]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand Title -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('portal') }}" class="flex items-center space-x-3 group">
                        <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                            <!-- Naivas Shopping Basket Icon in Naivas Green -->
                            <svg class="w-6 h-6 text-[#006837]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3h-6c0-1.66 1.34-3 3-3zm7 17H5V8h14v12zm-7-8c-1.66 0-3-1.34-3-3H7c0 2.76 2.24 5 5 5s5-2.24 5-5h-2c0 1.66-1.34 3-3 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-black tracking-tight text-white uppercase">NAIVAS</span>
                                <span class="text-xs font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-[#F7941D] text-white shadow-sm">Command Center</span>
                            </div>
                            <span class="text-[11px] text-emerald-100 hidden sm:block">Retail Marketing Orchestration Platform</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Tabs -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('executive.dashboard') }}" 
                       class="px-3 py-2 rounded-md text-xs lg:text-sm font-semibold transition-colors flex items-center space-x-1.5 {{ request()->routeIs('executive.*') ? 'bg-[#004b28] text-white ring-1 ring-emerald-400' : 'text-emerald-100 hover:bg-[#005a30] hover:text-white' }}">
                        <svg class="w-4 h-4 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Executive Hub</span>
                    </a>

                    <a href="{{ route('imc.dashboard') }}" 
                       class="px-3 py-2 rounded-md text-xs lg:text-sm font-semibold transition-colors flex items-center space-x-1.5 {{ request()->routeIs('imc.*') ? 'bg-[#004b28] text-white ring-1 ring-emerald-400' : 'text-emerald-100 hover:bg-[#005a30] hover:text-white' }}">
                        <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>1. IMC Creative</span>
                    </a>

                    <a href="{{ route('dpm.dashboard') }}" 
                       class="px-3 py-2 rounded-md text-xs lg:text-sm font-semibold transition-colors flex items-center space-x-1.5 {{ request()->routeIs('dpm.*') ? 'bg-[#004b28] text-white ring-1 ring-emerald-400' : 'text-emerald-100 hover:bg-[#005a30] hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>2. DPM Media</span>
                    </a>

                    <a href="{{ route('ome.dashboard') }}" 
                       class="px-3 py-2 rounded-md text-xs lg:text-sm font-semibold transition-colors flex items-center space-x-1.5 {{ request()->routeIs('ome.*') ? 'bg-[#004b28] text-white ring-1 ring-emerald-400' : 'text-emerald-100 hover:bg-[#005a30] hover:text-white' }}">
                        <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>3. OME Store Ops</span>
                    </a>
                </nav>

                <!-- Judge Persona Switcher & Controls -->
                <div class="flex items-center space-x-3">
                    
                    <!-- Reset Demo Button -->
                    <button type="button" @click="resetConfirmModal = true" 
                            title="Reset Mock Data to Default"
                            class="hidden sm:inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-md bg-[#004b28] text-emerald-100 hover:bg-[#003d21] hover:text-white border border-emerald-700/60 transition">
                        <svg class="w-3.5 h-3.5 mr-1 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Reset Demo
                    </button>

                    <!-- Judge Persona Pill & Dropdown Switcher -->
                    @auth
                    <div class="relative" @click.away="roleSwitcherOpen = false">
                        <button type="button" 
                                @click="roleSwitcherOpen = !roleSwitcherOpen"
                                class="flex items-center space-x-2 pl-2 pr-3 py-1.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white transition focus:outline-none">
                            <span class="w-7 h-7 rounded-full bg-[#F7941D] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="text-left hidden lg:block leading-tight">
                                <div class="text-xs font-bold text-white flex items-center space-x-1">
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-[#F7941D] text-white uppercase">{{ Auth::user()->department }}</span>
                                </div>
                                <div class="text-[10px] text-emerald-200 truncate max-w-[140px]">{{ Auth::user()->getRoleDisplayName() }}</div>
                            </div>
                            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Dropdown Menu for Judges -->
                        <div x-show="roleSwitcherOpen" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 rounded-xl bg-white shadow-2xl ring-1 ring-black/5 divide-y divide-gray-100 z-50 overflow-hidden text-slate-800">
                            
                            <div class="px-4 py-3 bg-gradient-to-r from-emerald-50 to-amber-50">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Judge Persona Switcher</p>
                                <p class="text-xs font-semibold text-slate-800">Switch role instantly without password:</p>
                            </div>

                            <div class="p-1.5 space-y-1">
                                <!-- Role 1: IMC Lead -->
                                <a href="{{ route('switch.role', 'imc_lead') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-purple-50 transition group {{ Auth::user()->role === 'imc_lead' ? 'bg-purple-100/70 font-semibold' : '' }}">
                                    <span class="w-7 h-7 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold mr-2 text-xs">IMC</span>
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 flex items-center justify-between">
                                            <span>Amina Mwangi</span>
                                            @if(Auth::user()->role === 'imc_lead')<span class="text-[10px] text-purple-700 font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">IMC Lead (Creative Approvals)</div>
                                    </div>
                                </a>

                                <!-- Role 2: DPM Media Buyer -->
                                <a href="{{ route('switch.role', 'dpm_buyer') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-blue-50 transition group {{ Auth::user()->role === 'dpm_buyer' ? 'bg-blue-100/70 font-semibold' : '' }}">
                                    <span class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold mr-2 text-xs">DPM</span>
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 flex items-center justify-between">
                                            <span>Kevin Omondi</span>
                                            @if(Auth::user()->role === 'dpm_buyer')<span class="text-[10px] text-blue-700 font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">DPM Media Buyer (Budgets & Ads)</div>
                                    </div>
                                </a>

                                <!-- Role 3: OME Store Manager -->
                                <a href="{{ route('switch.role', 'ome_manager') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-emerald-50 transition group {{ Auth::user()->role === 'ome_manager' ? 'bg-emerald-100/70 font-semibold' : '' }}">
                                    <span class="w-7 h-7 rounded-lg bg-[#006837] text-white flex items-center justify-center font-bold mr-2 text-xs">OME</span>
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 flex items-center justify-between">
                                            <span>Faith Wanjiku</span>
                                            @if(Auth::user()->role === 'ome_manager')<span class="text-[10px] text-emerald-700 font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">OME Store Manager (Mega City)</div>
                                    </div>
                                </a>

                                <!-- Role 4: Marketing Executive -->
                                <a href="{{ route('switch.role', 'marketing_exec') }}" 
                                   class="flex items-center px-3 py-2 rounded-lg text-xs hover:bg-amber-50 transition group {{ Auth::user()->role === 'marketing_exec' ? 'bg-amber-100/70 font-semibold' : '' }}">
                                    <span class="w-7 h-7 rounded-lg bg-[#F7941D] text-white flex items-center justify-center font-bold mr-2 text-xs">CMO</span>
                                    <div class="flex-1">
                                        <div class="font-medium text-slate-900 flex items-center justify-between">
                                            <span>Peter Mumo</span>
                                            @if(Auth::user()->role === 'marketing_exec')<span class="text-[10px] text-amber-700 font-bold">Active</span>@endif
                                        </div>
                                        <div class="text-[11px] text-slate-500">Chief Marketing Officer</div>
                                    </div>
                                </a>
                            </div>

                            <div class="p-2 bg-gray-50 flex items-center justify-between">
                                <a href="{{ route('portal') }}" class="text-xs text-slate-600 hover:text-[#006837] font-medium flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Judge Portal
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-medium">Log out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('portal') }}" class="px-3 py-1.5 rounded-lg bg-[#F7941D] text-white font-semibold text-xs shadow hover:bg-[#dc760f] transition">
                        Select Judge Persona
                    </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button type="button" @click="mobileNavOpen = !mobileNavOpen" class="md:hidden p-2 rounded-lg bg-white/10 text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Nav Menu -->
        <div x-show="mobileNavOpen" x-cloak class="md:hidden bg-[#004b28] px-4 pt-2 pb-4 space-y-2 border-t border-emerald-700">
            <a href="{{ route('executive.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-[#003d21]">Executive Hub</a>
            <a href="{{ route('imc.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-[#003d21]">1. IMC Creative Pipeline</a>
            <a href="{{ route('dpm.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-[#003d21]">2. DPM Media & Budgets</a>
            <a href="{{ route('ome.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-white hover:bg-[#003d21]">3. OME Store Ops (Mobile Proof)</a>
            <a href="{{ route('demo.reset') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-amber-300 hover:bg-[#003d21]">Reset Mock Demo Data</a>
        </div>
    </header>

    <!-- Sub-Header Sequential Workflow Ribbon -->
    <div class="bg-white border-b border-gray-200 shadow-xs hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="flex items-center justify-between text-xs text-slate-600">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-slate-800 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-ping"></span>
                        Sequential Workflow:
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">1. Campaign Planned</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-purple-800 font-medium">2. IMC Approves Assets</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">3. DPM Media Deployed</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-medium">4. OME In-Store Rollout</span>
                </div>
                <div class="flex items-center space-x-3 text-slate-500">
                    <span class="flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                        PostgreSQL Ready
                    </span>
                    <span>•</span>
                    <span class="font-semibold text-[#006837]">Naivas Supermarket Kenya</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Alerts / Toasts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 shadow-sm flex items-start space-x-3" x-data="{ show: true }" x-show="show">
                <div class="shrink-0 text-emerald-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-1 text-sm font-medium text-emerald-900 leading-snug">
                    {{ session('success') }}
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-4 shadow-sm flex items-start space-x-3" x-data="{ show: true }" x-show="show">
                <div class="shrink-0 text-rose-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-1 text-sm font-medium text-rose-900 leading-snug">
                    {{ session('error') }}
                </div>
                <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 p-4 shadow-sm flex items-start space-x-3" x-data="{ show: true }" x-show="show">
                <div class="shrink-0 text-blue-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-1 text-sm font-medium text-blue-900 leading-snug">
                    {{ session('info') }}
                </div>
                <button type="button" @click="show = false" class="text-blue-500 hover:text-blue-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-6 mt-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-5 h-5 rounded bg-[#006837] text-white flex items-center justify-center font-bold text-[10px]">N</div>
                <span class="font-semibold text-slate-800">Naivas Supermarket Kenya</span>
                <span>• Marketing Command Center MVP Prototype</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('portal') }}" class="hover:text-[#006837] font-medium">Judge Review Portal</a>
                <span>•</span>
                <a href="{{ route('demo.reset') }}" class="text-[#F7941D] hover:underline font-semibold">Reset Demo Data</a>
                <span>•</span>
                <span class="text-slate-400">Laravel 12 + Tailwind + PostgreSQL Ready</span>
            </div>
        </div>
    </footer>

    <!-- Reset Demo Confirmation Modal -->
    <div x-show="resetConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="resetConfirmModal = false">
            <div class="flex items-center space-x-3 text-amber-600 mb-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Reset Mock Database?</h3>
            </div>
            <p class="text-sm text-slate-600 mb-6">
                This will re-run the database migrations and seeders, restoring the 5 pre-seeded campaigns, briefs, assets, budgets, and store tasks back to their pristine demonstration state.
            </p>
            <div class="flex items-center justify-end space-x-3">
                <button type="button" @click="resetConfirmModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                    Cancel
                </button>
                <form action="{{ route('demo.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#006837] text-white hover:bg-[#005a30] shadow">
                        Yes, Reset Demo
                    </button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
