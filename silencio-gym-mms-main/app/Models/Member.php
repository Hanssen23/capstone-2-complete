<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Member extends Model
{
    protected $fillable = [
        'uid',
        'member_number',
        'membership',
        'first_name',
        'last_name',
        'mobile_number',
        'email',
        'status',
        'current_membership_period_id',
        'membership_starts_at',
        'membership_expires_at',
        'current_plan_type',
        'current_duration_type',
    ];

    protected $casts = [
        'membership_starts_at' => 'date',
        'membership_expires_at' => 'date',
    ];

    public function currentMembershipPeriod(): BelongsTo
    {
        return $this->belongsTo(MembershipPeriod::class, 'current_membership_period_id');
    }

    public function membershipPeriods(): HasMany
    {
        return $this->hasMany(MembershipPeriod::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function activeSessions(): HasMany
    {
        return $this->hasMany(ActiveSession::class);
    }

    public function getIsActiveAttribute(): bool
    {
    return $this->status === 'active' && 
           ($this->membership_expires_at === null || Carbon::parse($this->membership_expires_at)->isFuture());
    }

    public function getIsExpiredAttribute(): bool
    {
    return $this->membership_expires_at !== null && Carbon::parse($this->membership_expires_at)->isPast();
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if ($this->membership_expires_at === null) {
            return -1; // No expiration
        }
        return max(0, Carbon::now()->diffInDays(Carbon::parse($this->membership_expires_at), false));
    }

    public function getMembershipStatusAttribute(): string
    {
        // Check if member has current membership period
        if ($this->currentMembershipPeriod) {
            if ($this->currentMembershipPeriod->is_expired) {
                return 'Expired';
            }

            if ($this->currentMembershipPeriod->days_until_expiration <= 7) {
                return 'Expiring Soon';
            }

            return 'Active';
        }

        // Fallback to direct membership expiration check
        if ($this->membership_expires_at) {
            $expiresAt = Carbon::parse($this->membership_expires_at);
            if ($expiresAt->isPast()) {
                return 'Expired';
            }
            if ($expiresAt->diffInDays(Carbon::now(), false) <= 7) {
                return 'Expiring Soon';
            }
            return 'Active';
        }

        return 'No Active Membership';
    }

    public function getIsMembershipActiveAttribute(): bool
    {
        return $this->currentMembershipPeriod && $this->currentMembershipPeriod->is_active;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
                    ->where('membership_expires_at', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'active')
                    ->where('membership_expires_at', '>', now())
                    ->where('membership_expires_at', '<=', now()->addDays($days));
    }

    /**
     * Scope a query to only include members whose memberships expire within the next 7 days.
     * This checks multiple sources: direct member expiration, payment expiration dates, and membership periods.
     */
    public function scopeExpiringThisWeek($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->where(function ($subQ) {
                            // Check direct member expiration
                            $subQ->where('membership_expires_at', '>', now())
                                  ->where('membership_expires_at', '<=', now()->addDays(7));
                        })->orWhereHas('payments', function ($paymentQ) {
                            // Check payment expiration dates
                            $paymentQ->where('membership_expiration_date', '>', now())
                                     ->where('membership_expiration_date', '<=', now()->addDays(7));
                        })->orWhereHas('membershipPeriods', function ($periodQ) {
                            // Check membership period expiration dates
                            $periodQ->where('expiration_date', '>', now())
                                    ->where('expiration_date', '<=', now()->addDays(7));
                        });
                    });
    }

    /**
     * Get the member's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
