<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetReallocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_budget_id',
        'to_budget_id',
        'amount',
        'reason',
        'status',
        'requested_by_user_id',
        'requested_by_name',
        'approved_by_user_id',
        'rejection_reason',
        'responded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'responded_at' => 'datetime',
    ];

    public function fromBudget()
    {
        return $this->belongsTo(Budget::class, 'from_budget_id');
    }

    public function toBudget()
    {
        return $this->belongsTo(Budget::class, 'to_budget_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'pending_cmo_approval' => 'bg-amber-100 text-amber-800 border-amber-300',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending_cmo_approval' => 'Pending CMO Review',
            'approved' => 'Approved by CMO',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
