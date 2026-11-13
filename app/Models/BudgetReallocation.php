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
        'cmo_notes',
        'responded_at',
        'executed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'responded_at' => 'datetime',
        'executed_at' => 'datetime',
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

    public function isPending(): bool
    {
        return $this->status === 'pending_cmo_approval';
    }

    public function isApprovedByCmo(): bool
    {
        return in_array($this->status, ['approved_by_cmo', 'approved']);
    }

    public function isExecuted(): bool
    {
        return $this->status === 'executed';
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['rejected_by_cmo', 'rejected']);
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'pending_cmo_approval' => 'bg-amber-100 text-amber-800 border-amber-300 ring-1 ring-amber-200 animate-pulse',
            'approved_by_cmo', 'approved' => 'bg-blue-100 text-blue-800 border-blue-300 ring-1 ring-blue-200 font-semibold',
            'executed' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
            'rejected_by_cmo', 'rejected' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending_cmo_approval' => 'Pending CMO Review',
            'approved_by_cmo', 'approved' => 'Authorized by CMO (Ready to Apply)',
            'executed' => 'Executed & Shift Applied',
            'rejected_by_cmo', 'rejected' => 'Declined by CMO',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
