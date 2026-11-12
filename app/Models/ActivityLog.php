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

    public function getStoreTaskAttribute(): ?StoreTask
    {
        $taskId = $this->metadata['task_id'] ?? null;
        if ($taskId) {
            return StoreTask::with('campaign')->find($taskId);
        }

        $taskName = $this->metadata['task_name'] ?? $this->metadata['task'] ?? null;
        if ($taskName) {
            return StoreTask::with('campaign')
                ->where('task_name', 'like', "%{$taskName}%")
                ->first();
        }

        return null;
    }

    public function getDepartmentBadgeClasses(): string
    {
        return 'bg-orange-100 text-[#F7941D] border-orange-200';
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
