<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'payment_date',
        'payment_time',
        'status',
        'plan_type',
        'duration_type',
        'membership_start_date',
        'membership_expiration_date',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_time' => 'string',
        'membership_start_date' => 'date',
        'membership_expiration_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipPeriod(): HasOne
    {
        return $this->hasOne(MembershipPeriod::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }

    /**
     * Scope a query to only include payments whose memberships expire within the next 7 days.
     */
    public function scopeExpiringThisWeek($query)
    {
        return $query->where('membership_expiration_date', '>', now())
                    ->where('membership_expiration_date', '<=', now()->addDays(7));
    }

    public function getFullPaymentDateAttribute()
    {
        if (!$this->payment_date || !$this->payment_time) {
            return null;
        }
        
        try {
            // Create a new Carbon instance from the payment_date
            $date = \Carbon\Carbon::parse($this->payment_date);
            
            // Parse the time components
            $timeComponents = explode(':', $this->payment_time);
            if (count($timeComponents) === 3) {
                $date->setTime((int)$timeComponents[0], (int)$timeComponents[1], (int)$timeComponents[2]);
            }
            
            return $date->setTimezone('Asia/Manila');
        } catch (\Exception $e) {
            return null;
        }
    }
}
