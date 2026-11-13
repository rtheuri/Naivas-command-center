<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Brief;
use App\Models\Campaign;
use App\Models\StoreTask;
use App\Models\User;
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
        $draftAssets = $allAssets->where('status', 'draft')->filter(function ($a) {
            if ($a->brief && in_array($a->brief->status, ['pending_review', 'approved'])) {
                return false;
            }
            return true;
        });

        $kanban = [
            'draft' => [
                'briefs' => $allBriefs->where('status', 'draft')->filter(fn($b) => $b->assets->where('status', 'draft')->isEmpty()),
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

        $creativeUsers = User::whereIn('role', ['creative_designer', 'agency_partner'])->get();

        return view('dashboards.imc', [
            'campaigns' => $campaigns,
            'selectedCampaignId' => $selectedCampaignId,
            'kanban' => $kanban,
            'stats' => $stats,
            'creativeUsers' => $creativeUsers,
        ]);
    }

    /**
     * Store a newly created marketing brief.
     * All briefs automatically enter the 'In Design' pipeline (status: draft)
     * and land directly in the To-Do list of the chosen partner (Creative Studio or Agency).
     */
    public function storeBrief(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'objective' => 'required|string',
            'target_segment' => 'required|string|max:255',
            'key_messaging' => 'required|string',
            'channel' => 'required|string',
            'sla_hours' => 'nullable|integer|min:1|max:168',
        ]);

        $user = Auth::user();
        $userName = $user?->name ?? 'Sarah Jenkins (IMC Lead)';
        $userId = $user?->id ?? 1;

        $slaHours = (int) ($validated['sla_hours'] ?? 24);
        $slaExpiresAt = now()->addHours($slaHours);
        $assignedUserId = $validated['assigned_to_user_id'] ?? 5;
        $assignedUser = User::find($assignedUserId);

        // Dispatches directly to In Design queue (status: draft)
        $brief = Brief::create([
            'campaign_id' => $validated['campaign_id'],
            'created_by_user_id' => $userId,
            'assigned_to_user_id' => $assignedUserId,
            'title' => $validated['title'],
            'objective' => $validated['objective'],
            'target_segment' => $validated['target_segment'],
            'key_messaging' => $validated['key_messaging'],
            'channel' => $validated['channel'],
            'status' => 'draft', // In Design
            'submitted_at' => null,
            'sla_expires_at' => $slaExpiresAt,
        ]);

        $campaign = $brief->campaign;

        // If assigned to an agency partner, ensure campaign also links to agency workspace
        if ($assignedUser && ($assignedUser->role === 'agency_partner' || $assignedUser->agency_id)) {
            if ($campaign && (!$campaign->agency_id || $campaign->agency_id !== ($assignedUser->agency_id ?: 'APEX-AGENCY-01'))) {
                $campaign->update([
                    'agency_id' => $assignedUser->agency_id ?: 'APEX-AGENCY-01',
                    'agency_name' => $assignedUser->name,
                ]);
            }
        }

        $assignedName = $brief->assignedTo?->name ?? 'Creative Studio';

        ActivityLog::log(
            department: 'IMC',
            actionType: 'BRIEF_CREATED',
            title: 'Brief Created: ' . $brief->title,
            description: $userName . ' created brief for campaign "' . $campaign->title . '" dispatched to ' . $assignedName . ' (In Design, ' . $slaHours . 'h SLA).',
            campaign: $campaign
        );

        return back()->with('success', 'Brief "' . $brief->title . '" created & dispatched to ' . $assignedName . ' with full execution specifications!');
    }

    /**
     * Update an existing marketing brief while in design.
     */
    public function updateBrief(Request $request, Brief $brief)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'assigned_to_user_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'objective' => 'required|string',
            'target_segment' => 'required|string|max:255',
            'key_messaging' => 'required|string',
            'channel' => 'required|string',
            'sla_hours' => 'nullable|integer|min:1|max:168',
        ]);

        $user = Auth::user();
        $userName = $user?->name ?? 'Sarah Jenkins (IMC Lead)';

        $slaHours = (int) ($validated['sla_hours'] ?? 24);
        $slaExpiresAt = now()->addHours($slaHours);
        $assignedUserId = $validated['assigned_to_user_id'] ?? $brief->assigned_to_user_id ?? 5;
        $assignedUser = User::find($assignedUserId);

        $brief->update([
            'campaign_id' => $validated['campaign_id'],
            'assigned_to_user_id' => $assignedUserId,
            'title' => $validated['title'],
            'objective' => $validated['objective'],
            'target_segment' => $validated['target_segment'],
            'key_messaging' => $validated['key_messaging'],
            'channel' => $validated['channel'],
            'sla_expires_at' => $slaExpiresAt,
        ]);

        // If assigned to an agency partner, ensure campaign also links to agency workspace
        if ($assignedUser && ($assignedUser->role === 'agency_partner' || $assignedUser->agency_id)) {
            $campaign = $brief->campaign;
            if ($campaign && (!$campaign->agency_id || $campaign->agency_id !== ($assignedUser->agency_id ?: 'APEX-AGENCY-01'))) {
                $campaign->update([
                    'agency_id' => $assignedUser->agency_id ?: 'APEX-AGENCY-01',
                    'agency_name' => $assignedUser->name,
                ]);
            }
        }

        $assignedName = $brief->assignedTo?->name ?? 'Creative Studio';

        ActivityLog::log(
            department: 'IMC',
            actionType: 'BRIEF_UPDATED',
            title: 'Brief Updated: ' . $brief->title,
            description: $userName . ' updated brief specifications for "' . $brief->campaign->title . '" (' . $assignedName . ', In Design).',
            campaign: $brief->campaign
        );

        return back()->with('success', 'Brief specifications updated successfully in ' . $assignedName . '\'s In Design pipeline.');
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
        $userName = $user?->name ?? 'Sarah Jenkins (IMC Lead)';

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
        $userName = $user?->name ?? 'Sarah Jenkins (IMC Lead)';
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

        // Automatically dispatch in-store execution tasks to OME branches for physical POSM rollout
        $branches = [
            'Naivas Mega City',
            'Naivas Westlands',
            'Naivas Junction',
            'Naivas Kilimani',
            'Naivas Mombasa Road',
            'Naivas Karen',
        ];

        $taskZone = match ($asset->asset_type) {
            'posm_banner' => 'Entrance & Main Aisle',
            'gondola_header' => 'Gondola End Cap',
            'shelf_talker' => 'Dry Grocery Shelving',
            'price_dangler' => 'Butchery & Fresh Deli',
            default => 'Promotional Feature Zone',
        };

        foreach ($branches as $branch) {
            $taskName = 'Mount POSM: ' . $asset->title . ' (' . ($asset->dimensions ?: 'Standard') . ')';

            // 1. Check if a task exists for this specific asset at this branch
            $existingTask = StoreTask::where('campaign_id', $campaign->id)
                ->where('branch_name', $branch)
                ->where('asset_id', $asset->id)
                ->first();

            if ($existingTask) {
                $existingTask->update([
                    'task_name' => $taskName,
                    'zone' => $taskZone,
                ]);
            } else {
                // 2. Check if there is an unlinked task for this campaign at this branch
                $unlinkedTask = StoreTask::where('campaign_id', $campaign->id)
                    ->where('branch_name', $branch)
                    ->whereNull('asset_id')
                    ->first();

                if ($unlinkedTask) {
                    $unlinkedTask->update([
                        'asset_id' => $asset->id,
                        'task_name' => $taskName,
                        'zone' => $taskZone,
                    ]);
                } else {
                    // 3. Create fresh store task
                    StoreTask::create([
                        'campaign_id' => $campaign->id,
                        'asset_id' => $asset->id,
                        'branch_name' => $branch,
                        'assigned_to_user_id' => 3, // James Wilson (OME Store Manager)
                        'task_name' => $taskName,
                        'zone' => $taskZone,
                        'due_date' => now()->addDays(2),
                        'status' => 'pending',
                    ]);
                }
            }
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
        $notes = $request->input('notes', 'Revision requested by IMC Lead: Please refine promotional copy and design mockup.');
        $user = Auth::user();
        $userName = $user?->name ?? 'Sarah Jenkins (IMC Lead)';

        $asset->update([
            'status' => 'draft',
            'approval_notes' => $notes,
        ]);

        if ($asset->brief) {
            $asset->brief->update([
                'status' => 'draft',
                'feedback_notes' => $notes,
                'submitted_at' => null,
            ]);
        }

        ActivityLog::log(
            department: 'IMC',
            actionType: 'ASSET_REJECTED',
            title: 'Returned to AI Studio: ' . $asset->title,
            description: $userName . ' returned asset "' . $asset->title . '" to AI Studio for revisions. Notes: ' . $notes,
            campaign: $asset->campaign
        );

        return back()->with('info', 'Task returned to Creative Designer in AI Studio for revision.');
    }
}
