<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActiveSession;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\RfidLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current user role for data filtering (handle case when user is not authenticated)
        $user = auth()->check() ? auth()->user() : null;
        $isEmployee = $user && $user->role === 'employee';
        
        // Cache key with current hour and role to refresh hourly
        $cacheKey = 'dashboard_data_' . ($isEmployee ? 'employee_' : 'admin_') . Carbon::now()->format('Y-m-d_H');
        
        // Get dashboard data from cache or compute it
        $dashboardData = Cache::remember($cacheKey, 3600, function () use ($isEmployee) {
            // Get counts using Eloquent for better filtering
            // ONLY VALID MEMBERS - Excludes: unknown cards, inactive members, expired members
            $currentActiveMembersCount = ActiveSession::activeWithValidMembers()->count();
            $totalMembersCount = Member::count();
            $activeMembersCount = Member::active()->count();
            $todayAttendanceCount = Attendance::todayWithValidMembers()->count();
            $weekAttendanceCount = Attendance::thisWeekWithValidMembers()->count();
            $pendingPaymentsCount = Payment::pending()->count();
            $expiringMembershipsCount = Member::where('status', 'active')
                ->where('membership_expires_at', '<=', now()->addDays(7))
                ->count();
            $expiredMembershipsCount = Member::expired()->count();
            $unknownCardsCount = RfidLog::whereDate('timestamp', today())
                ->whereNotIn('card_uid', Member::pluck('uid'))
                ->count();

            // Get this week's revenue with proper formatting
            try {
                $thisWeekRevenue = Payment::completed()
                    ->thisWeek()
                    ->sum('amount') ?? 0;
            } catch (\Exception $e) {
                $thisWeekRevenue = 0;
            }
            
            // Get this month's revenue
            $thisMonthRevenue = Payment::completed()
                ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount');

            // Get expiring memberships this week
            $expiringMembershipsThisWeek = \App\Models\Member::where('status', 'active')
                ->where(function($query) {
                    $query->where('membership_expires_at', '>', now())
                          ->where('membership_expires_at', '<=', now()->addWeek())
                          ->orWhereHas('payments', function($q) {
                              $q->where('membership_expiration_date', '>', now())
                                ->where('membership_expiration_date', '<=', now()->addWeek());
                          })
                          ->orWhereHas('membershipPeriods', function($q) {
                              $q->where('expiration_date', '>', now())
                                ->where('expiration_date', '<=', now()->addWeek());
                          });
                })->count();
            
            // Get expiring memberships this month
            $expiringMembershipsThisMonth = \App\Models\Member::where('status', 'active')
                ->where(function($query) {
                    $query->where('membership_expires_at', '>', now())
                          ->where('membership_expires_at', '<=', now()->endOfMonth())
                          ->orWhereHas('payments', function($q) {
                              $q->where('membership_expiration_date', '>', now())
                                ->where('membership_expiration_date', '<=', now()->endOfMonth());
                          })
                          ->orWhereHas('membershipPeriods', function($q) {
                              $q->where('expiration_date', '>', now())
                                ->where('expiration_date', '<=', now()->endOfMonth());
                          });
                })->count();

            // Get recent RFID logs with member info in a single query
            $recentRfidLogs = RfidLog::select('rfid_logs.*', 'members.first_name', 'members.last_name')
                ->leftJoin('members', 'rfid_logs.card_uid', '=', 'members.uid')
                ->latest('timestamp')
                ->take(10)
                ->get();

            // Get active members with their membership info
            $members = Member::select('members.*', 'membership_periods.plan_type', 'membership_periods.expiration_date')
                ->join('membership_periods', 'members.current_membership_period_id', '=', 'membership_periods.id')
                ->where('members.status', 'active')
                ->whereNotNull('members.membership_expires_at')
                ->get();

            return [
                'currentActiveMembersCount' => $currentActiveMembersCount,
                'totalMembersCount' => $totalMembersCount,
                'totalActiveMembersCount' => $activeMembersCount,
                'todayAttendance' => $todayAttendanceCount,
                'thisWeekAttendance' => $weekAttendanceCount,
                'thisWeekRevenue' => $thisWeekRevenue,
                'thisMonthRevenue' => $thisMonthRevenue,
                'pendingPaymentsCount' => $pendingPaymentsCount,
                'expiringMembershipsCount' => $expiringMembershipsCount,
                'expiringMembershipsThisWeek' => $expiringMembershipsThisWeek,
                'expiringMembershipsThisMonth' => $expiringMembershipsThisMonth,
                'expiredMembershipsToday' => $expiredMembershipsCount,
                'unknownCardsToday' => $unknownCardsCount,
                'recentRfidLogs' => $recentRfidLogs,
                'members' => $members
            ];
        });

        return view('dashboard', $dashboardData);
    }

    /**
     * Get dashboard statistics for API calls
     */
    public function getStats()
    {
        // Cache key with current hour to refresh hourly
        $cacheKey = 'dashboard_stats_' . Carbon::now()->format('Y-m-d_H');

        // Get dashboard stats from cache or compute it
        $stats = Cache::remember($cacheKey, 3600, function () {
            // Get counts using Eloquent - ONLY VALID MEMBERS
            $currentActiveMembersCount = ActiveSession::activeWithValidMembers()->count();
            $totalMembersCount = Member::count();
            $activeMembersCount = Member::active()->count();
            $todayAttendanceCount = Attendance::todayWithValidMembers()->count();
            $weekAttendanceCount = Attendance::thisWeekWithValidMembers()->count();
            $pendingPaymentsCount = Payment::pending()->count();
            $expiringMembershipsCount = Member::where('status', 'active')
                ->where('membership_expires_at', '<=', now()->addDays(7))
                ->count();
            $expiredMembershipsCount = Member::expired()->count();
            $unknownCardsCount = RfidLog::whereDate('timestamp', today())
                ->whereNotIn('card_uid', Member::pluck('uid'))
                ->count();

            // Get this week's revenue
            try {
                $thisWeekRevenue = Payment::completed()
                    ->thisWeek()
                    ->sum('amount') ?? 0;
            } catch (\Exception $e) {
                $thisWeekRevenue = 0;
            }

            return [
                'current_active_members' => $currentActiveMembersCount,
                'total_members' => $totalMembersCount,
                'total_active_members' => $activeMembersCount,
                'today_attendance' => $todayAttendanceCount,
                'this_week_attendance' => $weekAttendanceCount,
                'this_week_revenue' => $thisWeekRevenue,
                'pending_payments' => $pendingPaymentsCount,
                'expiring_memberships' => $expiringMembershipsCount,
                'expired_memberships_today' => $expiredMembershipsCount,
                'unknown_cards_today' => $unknownCardsCount
            ];
        });

        return response()->json($stats);
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