// app/Models/LeaveRequest.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'total_days',
        'reason', 'attachment', 'status', 'approved_by',
        'approved_at', 'rejection_reason'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected static function booted()
    {
        static::creating(function ($leaveRequest) {
            $start = new \DateTime($leaveRequest->start_date);
            $end = new \DateTime($leaveRequest->end_date);
            $interval = $start->diff($end);
            $leaveRequest->total_days = $interval->days + 1;
        });
    }
}
