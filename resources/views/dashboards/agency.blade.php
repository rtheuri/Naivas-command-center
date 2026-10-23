@extends('layouts.app', ['title' => 'External Agency Partner Portal'])

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Scoped Tenant Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <span>External Partner Workspace</span>
                <span>•</span>
                <span class="text-blue-600 font-semibold">Persona 6: Agency Partner</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 flex items-center space-x-2">
                <span>Apex Brand Agency</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold tracking-wider">
                    Agency ID: {{ $agencyId }}
                </span>
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Permission-isolated agency tenant. Scoped exclusively to campaigns explicitly assigned to Apex Agency by Naivas Head of Marketing.
            </p>
        </div>

        <button type="button" @click="$dispatch('open-agency-modal')" 
                class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs flex items-center space-x-2">
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
                <p class="text-xs text-slate-500">Only campaigns explicitly allocated to Agency ID <code class="text-blue-600 font-mono">{{ $agencyId }}</code> appear in this view.</p>
            </div>
            <span class="text-xs text-slate-400 font-medium">Tenant Filter: Active</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($campaigns as $camp)
            <div class="p-5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-slate-300 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $camp->code }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                        @if($camp->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @elseif($camp->status === 'ready_for_launch') bg-orange-50 text-[#F7941D] border border-orange-200
                        @else bg-slate-100 text-slate-700 @endif">
                        {{ str_replace('_', ' ', $camp->status) }}
                    </span>
                </div>

                <div>
                    <h4 class="text-sm font-bold text-slate-900">{{ $camp->title }}</h4>
                    <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $camp->description }}</p>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-200 text-xs text-slate-600">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase block">Briefs</span>
                        <span class="font-bold text-slate-800">{{ $camp->briefs->count() }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase block">Assets</span>
                        <span class="font-bold text-slate-800">{{ $camp->assets->count() }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase block">Target Date</span>
                        <span class="font-bold text-slate-800">{{ $camp->end_date?->format('M d') ?? 'Active' }}</span>
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
        <h3 class="text-sm font-bold text-slate-900 mb-1">Delivered Creative Assets</h3>
        <p class="text-xs text-slate-500 mb-4">Artwork submissions delivered to Naivas IMC Lead for sign-off.</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase text-[10px] font-bold">
                        <th class="py-2.5">Asset Title</th>
                        <th class="py-2.5">Campaign</th>
                        <th class="py-2.5">Format & Specs</th>
                        <th class="py-2.5">Status</th>
                        <th class="py-2.5">Sign-Off Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($allAssets as $asset)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 font-semibold text-slate-900">{{ $asset->title }}</td>
                        <td class="py-3 text-slate-600">{{ $asset->campaign->title }}</td>
                        <td class="py-3 text-slate-500">{{ $asset->dimensions }} • {{ $asset->format }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                @if($asset->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                                @elseif($asset->status === 'pending_review') bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($asset->status === 'rejected') bg-rose-50 text-rose-700 border border-rose-200
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ str_replace('_', ' ', $asset->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-500">
                            {{ $asset->approvedBy ? $asset->approvedBy->name : 'Awaiting Amina Mwangi (IMC Lead)' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">No assets delivered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Agency Deliverable Modal -->
<div x-data="{ open: false }" 
     @open-agency-modal.window="open = true"
     x-show="open" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4" @click.away="open = false">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Upload Agency Deliverable (Apex Agency)</h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('agency.assets.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Assigned Campaign</label>
                <select name="campaign_id" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                    @foreach($campaigns as $camp)
                    <option value="{{ $camp->id }}">{{ $camp->code }} - {{ $camp->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Artwork Title</label>
                <input type="text" name="title" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2" placeholder="e.g. Back to School Station Shelf Banner">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Asset Type</label>
                    <select name="asset_type" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                        <option value="posm_banner">POSM Banner</option>
                        <option value="gondola_header">Gondola Header</option>
                        <option value="digital_graphic">Digital Graphic</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dimensions</label>
                    <input type="text" name="dimensions" value="1920x1080px" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Format</label>
                <input type="text" name="format" value="High-Res CMYK Print Ready PDF" required class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deliverable URL (Optional)</label>
                <input type="text" name="file_url" class="w-full text-xs rounded-lg border border-slate-300 px-3 py-2" placeholder="Leave blank for auto mockup image">
            </div>

            <div class="pt-2 flex items-center justify-end space-x-2">
                <button type="button" @click="open = false" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs">
                    Submit Deliverable to IMC Lead
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
