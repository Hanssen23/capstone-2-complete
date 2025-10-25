<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActiveSession;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\RfidLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        // Get current active members (currently logged in) - ONLY VALID MEMBERS
        // Excludes: unknown cards, inactive members, expired members
        $currentActiveMembersCount = ActiveSession::activeWithValidMembers()->count();

        // Get total active members (with valid memberships)
        $totalActiveMembersCount = Member::active()->count();

        // Get today's attendance - ONLY VALID MEMBERS
        // Excludes: unknown cards, inactive members
        $todayAttendance = Attendance::todayWithValidMembers()->count();

        // Get this week's attendance - ONLY VALID MEMBERS
        $thisWeekAttendance = Attendance::thisWeekWithValidMembers()->count();

        // Get this week's revenue
        $thisWeekRevenue = Payment::completed()->thisWeek()->sum('amount');

        // Get pending payments count
        $pendingPaymentsCount = Payment::pending()->count();

        // Get memberships expiring this week (from multiple sources)
        $expiringMembershipsCount = Member::expiringThisWeek()->count();

        // Get memberships expiring this week
        $expiringMembershipsThisWeek = Member::expiringThisWeek()->count();

        // Also get count from payments for verification
        $expiringPaymentsCount = Payment::expiringThisWeek()->count();

        // Get recent RFID logs
        $recentRfidLogs = RfidLog::latest('timestamp')->take(10)->get();

        // Get expired memberships today
        $expiredMembershipsToday = Member::expired()->count();

        // Get unknown cards today
        $unknownCardsToday = RfidLog::unknownCards()->today()->count();

        return view('employee.dashboard', compact(
            'currentActiveMembersCount',
            'totalActiveMembersCount',
            'todayAttendance',
            'thisWeekAttendance',
            'thisWeekRevenue',
            'pendingPaymentsCount',
            'expiringMembershipsCount',
            'expiringMembershipsThisWeek',
            'expiringPaymentsCount',
            'recentRfidLogs',
            'expiredMembershipsToday',
            'unknownCardsToday'
        ));
    }

    public function getDashboardStats()
    {
        // Get current active members (currently logged in) - ONLY VALID MEMBERS
        $currentActiveMembersCount = ActiveSession::activeWithValidMembers()->count();

        // Get total active members (with valid memberships)
        $totalActiveMembersCount = Member::active()->count();

        // Get today's attendance - ONLY VALID MEMBERS
        $todayAttendance = Attendance::todayWithValidMembers()->count();

        // Get this week's attendance - ONLY VALID MEMBERS
        $thisWeekAttendance = Attendance::thisWeekWithValidMembers()->count();

        // Get this week's revenue
        $thisWeekRevenue = Payment::completed()->thisWeek()->sum('amount');

        // Get pending payments count
        $pendingPaymentsCount = Payment::pending()->count();

        // Get memberships expiring this week
        $expiringMembershipsCount = Member::expiringThisWeek()->count();

        // Get memberships expiring this week
        $expiringMembershipsThisWeek = Member::expiringThisWeek()->count();

        // Get expired memberships today
        $expiredMembershipsToday = Member::expired()->count();

        // Get unknown cards today
        $unknownCardsToday = RfidLog::unknownCards()->today()->count();

        return response()->json([
            'currentActiveMembersCount' => $currentActiveMembersCount,
            'totalActiveMembersCount' => $totalActiveMembersCount,
            'todayAttendance' => $todayAttendance,
            'thisWeekAttendance' => $thisWeekAttendance,
            'thisWeekRevenue' => $thisWeekRevenue,
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'expiringMembershipsCount' => $expiringMembershipsCount,
            'expiringMembershipsThisWeek' => $expiringMembershipsThisWeek,
            'expiredMembershipsToday' => $expiredMembershipsToday,
            'unknownCardsToday' => $unknownCardsToday,
        ]);
    }

    /**
     * Get revenue data by period (weekly/monthly/yearly)
     */
    public function getRevenueByPeriod(Request $request)
    {
        $period = $request->input('period', 'weekly'); // weekly, monthly, yearly
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $revenue = 0;

        switch ($period) {
            case 'weekly':
                $revenue = Payment::completed()->thisWeek()->sum('amount') ?? 0;
                break;
            case 'monthly':
                $revenue = Payment::completed()->forMonth($month, $year)->sum('amount') ?? 0;
                break;
            case 'yearly':
                $revenue = Payment::completed()->forYear($year)->sum('amount') ?? 0;
                break;
            default:
                // Total revenue (all time)
                $revenue = Payment::completed()->sum('amount') ?? 0;
        }

        return response()->json([
            'period' => $period,
            'revenue' => $revenue,
            'month' => $month,
            'year' => $year,
        ]);
    }

    /**
     * Get currently active members list
     */
    public function getCurrentlyActiveMembers()
    {
        $activeMembers = ActiveSession::activeWithValidMembers()
            ->with('member')
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->member->id,
                    'name' => $session->member->first_name . ' ' . $session->member->last_name,
                    'member_number' => $session->member->member_number,
                    'check_in_time' => $session->check_in_time->format('h:i A'),
                    'duration' => $session->current_duration,
                ];
            });

        return response()->json([
            'members' => $activeMembers,
            'count' => $activeMembers->count(),
        ]);
    }

    /**
     * Get today's attendance list
     */
    public function getTodayAttendance()
    {
        $todayAttendance = Attendance::todayWithValidMembers()
            ->with('member')
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->member->id,
                    'name' => $attendance->member->first_name . ' ' . $attendance->member->last_name,
                    'member_number' => $attendance->member->member_number,
                    'check_in_time' => $attendance->check_in_time->format('h:i A'),
                    'check_out_time' => $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : 'Active',
                    'status' => $attendance->status,
                    'duration' => $attendance->calculateSessionDuration(),
                ];
            });

        return response()->json([
            'attendance' => $todayAttendance,
            'count' => $todayAttendance->count(),
        ]);
    }
}
