<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Member;
use App\Models\MemberDeletionLog;

class MemberReactivationController extends Controller
{
    /**
     * Show the reactivation page
     */
    public function show(Request $request, Member $member)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            return view('member.reactivation.expired')->with([
                'message' => 'This reactivation link has expired. Please contact the gym for assistance.'
            ]);
        }

        // Check if member is eligible for reactivation
        if (!$this->isEligibleForReactivation($member)) {
            return view('member.reactivation.ineligible')->with([
                'member' => $member,
                'message' => 'This account is not eligible for reactivation or has already been reactivated.'
            ]);
        }

        return view('member.reactivation.form', compact('member'));
    }

    /**
     * Process the reactivation request
     */
    public function reactivate(Request $request, Member $member)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            return redirect()->route('login.show')->with('error', 'Invalid or expired reactivation link.');
        }

        // Check if member is eligible for reactivation
        if (!$this->isEligibleForReactivation($member)) {
            return redirect()->route('login.show')->with('error', 'Account is not eligible for reactivation.');
        }

        $request->validate([
            'confirm_reactivation' => 'required|accepted',
            'contact_reason' => 'nullable|string|max:500',
        ], [
            'confirm_reactivation.accepted' => 'You must confirm that you want to reactivate your account.',
        ]);

        try {
            // Reactivate the member account
            $this->performReactivation($member, $request->contact_reason);

            // Log the reactivation
            Log::info('Member account reactivated', [
                'member_id' => $member->id,
                'member_number' => $member->member_number,
                'email' => $member->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'contact_reason' => $request->contact_reason,
            ]);

            return view('member.reactivation.success', compact('member'));

        } catch (\Exception $e) {
            Log::error('Member reactivation failed', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Reactivation failed. Please try again or contact the gym for assistance.');
        }
    }

    /**
     * Check if member is eligible for reactivation
     */
    private function isEligibleForReactivation(Member $member): bool
    {
        // Member must have deletion warnings sent
        if (!$member->hasDeletionWarningSent('first')) {
            return false;
        }

        // Member must not be excluded from auto-deletion
        if ($member->exclude_from_auto_deletion) {
            return false;
        }

        // Member must not be already active (recently reactivated)
        if ($member->last_activity_at && $member->last_activity_at->isAfter(now()->subDays(7))) {
            return false;
        }

        return true;
    }

    /**
     * Perform the actual reactivation
     */
    private function performReactivation(Member $member, ?string $contactReason): void
    {
        // Update member activity timestamps
        $member->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
            'deletion_warning_sent_at' => null,
            'final_warning_sent_at' => null,
            'status' => 'active',
        ]);

        // Create a note about the reactivation
        $reactivationNote = "Account reactivated via email link on " . now()->format('Y-m-d H:i:s');
        if ($contactReason) {
            $reactivationNote .= ". Member note: " . $contactReason;
        }

        // Update or create notes field if it exists
        if ($member->notes) {
            $member->update(['notes' => $member->notes . "\n" . $reactivationNote]);
        } else {
            $member->update(['notes' => $reactivationNote]);
        }

        // Update any existing deletion logs to mark as reactivated
        MemberDeletionLog::where('member_id', $member->id)
            ->where('is_restored', false)
            ->update(['member_reactivated_before_deletion' => true]);
    }

    /**
     * Show quick reactivation page (for simple one-click reactivation)
     */
    public function quickReactivate(Request $request, Member $member)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            return redirect()->route('login.show')->with('error', 'Invalid or expired reactivation link.');
        }

        // Check if member is eligible for reactivation
        if (!$this->isEligibleForReactivation($member)) {
            return redirect()->route('login.show')->with('error', 'Account is not eligible for reactivation.');
        }

        try {
            // Perform quick reactivation
            $this->performReactivation($member, 'Quick reactivation via email link');

            // Log the reactivation
            Log::info('Member account quick reactivated', [
                'member_id' => $member->id,
                'member_number' => $member->member_number,
                'email' => $member->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return view('member.reactivation.quick-success', compact('member'));

        } catch (\Exception $e) {
            Log::error('Member quick reactivation failed', [
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login.show')->with('error', 'Reactivation failed. Please contact the gym for assistance.');
        }
    }

    /**
     * Show reactivation status page
     */
    public function status(Request $request, Member $member)
    {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            return view('member.reactivation.expired');
        }

        $isEligible = $this->isEligibleForReactivation($member);
        $warningsSent = [
            'first' => $member->deletion_warning_sent_at,
            'final' => $member->final_warning_sent_at,
        ];

        return view('member.reactivation.status', compact('member', 'isEligible', 'warningsSent'));
    }
}
