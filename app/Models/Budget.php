<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'channel_name',
        'channel_icon',
        'allocated_amount',
        'spent_amount',
        'impressions',
        'clicks',
        'conversions',
        'pacing_status',
        'is_deployed',
        'deployed_at',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'is_deployed' => 'boolean',
        'deployed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getSpentPercentAttribute(): float
    {
        if ($this->allocated_amount <= 0) {
            return 0.0;
        }
        return round(($this->spent_amount / $this->allocated_amount) * 100, 1);
    }

    public function getCtrAttribute(): float
    {
        if ($this->impressions <= 0) {
            return 0.0;
        }
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    public function getPacingBadgeClasses(): string
    {
        return match ($this->pacing_status) {
            'on_track' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'overpacing' => 'bg-rose-100 text-rose-800 border-rose-300',
            'underpacing' => 'bg-amber-100 text-amber-800 border-amber-300',
            'pending_deployment' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getPacingDisplayName(): string
    {
        return match ($this->pacing_status) {
            'on_track' => 'On Track',
            'overpacing' => 'Overpacing',
            'underpacing' => 'Underpacing',
            'pending_deployment' => 'Pending Launch',
            default => ucfirst(str_replace('_', ' ', $this->pacing_status)),
        };
    }
}
