<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use App\Models\Payment;
use App\Models\MembershipPeriod;
use Carbon\Carbon;

class SyncMemberSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:sync-subscriptions {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync member subscription statuses with their completed payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting member subscription synchronization...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Get all members
        $members = Member::all();
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($members as $member) {
            $this->line("Processing member: {$member->full_name} ({$member->member_number})");
            
            // Get the most recent completed payment for this member
            $latestPayment = Payment::where('member_id', $member->id)
                ->where('status', 'completed')
                ->orderBy('payment_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestPayment) {
                $this->line("  - No completed payments found");
                $skippedCount++;
                continue;
            }

            // Check if membership period exists for this payment
            $membershipPeriod = MembershipPeriod::where('payment_id', $latestPayment->id)->first();
            
            if (!$membershipPeriod) {
                $this->line("  - Creating missing membership period for payment {$latestPayment->id}");
                
                if (!$dryRun) {
                    $membershipPeriod = MembershipPeriod::create([
                        'member_id' => $member->id,
                        'payment_id' => $latestPayment->id,
                        'plan_type' => $latestPayment->plan_type,
                        'duration_type' => $latestPayment->duration_type,
                        'start_date' => $latestPayment->membership_start_date,
                        'expiration_date' => $latestPayment->membership_expiration_date,
                        'status' => 'active',
                        'notes' => 'Auto-created during sync',
                    ]);
                } else {
                    // In dry-run mode, create a mock membership period for comparison
                    $membershipPeriod = (object) [
                        'id' => 'NEW_ID',
                        'plan_type' => $latestPayment->plan_type,
                        'duration_type' => $latestPayment->duration_type,
                        'start_date' => $latestPayment->membership_start_date,
                        'expiration_date' => $latestPayment->membership_expiration_date,
                        'status' => 'active',
                    ];
                }
            }

            // Check if member's subscription status needs updating
            $needsUpdate = false;
            $updateData = [];

            if ($member->current_membership_period_id !== $membershipPeriod->id) {
                $updateData['current_membership_period_id'] = $membershipPeriod->id;
                $needsUpdate = true;
            }

            if ($member->membership_starts_at != $latestPayment->membership_start_date) {
                $updateData['membership_starts_at'] = $latestPayment->membership_start_date;
                $needsUpdate = true;
            }

            if ($member->membership_expires_at != $latestPayment->membership_expiration_date) {
                $updateData['membership_expires_at'] = $latestPayment->membership_expiration_date;
                $needsUpdate = true;
            }

            if ($member->current_plan_type !== $latestPayment->plan_type) {
                $updateData['current_plan_type'] = $latestPayment->plan_type;
                $needsUpdate = true;
            }

            if ($member->current_duration_type !== $latestPayment->duration_type) {
                $updateData['current_duration_type'] = $latestPayment->duration_type;
                $needsUpdate = true;
            }

            if ($member->subscription_status !== 'active') {
                $updateData['subscription_status'] = 'active';
                $needsUpdate = true;
            }

            if ($member->status !== 'active') {
                $updateData['status'] = 'active';
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $this->line("  - Updating subscription status:");
                foreach ($updateData as $field => $value) {
                    $this->line("    * {$field}: {$member->$field} -> {$value}");
                }
                
                if (!$dryRun) {
                    $member->update($updateData);
                }
                
                $updatedCount++;
            } else {
                $this->line("  - Subscription status is already up to date");
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("Synchronization complete!");
        $this->info("Members updated: {$updatedCount}");
        $this->info("Members skipped: {$skippedCount}");
        
        if ($dryRun) {
            $this->warn("This was a dry run. Run without --dry-run to apply changes.");
        }
    }
}
