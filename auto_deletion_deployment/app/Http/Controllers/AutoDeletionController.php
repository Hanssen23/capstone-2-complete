<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AutoDeletionSettings;
use App\Models\MemberDeletionLog;
use App\Models\Member;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AutoDeletionController extends Controller
{
    /**
     * Display the auto-deletion settings page
     */
    public function index()
    {
        $settings = AutoDeletionSettings::getInstance();
        
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get recent deletion logs
        $recentLogs = MemberDeletionLog::with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.auto-deletion.index', compact('settings', 'stats', 'recentLogs'));
    }

    /**
     * Update auto-deletion settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'is_enabled' => 'boolean',
            'dry_run_mode' => 'boolean',
            'no_login_threshold_days' => 'required|integer|min:30|max:1095',
            'expired_membership_grace_days' => 'required|integer|min:7|max:365',
            'unverified_email_threshold_days' => 'required|integer|min:1|max:90',
            'inactive_status_threshold_days' => 'required|integer|min:30|max:730',
            'first_warning_days' => 'required|integer|min:1|max:90',
            'final_warning_days' => 'required|integer|min:1|max:30',
            'send_warning_emails' => 'boolean',
            'exclude_vip_members' => 'boolean',
            'exclude_members_with_payments' => 'boolean',
            'exclude_recent_activity' => 'boolean',
            'recent_activity_threshold_days' => 'required|integer|min:1|max:90',
            'schedule_time' => 'required|date_format:H:i',
        ], [
            'no_login_threshold_days.min' => 'No login threshold must be at least 30 days for safety.',
            'no_login_threshold_days.max' => 'No login threshold cannot exceed 3 years.',
            'first_warning_days.max' => 'First warning cannot be more than 90 days before deletion.',
            'final_warning_days.max' => 'Final warning cannot be more than 30 days before deletion.',
        ]);

        try {
            $settings = AutoDeletionSettings::getInstance();
            
            // Validate warning schedule logic
            if ($request->first_warning_days <= $request->final_warning_days) {
                return back()->withErrors([
                    'final_warning_days' => 'Final warning must be fewer days than first warning.'
                ])->withInput();
            }

            $settings->update($request->only([
                'is_enabled',
                'dry_run_mode',
                'no_login_threshold_days',
                'expired_membership_grace_days',
                'unverified_email_threshold_days',
                'inactive_status_threshold_days',
                'first_warning_days',
                'final_warning_days',
                'send_warning_emails',
                'exclude_vip_members',
                'exclude_members_with_payments',
                'exclude_recent_activity',
                'recent_activity_threshold_days',
                'schedule_time',
            ]));

            Log::info('Auto-deletion settings updated', [
                'admin_id' => auth()->id(),
                'settings' => $request->only([
                    'is_enabled', 'dry_run_mode', 'no_login_threshold_days'
                ])
            ]);

            return redirect()->route('admin.auto-deletion.index')
                ->with('success', 'Auto-deletion settings updated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to update auto-deletion settings', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'general' => 'Failed to update settings. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Run the deletion process manually
     */
    public function runProcess(Request $request)
    {
        $request->validate([
            'dry_run' => 'boolean',
        ]);

        try {
            $isDryRun = $request->boolean('dry_run', true);
            
            // Run the deletion command
            $exitCode = Artisan::call('members:process-inactive-deletion', [
                '--dry-run' => $isDryRun,
                '--force' => true,
            ]);

            $output = Artisan::output();

            Log::info('Manual auto-deletion process executed', [
                'admin_id' => auth()->id(),
                'dry_run' => $isDryRun,
                'exit_code' => $exitCode,
            ]);

            if ($exitCode === 0) {
                $message = $isDryRun 
                    ? 'Dry run completed successfully. Check the logs for details.'
                    : 'Deletion process completed successfully.';
                    
                return redirect()->route('admin.auto-deletion.index')
                    ->with('success', $message)
                    ->with('command_output', $output);
            } else {
                return redirect()->route('admin.auto-deletion.index')
                    ->with('error', 'Process failed. Check the logs for details.')
                    ->with('command_output', $output);
            }

        } catch (\Exception $e) {
            Log::error('Manual auto-deletion process failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.auto-deletion.index')
                ->with('error', 'Failed to run process: ' . $e->getMessage());
        }
    }

    /**
     * View deletion logs
     */
    public function logs(Request $request)
    {
        $query = MemberDeletionLog::with('admin')
            ->orderBy('created_at', 'desc');

        // Filter by deletion type
        if ($request->filled('type')) {
            $query->where('deletion_type', $request->type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(50);
        
        return view('admin.auto-deletion.logs', compact('logs'));
    }

    /**
     * Exclude a member from auto-deletion
     */
    public function excludeMember(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'exclusion_reason' => 'required|string|max:500',
        ]);

        try {
            $member = Member::findOrFail($request->member_id);
            
            $member->update([
                'exclude_from_auto_deletion' => true,
                'exclusion_reason' => $request->exclusion_reason,
            ]);

            Log::info('Member excluded from auto-deletion', [
                'admin_id' => auth()->id(),
                'member_id' => $member->id,
                'reason' => $request->exclusion_reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member excluded from auto-deletion successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to exclude member from auto-deletion', [
                'admin_id' => auth()->id(),
                'member_id' => $request->member_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to exclude member. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove exclusion from a member
     */
    public function removeExclusion(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        try {
            $member = Member::findOrFail($request->member_id);
            
            $member->update([
                'exclude_from_auto_deletion' => false,
                'exclusion_reason' => null,
            ]);

            Log::info('Member exclusion removed from auto-deletion', [
                'admin_id' => auth()->id(),
                'member_id' => $member->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member exclusion removed successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to remove member exclusion', [
                'admin_id' => auth()->id(),
                'member_id' => $request->member_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove exclusion. Please try again.'
            ], 500);
        }
    }

    /**
     * Get statistics for the dashboard
     */
    private function getStatistics(): array
    {
        $settings = AutoDeletionSettings::getInstance();
        
        // Count members eligible for deletion
        $eligibleMembers = Member::whereNull('deleted_at')
            ->where('exclude_from_auto_deletion', false)
            ->get()
            ->filter(function ($member) {
                return $member->isEligibleForAutoDeletion();
            });

        // Count members with warnings
        $membersWithFirstWarning = Member::whereNull('deleted_at')
            ->whereNotNull('deletion_warning_sent_at')
            ->whereNull('final_warning_sent_at')
            ->count();

        $membersWithFinalWarning = Member::whereNull('deleted_at')
            ->whereNotNull('final_warning_sent_at')
            ->count();

        // Count excluded members
        $excludedMembers = Member::whereNull('deleted_at')
            ->where('exclude_from_auto_deletion', true)
            ->count();

        // Count deleted members (last 30 days)
        $recentlyDeleted = MemberDeletionLog::where('deletion_type', 'auto')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return [
            'eligible_for_deletion' => $eligibleMembers->count(),
            'first_warning_sent' => $membersWithFirstWarning,
            'final_warning_sent' => $membersWithFinalWarning,
            'excluded_members' => $excludedMembers,
            'recently_deleted' => $recentlyDeleted,
            'last_run' => $settings->last_run_at,
            'last_run_stats' => [
                'processed' => $settings->last_run_processed_count,
                'deleted' => $settings->last_run_deleted_count,
                'warned' => $settings->last_run_warned_count,
            ],
        ];
    }
}
