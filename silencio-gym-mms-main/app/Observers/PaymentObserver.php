<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\MembershipPeriod;
use App\Models\Member;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // If payment is created as completed, activate membership
        if ($payment->status === 'completed') {
            $this->activateMembership($payment);
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Check if status changed to completed
        if ($payment->wasChanged('status') && $payment->status === 'completed') {
            $this->activateMembership($payment);
        }
        
        // Check if status changed to failed or cancelled
        if ($payment->wasChanged('status') && in_array($payment->status, ['failed', 'cancelled'])) {
            $this->deactivateMembership($payment);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        // If payment is deleted, deactivate the membership
        $this->deactivateMembership($payment);
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }

    /**
     * Activate membership for a completed payment
     */
    private function activateMembership(Payment $payment): void
    {
        try {
            // Find or create membership period
            $membershipPeriod = MembershipPeriod::where('payment_id', $payment->id)->first();
            
            if (!$membershipPeriod) {
                $membershipPeriod = MembershipPeriod::create([
                    'member_id' => $payment->member_id,
                    'payment_id' => $payment->id,
                    'plan_type' => $payment->plan_type,
                    'duration_type' => $payment->duration_type,
                    'start_date' => $payment->membership_start_date,
                    'expiration_date' => $payment->membership_expiration_date,
                    'status' => 'active',
                    'notes' => 'Auto-created by PaymentObserver',
                ]);
            } else {
                $membershipPeriod->update(['status' => 'active']);
            }
            
            // Update member's current membership
            $member = $payment->member;
            if ($member) {
                $member->update([
                    'current_membership_period_id' => $membershipPeriod->id,
                    'membership_starts_at' => $payment->membership_start_date,
                    'membership_expires_at' => $payment->membership_expiration_date,
                    'current_plan_type' => $payment->plan_type,
                    'current_duration_type' => $payment->duration_type,
                    'membership' => $payment->plan_type, // Set membership type automatically
                    'subscription_status' => 'active',
                    'status' => 'active',
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('PaymentObserver: Failed to activate membership', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Deactivate membership for a failed/cancelled payment
     */
    private function deactivateMembership(Payment $payment): void
    {
        try {
            // Find membership period and mark as cancelled
            $membershipPeriod = MembershipPeriod::where('payment_id', $payment->id)->first();
            
            if ($membershipPeriod) {
                $membershipPeriod->update(['status' => 'cancelled']);
                
                // Clear member's current membership if this was their active one
                $member = $payment->member;
                if ($member && $member->current_membership_period_id === $membershipPeriod->id) {
                    $member->update([
                        'current_membership_period_id' => null,
                        'membership_starts_at' => null,
                        'membership_expires_at' => null,
                        'current_plan_type' => null,
                        'current_duration_type' => null,
                        'membership' => null, // Clear membership type when cancelled
                        'subscription_status' => 'cancelled',
                        'status' => 'inactive',
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('PaymentObserver: Failed to deactivate membership', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
