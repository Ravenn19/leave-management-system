<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'leave_quota'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'leave_quota' => 'integer',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function approvedLeaves()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    public function canTakeLeave($days)
    {
        $usedDays = $this->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('created_at', date('Y'))
            ->sum('total_days');

        return ($this->leave_quota - $usedDays) >= $days;
    }

    public function getRemainingQuotaAttribute()
    {
        $usedDays = $this->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('created_at', date('Y'))
            ->sum('total_days');

        return $this->leave_quota - $usedDays;
    }
}
