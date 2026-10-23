<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Brief;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImcController extends Controller
{
    /**
     * Display the IMC Briefing & Approval Pipeline (Kanban Board).
     */
    public function index(Request $request)
    {
        $campaigns = Campaign::orderBy('title')->get();
        $selectedCampaignId = $request->query('campaign_id');

        $briefsQuery = Brief::with(['campaign', 'createdBy', 'approvedBy', 'assets']);
        $assetsQuery = Asset::with(['campaign', 'brief', 'approvedBy']);

        if ($selectedCampaignId) {
            $briefsQuery->where('campaign_id', $selectedCampaignId);
            $assetsQuery->where('campaign_id', $selectedCampaignId);
        }

        $allBriefs = $briefsQuery->latest()->get();
        $allAssets = $assetsQuery->latest()->get();

        // Categorize into Kanban Columns
        $kanban = [
            'draft' => [
                'briefs' => $allBriefs->where('status', 'draft'),
                'assets' => $allAssets->where('status', 'draft'),
            ],
            'pending_review' => [
                'briefs' => $allBriefs->where('status', 'pending_review'),
                'assets' => $allAssets->where('status', 'pending_review'),
            ],
            'approved' => [
                'briefs' => $allBriefs->where('status', 'approved'),
                'assets' => $allAssets->where('status', 'approved'),
            ],
        ];

        // Summary Counts
        $stats = [
            'draft_count' => $allBriefs->where('status', 'draft')->count() + $allAssets->where('status', 'draft')->count(),
            'pending_count' => $allBriefs->where('status', 'pending_review')->count() + $allAssets->where('status', 'pending_review')->count(),
            'approved_count' => $allBriefs->where('status', 'approved')->count() + $allAssets->where('status', 'approved')->count(),
            'total_assets' => $allAssets->count(),
        ];

        return view('dashboards.imc', [
            'campaigns' => $campaigns,
            'selectedCampaignId' => $selectedCampaignId,
            'kanban' => $kanban,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created marketing brief.
     */
    public function storeBrief(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'title' => 'required|string|max:255',
            'objective' => 'required|string',
            'target_segment' => 'required|string|max:255',
            'key_messaging' => 'required|string',
            'channel' => 'required|string',
            'status' => 'required|in:draft,pending_review',
        ]);

        $brief = Brief::create([
            'campaign_id' => $validated['campaign_id'],
            'created_by_user_id' => Auth::id(),
            'title' => $validated['title'],
            'objective' => $validated['objective'],
            'target_segment' => $validated['target_segment'],
            'key_messaging' => $validated['key_messaging'],
            'channel' => $validated['channel'],
            'status' => $validated['status'],
            'submitted_at' => $validated['status'] === 'pending_review' ? now() : null,
        ]);

        $campaign = $brief->campaign;

        // Auto create an initial mock creative asset paired with this brief
        Asset::create([
            'campaign_id' => $campaign->id,
            'brief_id' => $brief->id,
            'title' => $brief->title . ' - Key POSM Graphic',
            'asset_type' => 'posm_banner',
            'file_url' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80',
            'dimensions' => '1200x600mm',
            'format' => 'Print PDF',
            'version' => 1,
            'status' => $validated['status'],
            'approval_notes' => 'Generated from creative brief submission.',
        ]);

        ActivityLog::log(
            department: 'IMC',
            actionType: 'BRIEF_CREATED',
            title: 'New IMC Brief: ' . $brief->title,
            description: Auth::user()->name . ' created creative brief for campaign "' . $campaign->title . '". Status: ' . ucfirst(str_replace('_', ' ', $brief->status)),
            campaign: $campaign
        );

        return back()->with('success', 'Brief "' . $brief->title . '" created successfully and queued in pipeline!');
    }

    /**
     * Submit a new standalone creative asset for review.
     */
    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'brief_id' => 'nullable|exists:briefs,id',
            'title' => 'required|string|max:255',
            'asset_type' => 'required|string',
            'dimensions' => 'required|string|max:50',
            'format' => 'required|string|max:50',
            'file_url' => 'nullable|string',
        ]);

        // Default mockup images for retail feel
        $mockupImages = [
            'posm_banner' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
            'gondola_header' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80',
            'digital_graphic' => 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?auto=format&fit=crop&w=1000&q=80',
            'video_reel' => 'https://images.unsplash.com/photo-1592417817098-8f3d6910985b?auto=format&fit=crop&w=1000&q=80',
            'price_dangler' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1000&q=80',
            'shelf_talker' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=80',
        ];

        $imageUrl = $validated['file_url'] ?: ($mockupImages[$validated['asset_type']] ?? $mockupImages['posm_banner']);

        $asset = Asset::create([
            'campaign_id' => $validated['campaign_id'],
            'brief_id' => $validated['brief_id'],
            'title' => $validated['title'],
            'asset_type' => $validated['asset_type'],
            'dimensions' => $validated['dimensions'],
            'format' => $validated['format'],
            'file_url' => $imageUrl,
            'version' => 1,
            'status' => 'pending_review',
        ]);

        $campaign = $asset->campaign;

        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_SUBMITTED',
            title: 'Creative Asset Submitted: ' . $asset->title,
            description: Auth::user()->name . ' uploaded new creative asset (' . $asset->getAssetTypeLabel() . ') for review in campaign "' . $campaign->title . '".',
            campaign: $campaign
        );

        return back()->with('success', 'Asset "' . $asset->title . '" submitted for IMC review.');
    }

    /**
     * Approve an asset (IMC Lead action).
     * Automatically transitions campaign status to "Ready for Launch",
     * unlocking DPM Ad deployment and OME store rollout!
     */
    public function approveAsset(Request $request, Asset $asset)
    {
        $notes = $request->input('notes', 'Approved by IMC Lead according to Naivas brand standards.');

        $asset->update([
            'status' => 'approved',
            'approval_notes' => $notes,
            'approved_at' => now(),
            'approved_by_user_id' => Auth::id(),
        ]);

        // Also approve attached brief if it exists
        if ($asset->brief && $asset->brief->status !== 'approved') {
            $asset->brief->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by_user_id' => Auth::id(),
            ]);
        }

        // Sequential interrelation: Update campaign status to 'ready_for_launch'
        $campaign = $asset->campaign;
        $previousStatus = $campaign->status;

        if (in_array($previousStatus, ['planning', 'pending_approval'])) {
            $campaign->update([
                'status' => 'ready_for_launch',
            ]);
        }

        // Log Cross-Department Action
        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_APPROVED',
            title: 'IMC Sign-Off: ' . $asset->title . ' (Campaign Unlocked)',
            description: Auth::user()->name . ' approved creative asset "' . $asset->title . '". Campaign "' . $campaign->title . '" automatically upgraded to "Ready for Launch" — UNLOCKING DPM media deployment & OME store execution!',
            campaign: $campaign,
            metadata: [
                'asset_id' => $asset->id,
                'previous_campaign_status' => $previousStatus,
                'new_campaign_status' => $campaign->status,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset approved! Campaign is now Ready for Launch.',
                'campaign_status' => $campaign->status,
                'asset' => $asset,
            ]);
        }

        return back()->with('success', 'Asset "' . $asset->title . '" approved. Campaign "' . $campaign->title . '" is now ready for launch (Actions unlocked in DPM & OME).');
    }

    /**
     * Reject an asset with revision notes.
     */
    public function rejectAsset(Request $request, Asset $asset)
    {
        $notes = $request->input('notes', 'Requested revisions from creative agency.');

        $asset->update([
            'status' => 'rejected',
            'approval_notes' => $notes,
        ]);

        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_REJECTED',
            title: 'Asset Revision Requested: ' . $asset->title,
            description: Auth::user()->name . ' flagged asset "' . $asset->title . '" for revisions. Notes: ' . $notes,
            campaign: $asset->campaign
        );

        return back()->with('info', 'Asset "' . $asset->title . '" returned for revisions.');
    }
}
