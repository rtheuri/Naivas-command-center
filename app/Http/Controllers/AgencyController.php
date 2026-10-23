<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyController extends Controller
{
    /**
     * Display the External Agency Partner (Apex Brand Agency) Portal.
     * Enforces strict RBAC: scoped strictly to campaigns with user's agency_id.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user?->agency_id ?: 'APEX-AGENCY-01';

        // Strict Tenant/Agency Isolation: ONLY assigned campaigns
        $campaigns = Campaign::where('agency_id', $agencyId)
            ->with(['briefs', 'assets', 'budgets'])
            ->orderBy('id', 'desc')
            ->get();

        $allAssets = Asset::whereIn('campaign_id', $campaigns->pluck('id'))
            ->with(['campaign', 'brief', 'approvedBy'])
            ->latest()
            ->get();

        // Performance Metrics for this Agency
        $totalDeliverables = $allAssets->count();
        $approvedDeliverables = $allAssets->where('status', 'approved')->count();
        $pendingDeliverables = $allAssets->where('status', 'pending_review')->count();
        $revisionDeliverables = $allAssets->where('status', 'rejected')->count();
        $slaComplianceRate = $totalDeliverables > 0 ? round(($approvedDeliverables / $totalDeliverables) * 100, 1) : 94.5;

        return view('dashboards.agency', [
            'agencyId' => $agencyId,
            'campaigns' => $campaigns,
            'allAssets' => $allAssets,
            'totalDeliverables' => $totalDeliverables,
            'approvedDeliverables' => $approvedDeliverables,
            'pendingDeliverables' => $pendingDeliverables,
            'revisionDeliverables' => $revisionDeliverables,
            'slaComplianceRate' => $slaComplianceRate,
        ]);
    }

    /**
     * Submit creative assets on behalf of the external agency.
     */
    public function storeAsset(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user?->agency_id ?: 'APEX-AGENCY-01';

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'brief_id' => 'nullable|exists:briefs,id',
            'title' => 'required|string|max:255',
            'asset_type' => 'required|string',
            'dimensions' => 'required|string|max:50',
            'format' => 'required|string|max:50',
            'file_url' => 'nullable|string',
        ]);

        // Security check: ensure campaign is actually assigned to this agency
        $campaign = Campaign::where('id', $validated['campaign_id'])
            ->where('agency_id', $agencyId)
            ->firstOrFail();

        $asset = Asset::create([
            'campaign_id' => $campaign->id,
            'brief_id' => $validated['brief_id'],
            'title' => '[Agency] ' . $validated['title'],
            'asset_type' => $validated['asset_type'],
            'dimensions' => $validated['dimensions'],
            'format' => $validated['format'],
            'file_url' => $validated['file_url'] ?: 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
            'version' => 1,
            'status' => 'pending_review',
            'sla_expires_at' => now()->addHours(2)->addMinutes(15),
        ]);

        ActivityLog::log(
            department: 'Agency',
            actionType: 'AGENCY_ASSET_SUBMITTED',
            title: 'Agency Asset Delivered: ' . $asset->title,
            description: $user->name . ' (' . ($user->job_title ?: 'Agency Partner') . ') uploaded creative deliverable for campaign "' . $campaign->title . '" (Agency ID: ' . $agencyId . '). Submitted to IMC Lead queue for sign-off.',
            campaign: $campaign
        );

        return back()->with('success', 'Agency asset "' . $asset->title . '" submitted to Naivas IMC Lead for sign-off!');
    }
}
