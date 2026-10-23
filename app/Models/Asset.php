<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'brief_id',
        'title',
        'asset_type',
        'file_url',
        'dimensions',
        'format',
        'version',
        'status',
        'approval_notes',
        'approved_at',
        'approved_by_user_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'version' => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function brief()
    {
        return $this->belongsTo(Brief::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'draft' => 'bg-gray-100 text-gray-700 border-gray-300',
            'pending_review' => 'bg-amber-100 text-amber-800 border-amber-300 font-medium',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-semibold',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getAssetTypeLabel(): string
    {
        return match ($this->asset_type) {
            'posm_banner' => 'POSM Banner',
            'gondola_header' => 'Gondola Header',
            'digital_graphic' => 'Digital Social Graphic',
            'video_reel' => 'TikTok / IG Reel',
            'price_dangler' => 'Aisle Price Dangler',
            'shelf_talker' => 'Shelf Talker Decal',
            default => ucwords(str_replace('_', ' ', $this->asset_type)),
        };
    }
}
