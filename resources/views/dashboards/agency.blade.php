@extends('layouts.app', ['title' => 'External Agency Partner Portal'])

@section('content')
<div class="space-y-6">

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

        <button type="button" @click="$dispatch('open-agency-modal')" 
                class="px-4 py-2 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold transition shadow-xs flex items-center space-x-2">
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
                    <span class="text-xs font-bold text-[#F7941D] bg-orange-50 px-2 py-0.5 rounded border border-orange-200">{{ $camp->code }}</span>
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
                            {{ $asset->approvedBy ? $asset->approvedBy->name : 'Awaiting Sarah Jenkins (IMC Lead)' }}
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
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl max-w-lg w-full max-h-[84vh] flex flex-col shadow-2xl border border-slate-200 text-slate-800 overflow-hidden my-auto" @click.away="open = false">
        <div class="px-4 py-2.5 sm:px-5 sm:py-3 border-b border-slate-100 flex items-center justify-between shrink-0 bg-white">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Upload Agency Deliverable (Apex Agency)</h3>
                <p class="text-[10px] sm:text-[11px] text-slate-500">Attach artwork proposal for IMC Lead review and sign-off</p>
            </div>
            <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-md hover:bg-slate-100 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('agency.assets.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
            @csrf
            
            <div class="p-3.5 sm:p-4 space-y-2.5 overflow-y-auto flex-1 text-xs overscroll-contain">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Select Assigned Campaign *</label>
                    <select name="campaign_id" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                        @foreach($campaigns as $camp)
                        <option value="{{ $camp->id }}">{{ $camp->code }} - {{ $camp->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Artwork Title *</label>
                    <input type="text" name="title" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none" placeholder="e.g. Back to School Station Shelf Banner">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Asset Type *</label>
                        <select name="asset_type" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none bg-white">
                            <option value="posm_banner">POSM Banner</option>
                            <option value="gondola_header">Gondola Header</option>
                            <option value="digital_graphic">Digital Graphic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Dimensions *</label>
                        <input type="text" name="dimensions" value="1920x1080px" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Format *</label>
                    <input type="text" name="format" value="High-Res CMYK Print Ready PDF" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-700 mb-0.5">Deliverable URL (Optional)</label>
                    <input type="text" name="file_url" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none" placeholder="Leave blank for auto mockup image">
                </div>
            </div>

            <div class="px-4 py-2 sm:px-5 sm:py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" @click="open = false" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-white transition cursor-pointer">Cancel</button>
                <button type="submit" class="px-3.5 py-1.5 text-xs font-bold text-white bg-[#F7941D] hover:bg-[#e07f0f] rounded-lg shadow-xs transition cursor-pointer shrink-0">
                    Submit Deliverable
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
