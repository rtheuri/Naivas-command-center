<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'total_budget',
        'allocated_budget',
        'spent_budget',
        'start_date',
        'end_date',
        'target_audience',
        'agency_id',
        'agency_name',
        'sla_deadline',
        'banner_image',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sla_deadline' => 'datetime',
        'total_budget' => 'decimal:2',
        'allocated_budget' => 'decimal:2',
        'spent_budget' => 'decimal:2',
    ];

    public function briefs()
    {
        return $this->hasMany(Brief::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function storeTasks()
    {
        return $this->hasMany(StoreTask::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasApprovedAssets(): bool
    {
        return $this->assets()->where('status', 'approved')->exists();
    }

    public function getBurnRateAttribute(): float
    {
        if ($this->total_budget <= 0) {
            return 0.0;
        }
        return round(($this->spent_budget / $this->total_budget) * 100, 1);
    }

    public function getAllocatedPercentAttribute(): float
    {
        if ($this->total_budget <= 0) {
            return 0.0;
        }
        return min(100.0, round(($this->allocated_budget / $this->total_budget) * 100, 1));
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'planning' => 'bg-slate-100 text-slate-800 border-slate-300',
            'pending_approval' => 'bg-amber-100 text-amber-800 border-amber-300',
            'ready_for_launch' => 'bg-emerald-100 text-emerald-800 border-emerald-300 ring-1 ring-emerald-400',
            'active' => 'bg-emerald-600 text-white border-emerald-700 shadow-sm',
            'completed' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'planning' => 'Planning',
            'pending_approval' => 'Pending Review',
            'ready_for_launch' => 'Ready for Launch',
            'active' => 'Active In Market',
            'completed' => 'Completed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function recalculateBudgets(): void
    {
        $allocated = $this->budgets()->sum('allocated_amount');
        $spent = $this->budgets()->sum('spent_amount');

        $this->update([
            'allocated_budget' => $allocated,
            'spent_budget' => $spent,
        ]);
    }
}
