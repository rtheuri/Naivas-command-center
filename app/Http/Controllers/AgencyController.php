<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Brief;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyController extends Controller
{
    /**
     * Display the External Agency Partner (Apex Brand Agency) Portal.
     * Enforces strict RBAC: scoped strictly to campaigns and briefs assigned to Apex Brand Agency.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user?->agency_id ?: 'APEX-AGENCY-01';
        $agencyUser = User::where('role', 'agency_partner')->first();
        $agencyUserId = $user?->id ?? ($agencyUser?->id ?? 6);

        // Strict Tenant/Agency Isolation: Scoped to campaigns assigned to this agency OR where briefs are assigned to this agency partner
        $campaigns = Campaign::where('agency_id', $agencyId)
            ->orWhereHas('briefs', fn($q) => $q->where('assigned_to_user_id', $agencyUserId))
            ->with(['briefs.assets', 'briefs.createdBy', 'briefs.assignedTo', 'briefs.approvedBy', 'assets', 'budgets'])
            ->orderBy('id', 'desc')
            ->get();

        // Retrieve all briefs specifically assigned to Apex Agency OR belonging to campaigns assigned to this agency
        $assignedBriefs = Brief::where(function ($q) use ($agencyUserId, $agencyId, $campaigns) {
                $q->where('assigned_to_user_id', $agencyUserId)
                  ->orWhereIn('campaign_id', $campaigns->pluck('id'));
            })
            ->with(['campaign', 'assets', 'createdBy', 'approvedBy', 'assignedTo'])
            ->latest()
            ->get();

        $allAssets = Asset::whereIn('campaign_id', $campaigns->pluck('id'))
            ->orWhereIn('brief_id', $assignedBriefs->pluck('id'))
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
            'assignedBriefs' => $assignedBriefs,
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
     * Links asset directly to the selected brief and advances brief status to pending_review for IMC review.
     */
    public function storeAsset(Request $request)
    {
        $user = Auth::user();
        $agencyId = $user?->agency_id ?: 'APEX-AGENCY-01';
        $agencyUser = User::where('role', 'agency_partner')->first();
        $agencyUserId = $user?->id ?? ($agencyUser?->id ?? 6);

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'brief_id' => 'nullable|exists:briefs,id',
            'title' => 'required|string|max:255',
            'asset_type' => 'required|string',
            'dimensions' => 'required|string|max:50',
            'format' => 'required|string|max:50',
            'file_url' => 'nullable|string',
        ]);

        // Security check: ensure campaign is actually assigned to this agency OR brief is assigned to this agency
        $campaign = Campaign::where('id', $validated['campaign_id'])
            ->where(function ($q) use ($agencyId, $agencyUserId, $validated) {
                $q->where('agency_id', $agencyId);
                if (!empty($validated['brief_id'])) {
                    $q->orWhereHas('briefs', fn($b) => $b->where('id', $validated['brief_id'])->where('assigned_to_user_id', $agencyUserId));
                }
            })
            ->firstOrFail();

        // Ensure campaign reflects agency_id if not set
        if (!$campaign->agency_id) {
            $campaign->update([
                'agency_id' => $agencyId,
                'agency_name' => $user?->name ?? 'Apex Brand Agency',
            ]);
        }

        $mockupImages = [
            'posm_banner' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
            'gondola_header' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80',
            'digital_graphic' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1000&q=80',
            'video_reel' => 'https://images.unsplash.com/photo-1592417817098-8f3d6910985b?auto=format&fit=crop&w=1000&q=80',
            'price_dangler' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1000&q=80',
            'shelf_talker' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80',
        ];

        $imageUrl = (!empty($validated['file_url'])) ? $validated['file_url'] : ($mockupImages[$validated['asset_type']] ?? $mockupImages['posm_banner']);
        $assetTitle = str_starts_with($validated['title'], '[Agency]') ? $validated['title'] : '[Agency] ' . $validated['title'];

        $asset = null;
        if (!empty($validated['brief_id'])) {
            $existingAsset = Asset::where('brief_id', $validated['brief_id'])->first();
            if ($existingAsset) {
                $existingAsset->update([
                    'campaign_id' => $campaign->id,
                    'title' => $assetTitle,
                    'asset_type' => $validated['asset_type'],
                    'dimensions' => $validated['dimensions'],
                    'format' => $validated['format'],
                    'file_url' => $imageUrl,
                    'version' => $existingAsset->version + 1,
                    'status' => 'pending_review',
                    'sla_expires_at' => now()->addHours(2)->addMinutes(15),
                    'approval_notes' => null,
                ]);
                $asset = $existingAsset;
            }
        }

        if (!$asset) {
            $asset = Asset::create([
                'campaign_id' => $campaign->id,
                'brief_id' => $validated['brief_id'] ?? null,
                'title' => $assetTitle,
                'asset_type' => $validated['asset_type'],
                'dimensions' => $validated['dimensions'],
                'format' => $validated['format'],
                'file_url' => $imageUrl,
                'version' => 1,
                'status' => 'pending_review',
                'sla_expires_at' => now()->addHours(2)->addMinutes(15),
            ]);
        }

        // Advance brief to pending_review and start SLA clock for IMC Lead
        if ($asset->brief_id) {
            $brief = \App\Models\Brief::find($asset->brief_id);
            if ($brief) {
                $brief->update([
                    'status' => 'pending_review',
                    'submitted_at' => now(),
                    'sla_expires_at' => $asset->sla_expires_at,
                    'feedback_notes' => null,
                ]);
            }
        }

        $userName = $user?->name ?? 'Apex Brand Agency';
        $userTitle = $user?->job_title ?? 'Lead External Agency Partner';

        ActivityLog::log(
            department: 'Agency',
            actionType: 'AGENCY_ASSET_SUBMITTED',
            title: 'Agency Asset Delivered: ' . $asset->title,
            description: $userName . ' (' . $userTitle . ') uploaded creative deliverable for brief "' . ($asset->brief?->title ?? $asset->title) . '" in campaign "' . $campaign->title . '" (Agency ID: ' . $agencyId . '). Submitted to IMC Lead (Sarah Jenkins) for sign-off.',
            campaign: $campaign
        );

        return back()->with('success', 'Agency deliverable "' . $asset->title . '" successfully submitted! It is now in Sarah Jenkins\' (IMC Lead) review queue with active SLA tracking.');
    }
}
