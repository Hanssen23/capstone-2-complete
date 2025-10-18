<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'member_id',
        'check_in_time',
        'check_out_time',
        'status',
        'session_duration',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function activeSession(): BelongsTo
    {
        return $this->belongsTo(ActiveSession::class);
    }

    public function calculateSessionDuration(): string
    {
        if (!$this->check_out_time) {
            return 'Active Session';
        }

        $duration = $this->check_in_time->diffInSeconds($this->check_out_time);
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in_time', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('check_in_time', now()->month);
    }
}
