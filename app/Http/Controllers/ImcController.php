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

        // Categorize into Kanban Columns (avoiding duplicate cards when an asset proposal exists for a brief)
        $pendingAssets = $allAssets->where('status', 'pending_review');
        $approvedAssets = $allAssets->where('status', 'approved');
        $draftAssets = $allAssets->where('status', 'draft');

        $kanban = [
            'draft' => [
                'briefs' => $allBriefs->where('status', 'draft'),
                'assets' => $draftAssets,
            ],
            'pending_review' => [
                'briefs' => $allBriefs->where('status', 'pending_review')->filter(fn($b) => $b->assets->where('status', 'pending_review')->isEmpty()),
                'assets' => $pendingAssets,
            ],
            'approved' => [
                'briefs' => $allBriefs->where('status', 'approved')->filter(fn($b) => $b->assets->where('status', 'approved')->isEmpty()),
                'assets' => $approvedAssets,
            ],
        ];

        // Summary Counts
        $stats = [
            'draft_count' => $kanban['draft']['briefs']->count() + $kanban['draft']['assets']->count(),
            'pending_count' => $kanban['pending_review']['briefs']->count() + $kanban['pending_review']['assets']->count(),
            'approved_count' => $kanban['approved']['briefs']->count() + $kanban['approved']['assets']->count(),
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

        $user = Auth::user();
        $userName = $user?->name ?? 'Amina Mwangi (IMC Lead)';
        $userId = $user?->id ?? 1;

        $brief = Brief::create([
            'campaign_id' => $validated['campaign_id'],
            'created_by_user_id' => $userId,
            'title' => $validated['title'],
            'objective' => $validated['objective'],
            'target_segment' => $validated['target_segment'],
            'key_messaging' => $validated['key_messaging'],
            'channel' => $validated['channel'],
            'status' => $validated['status'],
            'submitted_at' => $validated['status'] === 'pending_review' ? now() : null,
        ]);

        $campaign = $brief->campaign;

        ActivityLog::log(
            department: 'IMC',
            actionType: 'BRIEF_CREATED',
            title: 'Brief Created: ' . $brief->title,
            description: $userName . ' created brief for campaign "' . $campaign->title . '".',
            campaign: $campaign
        );

        return back()->with('success', 'Brief created successfully.');
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

        $user = Auth::user();
        $userName = $user?->name ?? 'Amina Mwangi (IMC Lead)';

        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_SUBMITTED',
            title: 'Creative Asset Submitted: ' . $asset->title,
            description: $userName . ' submitted creative asset for review in campaign "' . $campaign->title . '".',
            campaign: $campaign
        );

        return back()->with('success', 'Creative asset submitted for review.');
    }

    /**
     * Approve an asset (IMC Lead action).
     * Automatically transitions campaign status to "Ready for Launch",
     * unlocking DPM Ad deployment and OME store rollout!
     */
    public function approveAsset(Request $request, Asset $asset)
    {
        $notes = $request->input('notes', 'Approved by IMC Lead according to Naivas brand standards.');
        $user = Auth::user();
        $userName = $user?->name ?? 'Amina Mwangi (IMC Lead)';
        $userId = $user?->id ?? 1;

        $asset->update([
            'status' => 'approved',
            'approval_notes' => $notes,
            'approved_at' => now(),
            'approved_by_user_id' => $userId,
        ]);

        // Also approve attached brief if it exists
        if ($asset->brief && $asset->brief->status !== 'approved') {
            $asset->brief->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by_user_id' => $userId,
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
            title: 'Asset Approved: ' . $asset->title,
            description: $userName . ' approved creative asset "' . $asset->title . '" for campaign "' . $campaign->title . '".',
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
                'message' => 'Asset approved. Campaign is ready for launch.',
                'campaign_status' => $campaign->status,
                'asset' => $asset,
            ]);
        }

        return back()->with('success', 'Asset approved. Campaign is ready for launch.');
    }

    /**
     * Reject an asset with revision notes.
     */
    public function rejectAsset(Request $request, Asset $asset)
    {
        $notes = $request->input('notes', 'Requested revisions from creative agency.');
        $user = Auth::user();
        $userName = $user?->name ?? 'Amina Mwangi (IMC Lead)';

        $asset->update([
            'status' => 'rejected',
            'approval_notes' => $notes,
        ]);

        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_REJECTED',
            title: 'Asset Revision Requested: ' . $asset->title,
            description: $userName . ' returned asset "' . $asset->title . '" for revisions. Notes: ' . $notes,
            campaign: $asset->campaign
        );

        return back()->with('info', 'Asset returned with revision notes.');
    }
}
