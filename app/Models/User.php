<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'job_title',
        'branch',
        'agency_id',
        'avatar',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isImc(): bool
    {
        return $this->role === 'imc_lead';
    }

    public function isDpm(): bool
    {
        return $this->role === 'dpm_buyer';
    }

    public function isOme(): bool
    {
        return $this->role === 'ome_manager';
    }

    public function isExec(): bool
    {
        return $this->role === 'marketing_exec';
    }

    public function isDesigner(): bool
    {
        return $this->role === 'creative_designer';
    }

    public function isAgency(): bool
    {
        return $this->role === 'agency_partner';
    }

    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'imc_lead' => 'IMC Lead',
            'creative_designer' => 'Creative Designer (IMC)',
            'agency_partner' => 'External Agency Partner',
            'dpm_buyer' => 'DPM Media Buyer',
            'ome_manager' => 'OME Store Manager',
            'marketing_exec' => 'Marketing Executive',
            default => 'Staff Member',
        };
    }

    public function getRoleColorClasses(): string
    {
        return match ($this->role) {
            'imc_lead' => 'bg-purple-100 text-purple-800 border-purple-200',
            'creative_designer' => 'bg-orange-100 text-[#F7941D] border-orange-200',
            'agency_partner' => 'bg-blue-100 text-blue-800 border-blue-200',
            'dpm_buyer' => 'bg-sky-100 text-sky-800 border-sky-200',
            'ome_manager' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'marketing_exec' => 'bg-slate-900 text-white border-slate-700',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function briefs()
    {
        return $this->hasMany(Brief::class, 'created_by_user_id');
    }

    public function storeTasks()
    {
        return $this->hasMany(StoreTask::class, 'assigned_to_user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}
