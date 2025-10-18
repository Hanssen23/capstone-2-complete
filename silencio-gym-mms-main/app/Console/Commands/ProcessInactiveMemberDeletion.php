<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\AutoDeletionSettings;
use App\Models\MemberDeletionLog;
use App\Notifications\MemberDeletionWarning;
use App\Notifications\MemberFinalDeletionWarning;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessInactiveMemberDeletion extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'members:process-inactive-deletion
                            {--dry-run : Run in dry-run mode without actually deleting}
                            {--force : Force run even if disabled in settings}';

    /**
     * The console command description.
     */
    protected $description = 'Process inactive member accounts for deletion with warning system';

    /**
     * Statistics for the current run
     */
    private array $stats = [
        'processed' => 0,
        'eligible_for_deletion' => 0,
        'deleted' => 0,
        'first_warnings_sent' => 0,
        'final_warnings_sent' => 0,
        'excluded' => 0,
        'errors' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Starting inactive member deletion process...');
        
        // Get settings
        $settings = AutoDeletionSettings::getInstance();
        
        // Check if enabled
        if (!$settings->is_enabled && !$this->option('force')) {
            $this->warn('❌ Auto-deletion is disabled in settings. Use --force to override.');
            return Command::FAILURE;
        }

        // Determine if this is a dry run
        $isDryRun = $this->option('dry-run') || $settings->dry_run_mode;
        
        if ($isDryRun) {
            $this->warn('🧪 Running in DRY RUN mode - no actual deletions will occur');
        }

        $this->info("📊 Processing with settings:");
        $this->table(['Setting', 'Value'], [
            ['No login threshold', $settings->no_login_threshold_days . ' days'],
            ['Expired membership grace', $settings->expired_membership_grace_days . ' days'],
            ['Unverified email threshold', $settings->unverified_email_threshold_days . ' days'],
            ['Inactive status threshold', $settings->inactive_status_threshold_days . ' days'],
            ['First warning', $settings->first_warning_days . ' days before deletion'],
            ['Final warning', $settings->final_warning_days . ' days before deletion'],
        ]);

        try {
            // Process members
            $this->processMembers($settings, $isDryRun);
            
            // Update settings with run statistics
            if (!$isDryRun) {
                $settings->updateLastRunStats(
                    $this->stats['processed'],
                    $this->stats['deleted'],
                    $this->stats['first_warnings_sent'] + $this->stats['final_warnings_sent']
                );
            }

            // Display results
            $this->displayResults($isDryRun);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('❌ Error during processing: ' . $e->getMessage());
            Log::error('Member deletion process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Process all members for deletion eligibility
     */
    private function processMembers(AutoDeletionSettings $settings, bool $isDryRun): void
    {
        $this->info('🔍 Scanning for inactive members...');
        
        // Get all active members (not soft deleted)
        $members = Member::whereNull('deleted_at')
            ->where('exclude_from_auto_deletion', false)
            ->get();

        $progressBar = $this->output->createProgressBar($members->count());
        $progressBar->start();

        foreach ($members as $member) {
            $this->stats['processed']++;
            
            try {
                $this->processMember($member, $settings, $isDryRun);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error('Error processing member', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage()
                ]);
                
                if ($this->getOutput()->isVerbose()) {
                    $this->error("Error processing member {$member->id}: " . $e->getMessage());
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Process individual member
     */
    private function processMember(Member $member, AutoDeletionSettings $settings, bool $isDryRun): void
    {
        // Check if member is eligible for deletion
        if (!$member->isEligibleForAutoDeletion()) {
            $this->stats['excluded']++;
            return;
        }

        // Apply exclusion rules
        if ($this->shouldExcludeMember($member, $settings)) {
            $this->stats['excluded']++;
            return;
        }

        // Get deletion reasons
        $reasons = $member->getDeletionEligibilityReasons();
        
        if (empty($reasons)) {
            return; // Not eligible for deletion
        }

        $this->stats['eligible_for_deletion']++;
        
        if ($this->getOutput()->isVerbose()) {
            $this->line("📋 Member {$member->member_number} ({$member->full_name}) eligible: " . implode(', ', $reasons));
        }

        // Check if we should send warnings or delete
        $daysSinceFirstWarning = $member->deletion_warning_sent_at 
            ? $member->deletion_warning_sent_at->diffInDays(now()) 
            : null;
            
        $daysSinceFinalWarning = $member->final_warning_sent_at 
            ? $member->final_warning_sent_at->diffInDays(now()) 
            : null;

        // Determine action based on warning schedule
        if (!$member->hasDeletionWarningSent('first')) {
            // Send first warning
            $this->sendFirstWarning($member, $settings, $isDryRun);
        } elseif ($daysSinceFirstWarning >= ($settings->first_warning_days - $settings->final_warning_days) 
                  && !$member->hasDeletionWarningSent('final')) {
            // Send final warning
            $this->sendFinalWarning($member, $settings, $isDryRun);
        } elseif ($daysSinceFinalWarning >= $settings->final_warning_days) {
            // Delete member
            $this->deleteMember($member, $reasons, $isDryRun);
        }
    }

    /**
     * Check if member should be excluded from deletion
     */
    private function shouldExcludeMember(Member $member, AutoDeletionSettings $settings): bool
    {
        // Exclude VIP members
        if ($settings->exclude_vip_members && $member->membership === 'vip') {
            return true;
        }

        // Exclude members with outstanding payments
        if ($settings->exclude_members_with_payments && $member->hasOutstandingPayments()) {
            return true;
        }

        // Exclude members with recent RFID activity
        if ($settings->exclude_recent_activity && 
            $member->hasRecentRfidActivity($settings->recent_activity_threshold_days)) {
            return true;
        }

        return false;
    }

    /**
     * Send first deletion warning
     */
    private function sendFirstWarning(Member $member, AutoDeletionSettings $settings, bool $isDryRun): void
    {
        if ($this->getOutput()->isVerbose()) {
            $this->line("📧 Sending first warning to {$member->email}");
        }

        if (!$isDryRun && $settings->send_warning_emails) {
            $member->notify(new MemberDeletionWarning($settings->first_warning_days));
        }

        if (!$isDryRun) {
            $member->markForDeletionWarning('first');
        }

        $this->stats['first_warnings_sent']++;
    }

    /**
     * Send final deletion warning
     */
    private function sendFinalWarning(Member $member, AutoDeletionSettings $settings, bool $isDryRun): void
    {
        if ($this->getOutput()->isVerbose()) {
            $this->line("⚠️ Sending final warning to {$member->email}");
        }

        if (!$isDryRun && $settings->send_warning_emails) {
            $member->notify(new MemberFinalDeletionWarning($settings->final_warning_days));
        }

        if (!$isDryRun) {
            $member->markForDeletionWarning('final');
        }

        $this->stats['final_warnings_sent']++;
    }

    /**
     * Delete member account
     */
    private function deleteMember(Member $member, array $reasons, bool $isDryRun): void
    {
        if ($this->getOutput()->isVerbose()) {
            $this->line("🗑️ Deleting member {$member->member_number} ({$member->full_name})");
        }

        if (!$isDryRun) {
            // Create deletion log
            MemberDeletionLog::createFromMember(
                $member,
                'auto',
                'Automatic hard deletion due to inactivity',
                $reasons
            );

            // Hard delete the member - this will completely remove the record
            // Related data will be handled by database foreign key constraints
            $member->delete();
        }

        $this->stats['deleted']++;
    }

    /**
     * Display processing results
     */
    private function displayResults(bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📊 Processing Results:');
        
        $this->table(['Metric', 'Count'], [
            ['Members processed', $this->stats['processed']],
            ['Eligible for deletion', $this->stats['eligible_for_deletion']],
            ['Excluded from deletion', $this->stats['excluded']],
            ['First warnings sent', $this->stats['first_warnings_sent']],
            ['Final warnings sent', $this->stats['final_warnings_sent']],
            ['Members deleted', $this->stats['deleted'] . ($isDryRun ? ' (simulated)' : '')],
            ['Errors encountered', $this->stats['errors']],
        ]);

        if ($isDryRun) {
            $this->warn('🧪 This was a dry run - no actual changes were made');
        } else {
            $this->info('✅ Processing completed successfully');
        }
    }
}
