<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MembershipPeriod extends Model
{
    protected $fillable = [
        'member_id',
        'payment_id',
        'plan_type',
        'duration_type',
        'start_date',
        'expiration_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->expiration_date > now();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date < now();
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if ($this->is_expired) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->expiration_date, false));
    }

    public function getMembershipStatusAttribute(): string
    {
        if ($this->status !== 'active') {
            return ucfirst($this->status);
        }

        if ($this->is_expired) {
            return 'Expired';
        }

        if ($this->days_until_expiration <= 7) {
            return 'Expiring Soon';
        }

        return 'Active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('expiration_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'active')
                    ->where('expiration_date', '>', now())
                    ->where('expiration_date', '<=', now()->addDays($days));
    }
}
