<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'department',
        'campaign_id',
        'action_type',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getDepartmentBadgeClasses(): string
    {
        return match ($this->department) {
            'IMC' => 'bg-purple-100 text-purple-800 border-purple-200',
            'DPM' => 'bg-blue-100 text-blue-800 border-blue-200',
            'OME' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'Executive' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public static function log(
        string $department,
        string $actionType,
        string $title,
        string $description,
        ?Campaign $campaign = null,
        ?User $user = null,
        ?array $metadata = null
    ): self {
        $currentUser = $user ?? auth()->user();

        return self::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'System Lead',
            'user_role' => $currentUser?->getRoleDisplayName() ?? 'Automated Pipeline',
            'department' => $department,
            'campaign_id' => $campaign?->id,
            'action_type' => $actionType,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
