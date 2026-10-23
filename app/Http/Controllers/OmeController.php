<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Campaign;
use App\Models\StoreTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OmeController extends Controller
{
    /**
     * Display the OME Store Execution & Mobile Proof View.
     * Highly mobile-responsive interface for in-store managers.
     */
    public function index(Request $request)
    {
        $currentBranch = $request->query('branch', Auth::user()?->branch ?: 'Naivas Mega City');

        $branches = [
            'Naivas Mega City',
            'Naivas Westlands',
            'Naivas Junction',
            'Naivas Kilimani',
            'Naivas Mombasa Road',
            'Naivas Karen',
        ];

        // Fetch tasks for the current branch
        $tasksQuery = StoreTask::with(['campaign', 'assignedTo', 'verifiedBy'])
            ->where('branch_name', $currentBranch)
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'in_progress' THEN 2 
                WHEN status = 'executed_pending_verification' THEN 3 
                ELSE 4 END")
            ->orderBy('due_date', 'asc');

        $tasks = $tasksQuery->get();

        // Branch execution statistics
        $totalBranchTasks = $tasks->count();
        $pendingTasks = $tasks->where('status', 'pending')->count();
        $executedPendingVerification = $tasks->where('status', 'executed_pending_verification')->count();
        $verifiedTasks = $tasks->where('status', 'verified')->count();
        $completionRate = $totalBranchTasks > 0 ? round((($executedPendingVerification + $verifiedTasks) / $totalBranchTasks) * 100, 1) : 0;

        // Active campaigns that are live or ready for launch
        $activeCampaigns = Campaign::whereIn('status', ['ready_for_launch', 'active'])->get();

        return view('dashboards.ome', [
            'currentBranch' => $currentBranch,
            'branches' => $branches,
            'tasks' => $tasks,
            'totalBranchTasks' => $totalBranchTasks,
            'pendingTasks' => $pendingTasks,
            'executedPendingVerification' => $executedPendingVerification,
            'verifiedTasks' => $verifiedTasks,
            'completionRate' => $completionRate,
            'activeCampaigns' => $activeCampaigns,
        ]);
    }

    /**
     * Submit Proof of Execution for an in-store marketing task.
     * Updates status to "executed_pending_verification", instantly visible on Executive Dashboard.
     */
    public function submitProof(Request $request, StoreTask $task)
    {
        $validated = $request->validate([
            'proof_image_url' => 'nullable|string',
            'proof_notes' => 'nullable|string|max:500',
            'preset_photo' => 'nullable|string',
        ]);

        // Preset realistic retail photos for one-click simulation
        $presetPhotos = [
            'gondola' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1000&q=80',
            'archway' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=1000&q=80',
            'standee' => 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=1000&q=80',
            'danglers' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1000&q=80',
            'tills' => 'https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=1000&q=80',
        ];

        $photo = ($validated['proof_image_url'] ?? null) 
            ?: ($presetPhotos[$validated['preset_photo'] ?? ''] ?? $presetPhotos['gondola']);

        $notes = ($validated['proof_notes'] ?? null) ?: 'Physical POSM installed securely per branch merchandising guidelines.';

        $task->update([
            'status' => 'executed_pending_verification',
            'proof_image_url' => $photo,
            'proof_notes' => $notes,
            'executed_at' => now(),
            'assigned_to_user_id' => Auth::id(),
        ]);

        $campaign = $task->campaign;

        ActivityLog::log(
            department: 'OME',
            actionType: 'PROOF_SUBMITTED',
            title: 'Store Rollout Proof: ' . $task->branch_name,
            description: Auth::user()->name . ' (Store Manager) submitted photo proof of execution for "' . $task->task_name . '" at ' . $task->branch_name . '. Status updated to "Executed - Pending Verification" for Executive review.',
            campaign: $campaign,
            metadata: [
                'task_id' => $task->id,
                'branch' => $task->branch_name,
                'task_name' => $task->task_name,
                'zone' => $task->zone,
            ]
        );

        return back()->with('success', 'Proof of execution uploaded for "' . $task->task_name . '". Task marked as "Executed - Pending Verification" and synced to Executive Dashboard.');
    }

    /**
     * Mark an executed task as officially verified (HQ sign-off).
     */
    public function verifyTask(Request $request, StoreTask $task)
    {
        $task->update([
            'status' => 'verified',
            'verified_by_user_id' => Auth::id(),
            'verified_at' => now(),
        ]);

        ActivityLog::log(
            department: 'OME',
            actionType: 'STORE_VERIFIED',
            title: 'In-Store POSM Verified: ' . $task->branch_name,
            description: Auth::user()->name . ' signed off and verified branch installation for "' . $task->task_name . '" at ' . $task->branch_name . '. Display is officially verified live.',
            campaign: $task->campaign
        );

        return back()->with('success', 'Task "' . $task->task_name . '" has been verified as live in-store.');
    }
}
