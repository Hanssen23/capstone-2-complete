<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDeletionLog extends Model
{
    protected $fillable = [
        'member_id',
        'member_number',
        'member_name',
        'member_email',
        'member_status',
        'deletion_type',
        'deletion_reason',
        'deletion_criteria',
        'deleted_by_admin_id',
        'deleted_by_admin_name',
        'member_last_login_at',
        'member_last_activity_at',
        'membership_expired_at',
        'email_verified_at',
        'first_warning_sent_at',
        'final_warning_sent_at',
        'member_reactivated_before_deletion',
        'is_restored',
        'restored_at',
        'restored_by_admin_id',
        'restoration_reason',
    ];

    protected $casts = [
        'deletion_criteria' => 'array',
        'member_last_login_at' => 'datetime',
        'member_last_activity_at' => 'datetime',
        'membership_expired_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'first_warning_sent_at' => 'datetime',
        'final_warning_sent_at' => 'datetime',
        'member_reactivated_before_deletion' => 'boolean',
        'is_restored' => 'boolean',
        'restored_at' => 'datetime',
    ];

    /**
     * Get the admin who deleted this member
     */
    public function deletedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_admin_id');
    }

    /**
     * Get the admin who restored this member
     */
    public function restoredByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by_admin_id');
    }

    /**
     * Create a deletion log entry from a member
     */
    public static function createFromMember(Member $member, string $deletionType, string $reason, array $criteria = [], ?User $admin = null): self
    {
        return self::create([
            'member_id' => $member->id,
            'member_number' => $member->member_number,
            'member_name' => $member->full_name,
            'member_email' => $member->email,
            'member_status' => $member->status,
            'deletion_type' => $deletionType,
            'deletion_reason' => $reason,
            'deletion_criteria' => $criteria,
            'deleted_by_admin_id' => $admin?->id,
            'deleted_by_admin_name' => $admin?->name,
            'member_last_login_at' => $member->last_login_at,
            'member_last_activity_at' => $member->last_activity_at,
            'membership_expired_at' => $member->membership_expires_at,
            'email_verified_at' => $member->email_verified_at,
            'first_warning_sent_at' => $member->deletion_warning_sent_at,
            'final_warning_sent_at' => $member->final_warning_sent_at,
        ]);
    }

    /**
     * Mark as restored
     */
    public function markAsRestored(User $admin, string $reason): void
    {
        $this->update([
            'is_restored' => true,
            'restored_at' => now(),
            'restored_by_admin_id' => $admin->id,
            'restoration_reason' => $reason,
        ]);
    }

    /**
     * Get deletion type badge class for UI
     */
    public function getDeletionTypeBadgeClass(): string
    {
        return match($this->deletion_type) {
            'auto' => 'badge-warning',
            'manual' => 'badge-danger',
            'admin' => 'badge-info',
            default => 'badge-secondary',
        };
    }

    /**
     * Get human-readable deletion reason
     */
    public function getFormattedDeletionReason(): string
    {
        if ($this->deletion_type === 'auto' && is_array($this->deletion_criteria)) {
            return implode('; ', $this->deletion_criteria);
        }

        return $this->deletion_reason;
    }

    /**
     * Scope for auto-deletions
     */
    public function scopeAutoDeletions($query)
    {
        return $query->where('deletion_type', 'auto');
    }

    /**
     * Scope for manual deletions
     */
    public function scopeManualDeletions($query)
    {
        return $query->where('deletion_type', 'manual');
    }

    /**
     * Scope for restored members
     */
    public function scopeRestored($query)
    {
        return $query->where('is_restored', true);
    }

    /**
     * Scope for recent deletions
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
