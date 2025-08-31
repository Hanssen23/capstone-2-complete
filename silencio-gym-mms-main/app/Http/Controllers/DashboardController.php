<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ActiveSession;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\RfidLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
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
        
        // Also get count from payments for verification
        $expiringPaymentsCount = Payment::expiringThisWeek()->count();
        
        // Get recent RFID logs
        $recentRfidLogs = RfidLog::latest('timestamp')->take(10)->get();
        
        // Get failed RFID attempts today
        $failedRfidToday = RfidLog::failed()->today()->count();
        
        // Get unknown cards today
        $unknownCardsToday = RfidLog::unknownCards()->today()->count();

        return view('dashboard', compact(
            'currentActiveMembersCount',
            'totalActiveMembersCount',
            'todayAttendance',
            'thisWeekAttendance',
            'thisMonthRevenue',
            'pendingPaymentsCount',
            'expiringMembershipsCount',
            'expiringPaymentsCount',
            'recentRfidLogs',
            'failedRfidToday',
            'unknownCardsToday'
        ));
    }

    public function getDashboardStats()
    {
        // Real-time stats for AJAX updates
        $currentActiveMembersCount = ActiveSession::active()->count();
        $todayAttendance = Attendance::today()->count();
        $failedRfidToday = RfidLog::failed()->today()->count();
        
        return response()->json([
            'current_active_members' => $currentActiveMembersCount,
            'today_attendance' => $todayAttendance,
            'failed_rfid_today' => $failedRfidToday,
            'last_updated' => now()->format('H:i:s'),
        ]);
    }
}
