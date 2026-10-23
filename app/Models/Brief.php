<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brief extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'created_by_user_id',
        'assigned_to_user_id',
        'title',
        'objective',
        'target_segment',
        'key_messaging',
        'body_copy',
        'swahili_copy',
        'ai_feedback',
        'channel',
        'status',
        'feedback_notes',
        'submitted_at',
        'approved_at',
        'approved_by_user_id',
        'sla_expires_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'sla_expires_at' => 'datetime',
        'ai_feedback' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-800 border-gray-300',
            'pending_review' => 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
