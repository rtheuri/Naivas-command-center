<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'asset_id',
        'branch_name',
        'assigned_to_user_id',
        'task_name',
        'zone',
        'due_date',
        'status',
        'proof_image_url',
        'proof_notes',
        'redo_instructions',
        'redo_requested_at',
        'redo_requested_by_user_id',
        'executed_at',
        'verified_by_user_id',
        'verified_at',
    ];

    protected $appends = [
        'approved_asset',
    ];

    protected $casts = [
        'due_date' => 'date',
        'executed_at' => 'datetime',
        'verified_at' => 'datetime',
        'redo_requested_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function getApprovedAssetAttribute()
    {
        if ($this->asset_id && $this->relationLoaded('asset') && $this->asset) {
            return $this->asset;
        }

        if ($this->asset_id) {
            $found = Asset::find($this->asset_id);
            if ($found) {
                return $found;
            }
        }

        if (!$this->campaign) {
            return null;
        }

        // Return the first approved asset in this campaign
        if ($this->relationLoaded('campaign') && $this->campaign->relationLoaded('assets')) {
            return $this->campaign->assets->firstWhere('status', 'approved')
                ?? $this->campaign->assets->first();
        }

        return $this->campaign->assets()->where('status', 'approved')->first()
            ?? $this->campaign->assets()->first();
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function redoRequestedBy()
    {
        return $this->belongsTo(User::class, 'redo_requested_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRedoRequested(): bool
    {
        return $this->status === 'redo_requested';
    }

    public function isExecuted(): bool
    {
        return in_array($this->status, ['executed_pending_verification', 'verified']);
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-800 border-amber-300 ring-1 ring-amber-200',
            'in_progress' => 'bg-blue-50 text-blue-800 border-blue-300',
            'executed_pending_verification' => 'bg-purple-100 text-purple-800 border-purple-300 ring-1 ring-purple-300 animate-pulse',
            'redo_requested' => 'bg-rose-50 text-rose-700 border-rose-300 ring-1 ring-rose-200 font-bold',
            'verified' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Rollout',
            'in_progress' => 'In Progress',
            'executed_pending_verification' => 'Executed - Pending Verification',
            'redo_requested' => 'Redo Requested (Insufficient Proof)',
            'verified' => 'Verified & Live In-Store',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
