<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'attendance_id',
        'check_in_time',
        'check_out_time',
        'status',
        'session_duration',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    /**
     * Get the member that owns this session
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the attendance record for this session
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for active sessions with valid members only
     * Excludes: unknown cards (null member_id), inactive members, expired members
     */
    public function scopeActiveWithValidMembers($query)
    {
        return $query->where('status', 'active')
            ->whereHas('member', function($q) {
                $q->where('status', 'active')
                  ->where(function($subQ) {
                      $subQ->whereNull('membership_expires_at')
                           ->orWhere('membership_expires_at', '>=', now());
                  });
            });
    }

    /**
     * Get current session duration
     */
    public function getCurrentDurationAttribute()
    {
        if ($this->check_out_time) {
            return $this->check_in_time->diffForHumans($this->check_out_time, true);
        }
        
        return $this->check_in_time->diffForHumans(now(), true);
    }

    /**
     * Check if session is active
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && !$this->check_out_time;
    }
}
