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
        // Get current active members (currently logged in)
        $currentActiveMembersCount = ActiveSession::active()->count();
        
        // Get total active members (with valid memberships)
        $totalActiveMembersCount = Member::active()->count();
        
        // Get today's attendance
        $todayAttendance = Attendance::today()->count();
        
        // Get this week's attendance
        $thisWeekAttendance = Attendance::thisWeek()->count();
        
        // Get this month's revenue
        $thisMonthRevenue = Payment::completed()->thisMonth()->sum('amount');
        
        // Get pending payments count
        $pendingPaymentsCount = Payment::pending()->count();
        
        // Get memberships expiring this week (from multiple sources)
        $expiringMembershipsCount = Member::expiringThisWeek()->count();
        
        // Get memberships expiring this month (30 days)
        $expiringMembershipsThisMonth = Member::expiringSoon(30)->count();
        
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
            'thisMonthRevenue',
            'pendingPaymentsCount',
            'expiringMembershipsCount',
            'expiringMembershipsThisMonth',
            'expiringPaymentsCount',
            'recentRfidLogs',
            'expiredMembershipsToday',
            'unknownCardsToday'
        ));
    }

    public function getDashboardStats()
    {
        // Get current active members (currently logged in)
        $currentActiveMembersCount = ActiveSession::active()->count();
        
        // Get total active members (with valid memberships)
        $totalActiveMembersCount = Member::active()->count();
        
        // Get today's attendance
        $todayAttendance = Attendance::today()->count();
        
        // Get this week's attendance
        $thisWeekAttendance = Attendance::thisWeek()->count();
        
        // Get this month's revenue
        $thisMonthRevenue = Payment::completed()->thisMonth()->sum('amount');
        
        // Get pending payments count
        $pendingPaymentsCount = Payment::pending()->count();
        
        // Get memberships expiring this week
        $expiringMembershipsCount = Member::expiringThisWeek()->count();
        
        // Get memberships expiring this month (30 days)
        $expiringMembershipsThisMonth = Member::expiringSoon(30)->count();
        
        // Get expired memberships today
        $expiredMembershipsToday = Member::expired()->count();
        
        // Get unknown cards today
        $unknownCardsToday = RfidLog::unknownCards()->today()->count();

        return response()->json([
            'currentActiveMembersCount' => $currentActiveMembersCount,
            'totalActiveMembersCount' => $totalActiveMembersCount,
            'todayAttendance' => $todayAttendance,
            'thisWeekAttendance' => $thisWeekAttendance,
            'thisMonthRevenue' => $thisMonthRevenue,
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'expiringMembershipsCount' => $expiringMembershipsCount,
            'expiringMembershipsThisMonth' => $expiringMembershipsThisMonth,
            'expiredMembershipsToday' => $expiredMembershipsToday,
            'unknownCardsToday' => $unknownCardsToday,
        ]);
    }
}
