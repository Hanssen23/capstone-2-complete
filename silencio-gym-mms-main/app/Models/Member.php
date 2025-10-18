<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Member extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
    use Notifiable, CanResetPassword;
    protected $fillable = [
        'uid',
        'member_number',
        'membership',
        'subscription_status',
        'first_name',
        'middle_name',
        'last_name',
        'age',
        'gender',
        'mobile_number',
        'email',
        'email_verified_at',
        'password',
        'status',
        'role',
        'current_membership_period_id',
        'membership_starts_at',
        'membership_expires_at',
        'accepted_terms_at',
        'current_plan_type',
        'current_duration_type',
        'last_login_at',
        'last_activity_at',
        'exclude_from_auto_deletion',
        'exclusion_reason',
    ];

    protected $casts = [
        'membership_starts_at' => 'datetime',
        'membership_expires_at' => 'datetime',
        'accepted_terms_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'exclude_from_auto_deletion' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Append full_name to JSON serialization
     */
    protected $appends = ['full_name'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        // Return UID to pool when member is deleted
        static::deleting(function ($member) {
            if ($member->uid) {
                \App\Models\UidPool::returnUid($member->uid);
            }
        });
    }

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

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' &&
               ($this->membership_expires_at === null || $this->membership_expires_at->isFuture());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->membership_expires_at !== null && $this->membership_expires_at->isPast();
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        $name .= ' ' . $this->last_name;
        return trim($name);
    }

    /**
     * Generate the next sequential member number
     */
    public static function generateMemberNumber(): string
    {
        // Get the highest existing member number
        $lastMember = self::where('member_number', 'like', 'MEM%')
            ->orderByRaw('CAST(SUBSTRING(member_number, 4) AS UNSIGNED) DESC')
            ->first();

        if ($lastMember) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastMember->member_number, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from MEM001 if no members exist
            $nextNumber = 1;
        }

        return 'MEM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get an available UID from the pool
     */
    public static function getAvailableUid(): ?string
    {
        return \App\Models\UidPool::getAvailableUid();
    }

    /**
     * Return a UID to the pool
     */
    public static function returnUidToPool(string $uid): bool
    {
        return \App\Models\UidPool::returnUid($uid);
    }

    /**
     * Check if member has an active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' &&
               $this->current_plan_type !== null &&
               ($this->membership_expires_at === null || $this->membership_expires_at->isFuture());
    }

    /**
     * Check if member is subscribed (has any plan)
     */
    public function isSubscribed(): bool
    {
        return $this->subscription_status !== 'not_subscribed' && 
               $this->current_plan_type !== null;
    }

    /**
     * Get display name for subscription status
     */
    public function getSubscriptionStatusDisplayAttribute(): string
    {
        if (!$this->isSubscribed()) {
            return 'Not Subscribed';
        }

        return match($this->subscription_status) {
            'pending' => 'Pending Payment',
            'active' => 'Active',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
            default => 'Not Subscribed'
        };
    }

    /**
     * Get display name for plan type
     */
    public function getPlanTypeDisplayAttribute(): string
    {
        if (!$this->isSubscribed()) {
            return 'Not Subscribed';
        }

        return ucfirst($this->current_plan_type ?? 'Not Subscribed');
    }

    /**
     * Get display name for membership (legacy field)
     */
    public function getMembershipDisplayAttribute(): string
    {
        // If member has an active subscription, show the plan type
        if ($this->hasActiveSubscription()) {
            return ucfirst($this->current_plan_type);
        }
        
        // Otherwise show the basic membership field or "Not Subscribed"
        return $this->membership ? ucfirst($this->membership) : 'Not Subscribed';
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if ($this->membership_expires_at === null) {
            return -1; // No expiration
        }
        
        $expiresAt = Carbon::parse($this->membership_expires_at);
        $now = Carbon::now();
        
        // If membership is expired, return 0
        if ($expiresAt->isPast()) {
            return 0;
        }
        
        // Calculate days remaining (positive number)
        $daysLeft = $now->diffInDays($expiresAt, false);
        
        return max(0, (int) $daysLeft);
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

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\MemberEmailVerification);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MemberPasswordReset($token));
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get the email address that should be used for password reset.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Determine if the user has verified their email address.
     */
    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
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
     * Check if the member has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the member has member role
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Check if member is currently in the gym (has active RFID session)
     */
    public function isInGym(): bool
    {
        $hasActiveSession = $this->activeSessions()
            ->where('status', 'active')
            ->whereNull('check_out_time')
            ->exists();
            
        // Log consistency check
        if ($hasActiveSession) {
            Log::info('Member marked as in gym', [
                'member_id' => $this->id,
                'member_number' => $this->member_number,
                'method' => 'isInGym'
            ]);
        }
        
        return $hasActiveSession;
    }

    /**
     * Get current gym session if member is in gym
     */
    public function getCurrentGymSession()
    {
        return $this->activeSessions()
            ->where('status', 'active')
            ->whereNull('check_out_time')
            ->first();
    }

    /**
     * Get gym presence status
     */
    public function getGymPresenceStatusAttribute(): string
    {
        return $this->isInGym() ? 'In Gym' : 'Not in Gym';
    }

    /**
     * Get online status based on RFID check-in, not web session
     */
    public function getOnlineStatusAttribute(): string
    {
        return $this->isInGym() ? 'Online' : 'Offline';
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Check if member is eligible for auto-deletion
     */
    public function isEligibleForAutoDeletion(): bool
    {
        // Skip if excluded from auto-deletion
        if ($this->exclude_from_auto_deletion) {
            return false;
        }

        // Note: Hard delete is now used, so no need to check for soft delete

        return true;
    }

    /**
     * Get days since last login
     */
    public function getDaysSinceLastLogin(): ?int
    {
        if (!$this->last_login_at) {
            return null;
        }

        return $this->last_login_at->diffInDays(now());
    }

    /**
     * Get days since last activity
     */
    public function getDaysSinceLastActivity(): ?int
    {
        if (!$this->last_activity_at) {
            return null;
        }

        return $this->last_activity_at->diffInDays(now());
    }

    /**
     * Get days since membership expired
     */
    public function getDaysSinceMembershipExpired(): ?int
    {
        if (!$this->membership_expires_at || $this->membership_expires_at->isFuture()) {
            return null;
        }

        return $this->membership_expires_at->diffInDays(now());
    }

    /**
     * Get days since account creation (for unverified emails)
     */
    public function getDaysSinceCreation(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if member has recent RFID activity
     */
    public function hasRecentRfidActivity(int $days = 30): bool
    {
        return $this->rfidLogs()
            ->where('timestamp', '>=', now()->subDays($days))
            ->exists();
    }



    /**
     * Check if member has outstanding payments
     */
    public function hasOutstandingPayments(): bool
    {
        return $this->payments()
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Mark member for deletion warning
     */
    public function markForDeletionWarning(string $warningType = 'first'): void
    {
        $field = $warningType === 'final' ? 'final_warning_sent_at' : 'deletion_warning_sent_at';
        $this->update([$field => now()]);
    }

    /**
     * Check if deletion warning was sent
     */
    public function hasDeletionWarningSent(string $warningType = 'first'): bool
    {
        $field = $warningType === 'final' ? 'final_warning_sent_at' : 'deletion_warning_sent_at';
        return !is_null($this->$field);
    }

    /**
     * Get deletion eligibility reasons
     */
    public function getDeletionEligibilityReasons(): array
    {
        $reasons = [];
        $settings = \App\Models\AutoDeletionSettings::first();

        if (!$settings || !$settings->is_enabled) {
            return $reasons;
        }

        // Check no login threshold
        $daysSinceLogin = $this->getDaysSinceLastLogin();
        if ($daysSinceLogin && $daysSinceLogin >= $settings->no_login_threshold_days) {
            $reasons[] = "No login for {$daysSinceLogin} days (threshold: {$settings->no_login_threshold_days})";
        }

        // Check expired membership
        $daysSinceExpired = $this->getDaysSinceMembershipExpired();
        if ($daysSinceExpired && $daysSinceExpired >= $settings->expired_membership_grace_days) {
            $reasons[] = "Membership expired {$daysSinceExpired} days ago (grace period: {$settings->expired_membership_grace_days})";
        }

        // Check unverified email
        if (!$this->hasVerifiedEmail()) {
            $daysSinceCreation = $this->getDaysSinceCreation();
            if ($daysSinceCreation >= $settings->unverified_email_threshold_days) {
                $reasons[] = "Email unverified for {$daysSinceCreation} days (threshold: {$settings->unverified_email_threshold_days})";
            }
        }

        // Check inactive status
        if ($this->status === 'inactive') {
            $daysSinceLastActivity = $this->getDaysSinceLastActivity();
            if ($daysSinceLastActivity && $daysSinceLastActivity >= $settings->inactive_status_threshold_days) {
                $reasons[] = "Inactive status for {$daysSinceLastActivity} days (threshold: {$settings->inactive_status_threshold_days})";
            }
        }

        return $reasons;
    }
}
