<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoDeletionSettings extends Model
{
    protected $fillable = [
        'is_enabled',
        'dry_run_mode',
        'no_login_threshold_days',
        'expired_membership_grace_days',
        'unverified_email_threshold_days',
        'inactive_status_threshold_days',
        'first_warning_days',
        'final_warning_days',
        'exclude_vip_members',
        'exclude_members_with_payments',
        'exclude_recent_activity',
        'recent_activity_threshold_days',
        'send_warning_emails',
        'warning_email_from',
        'warning_email_from_name',
        'schedule_frequency',
        'schedule_time',
        'last_updated_by_admin_id',
        'last_run_at',
        'last_run_processed_count',
        'last_run_deleted_count',
        'last_run_warned_count',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'dry_run_mode' => 'boolean',
        'exclude_vip_members' => 'boolean',
        'exclude_members_with_payments' => 'boolean',
        'exclude_recent_activity' => 'boolean',
        'send_warning_emails' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the admin who last updated these settings
     */
    public function lastUpdatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by_admin_id');
    }

    /**
     * Get singleton instance (there should only be one settings record)
     */
    public static function getInstance(): self
    {
        return self::first() ?? self::create([]);
    }

    /**
     * Update last run statistics
     */
    public function updateLastRunStats(int $processed, int $deleted, int $warned): void
    {
        $this->update([
            'last_run_at' => now(),
            'last_run_processed_count' => $processed,
            'last_run_deleted_count' => $deleted,
            'last_run_warned_count' => $warned,
        ]);
    }

    /**
     * Check if auto-deletion is enabled and not in dry run mode
     */
    public function isActivelyDeleting(): bool
    {
        return $this->is_enabled && !$this->dry_run_mode;
    }

    /**
     * Get human-readable schedule description
     */
    public function getScheduleDescription(): string
    {
        return ucfirst($this->schedule_frequency) . ' at ' . $this->schedule_time;
    }

    /**
     * Get warning schedule description
     */
    public function getWarningScheduleDescription(): string
    {
        return "First warning: {$this->first_warning_days} days before deletion, Final warning: {$this->final_warning_days} days before deletion";
    }

    /**
     * Get all threshold settings as array
     */
    public function getThresholds(): array
    {
        return [
            'no_login_threshold_days' => $this->no_login_threshold_days,
            'expired_membership_grace_days' => $this->expired_membership_grace_days,
            'unverified_email_threshold_days' => $this->unverified_email_threshold_days,
            'inactive_status_threshold_days' => $this->inactive_status_threshold_days,
        ];
    }

    /**
     * Get exclusion rules as array
     */
    public function getExclusionRules(): array
    {
        return [
            'exclude_vip_members' => $this->exclude_vip_members,
            'exclude_members_with_payments' => $this->exclude_members_with_payments,
            'exclude_recent_activity' => $this->exclude_recent_activity,
            'recent_activity_threshold_days' => $this->recent_activity_threshold_days,
        ];
    }
}
