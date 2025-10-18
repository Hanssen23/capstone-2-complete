<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipPlan;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $member = Auth::guard('member')->user();

        // Get plan information from multiple sources
        $currentPlan = $member?->current_plan_type;
        $currentDuration = $member?->current_duration_type;
        
        // If no current plan, check for plans from completed payments
        if (!$currentPlan) {
            $latestPayment = $member?->payments()->where('status', 'completed')->latest()->first();
            if ($latestPayment) {
                $currentPlan = $latestPayment->plan_type;
                $currentDuration = $latestPayment->duration_type;
            }
        }

        $data = [
            'member' => $member,
            'membershipStatus' => $member?->membership_status,
            'expiresAt' => $member?->membership_expires_at,
            'todayAttendance' => $member?->attendances()->whereDate('created_at', today())->count() ?? 0,
            'totalAttendance' => $member?->attendances()->count() ?? 0,
            'currentPlan' => $currentPlan,
            'currentDuration' => $currentDuration,
            'isInGym' => $member?->isInGym(),
            'gymPresenceStatus' => $member?->gym_presence_status,
            'onlineStatus' => $member?->online_status,
            'currentGymSession' => $member?->getCurrentGymSession(),
        ];

        return view('members.dashboard', $data);
    }

    public function plans()
    {
        $plans = MembershipPlan::query()->orderBy('price')->get();
        return view('members.plans', compact('plans'));
    }

    public function accounts()
    {
        $member = Auth::guard('member')->user();
        
        // Get member's active sessions
        $activeSessions = $member->activeSessions()
            ->where('status', 'active')
            ->orderBy('check_in_time', 'desc')
            ->get();
        
        // Get recent attendance records
        $recentAttendance = $member->attendances()
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get();
        
        // Get login history (last 5 logins)
        $loginHistory = $member->activeSessions()
            ->orderBy('check_in_time', 'desc')
            ->limit(5)
            ->get();
        
        // Get payment history
        $paymentHistory = $member->payments()
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();
        
        // Get membership periods
        $membershipPeriods = $member->membershipPeriods()
            ->with('payment')
            ->orderBy('start_date', 'desc')
            ->get();

        $data = [
            'member' => $member,
            'activeSessions' => $activeSessions,
            'recentAttendance' => $recentAttendance,
            'loginHistory' => $loginHistory,
            'paymentHistory' => $paymentHistory,
            'membershipPeriods' => $membershipPeriods,
            'membershipStatus' => $member?->membership_status,
            'expiresAt' => $member?->membership_expires_at,
            'currentPlan' => $member?->current_plan_type,
            'currentDuration' => $member?->current_duration_type,
            'isInGym' => $member?->isInGym(),
            'gymPresenceStatus' => $member?->gym_presence_status,
            'onlineStatus' => $member?->online_status,
            'currentGymSession' => $member?->getCurrentGymSession(),
        ];

        return view('members.accounts', $data);
    }

    public function updateProfile(Request $request)
    {
        $member = Auth::guard('member')->user();

        $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'last_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:Male,Female,Other,Prefer not to say',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'mobile_number' => 'nullable|string|max:20',
        ], [
            'first_name.regex' => 'First name must start with a capital letter and can only contain letters and spaces',
            'middle_name.regex' => 'Middle name must start with a capital letter and can only contain letters and spaces',
            'last_name.regex' => 'Last name must start with a capital letter and can only contain letters and spaces',
            'age.integer' => 'Age must be a valid number',
            'age.min' => 'Age must be at least 1',
            'age.max' => 'Age must not exceed 120',
            'gender.in' => 'Please select a valid gender option',
        ]);

        $member->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!',
                'member' => [
                    'email' => $member->email,
                    'mobile_number' => $member->mobile_number,
                    'full_name' => $member->full_name,
                    'member_number' => $member->member_number,
                ]
            ]);
        }

        return redirect()->route('member.accounts')->with('success', 'Profile updated successfully!'); 
    }

    public function membershipPlans()
    {
        $plans = MembershipPlan::query()->orderBy('price')->get();
        return response()->json(['plans' => $plans]);
    }

    public function membershipPlansStream()
    {
        return response()->stream(function () {
            $plans = MembershipPlan::query()->orderBy('price')->get();
            
            echo "data: " . json_encode([
                'plans' => $plans,
                'plans_changed' => true,
                'timestamp' => now()->toISOString()
            ]) . "\n\n";
            
            if (connection_aborted()) {
                return;
            }
            
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}


