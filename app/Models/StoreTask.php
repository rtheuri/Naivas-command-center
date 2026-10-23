<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'branch_name',
        'assigned_to_user_id',
        'task_name',
        'zone',
        'due_date',
        'status',
        'proof_image_url',
        'proof_notes',
        'executed_at',
        'verified_by_user_id',
        'verified_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'executed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
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
            'verified' => 'Verified & Live In-Store',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
