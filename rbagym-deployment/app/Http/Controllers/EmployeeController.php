<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Payment;
use App\Models\MembershipPlan;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        // Use the unified dashboard controller with employee role filtering
        $dashboardController = new \App\Http\Controllers\DashboardController();
        return $dashboardController->index();
    }

    public function rfidMonitor()
    {
        return view('employee.rfid-monitor');
    }

    public function members(Request $request)
    {
        // Get search and filter parameters
        $search = $request->get('search');
        $selectedMembership = $request->get('membership');
        
        // Build the query
        $query = \App\Models\Member::with(['currentMembershipPeriod', 'payments']);
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }
        
        // Apply membership filter
        if ($selectedMembership) {
            $query->whereHas('currentMembershipPeriod', function($q) use ($selectedMembership) {
                $q->where('plan_type', $selectedMembership);
            })->orWhereHas('payments', function($q) use ($selectedMembership) {
                $q->where('plan_type', $selectedMembership)
                  ->where('status', 'completed');
            });
        }
        
        // Get paginated results
        $members = $query->paginate(15);
        
        return view('employee.members.index', compact('members', 'search', 'selectedMembership'));
    }

    public function createMember()
    {
        return view('employee.members.create');
    }

    public function payments(Request $request)
    {
        // Get search and filter parameters
        $search = $request->get('search');
        $planType = $request->get('plan_type');
        $date = $request->get('date');
        $status = $request->get('status');
        
        // Build the query
        $query = Payment::with('member');
        
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('member', function($memberQuery) use ($search) {
                      $memberQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply plan type filter
        if ($planType) {
            $query->where('plan_type', $planType);
        }
        
        // Apply date filter
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }
        
        // Get paginated results
        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get summary statistics for all completed payments (not just current page)
        $completedPayments = Payment::where('status', 'completed');
        
        // Apply same filters to summary statistics
        if ($search) {
            $completedPayments->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('member', function($memberQuery) use ($search) {
                      $memberQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($planType) {
            $completedPayments->where('plan_type', $planType);
        }
        
        if ($date) {
            $completedPayments->whereDate('created_at', $date);
        }
        
        if ($status) {
            $completedPayments->where('status', $status);
        }
        
        $completedCount = $completedPayments->count();
        $totalRevenue = $completedPayments->sum('amount');
        
        return view('employee.payments', compact('payments', 'completedCount', 'totalRevenue'));
    }

    // Employee Membership Methods
    public function manageMember()
    {
        $members = Member::with('currentMembershipPeriod')->get();
        $planTypes = config('membership.plan_types');
        $durationTypes = config('membership.duration_types');
        
        return view('membership.manage-member', compact('members', 'planTypes', 'durationTypes'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'plan_type' => 'required|string',
            'duration_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Employees cannot override - always check for active membership
        $member = Member::with(['membershipPeriods' => function($query) {
            $query->active();
        }])->findOrFail($request->member_id);

        $activeMembership = $member->membershipPeriods()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('expiration_date', '>', now())
            ->first();

        if ($activeMembership) {
            $planTypes = config('membership.plan_types');
            $planName = $planTypes[$activeMembership->plan_type]['name'] ?? $activeMembership->plan_type;

            return response()->json([
                'success' => false,
                'error' => 'ACTIVE_MEMBERSHIP_EXISTS',
                'message' => "Cannot process payment. This member already has an active membership plan: {$planName} (Expires: {$activeMembership->expiration_date->format('F j, Y')})",
                'active_plan' => [
                    'name' => $planName,
                    'expiration_date' => $activeMembership->expiration_date->format('F j, Y')
                ]
            ], 422);
        }

        // If no active membership, delegate to MembershipController
        return app(MembershipController::class)->processPayment($request);
    }

    public function plans()
    {
        $plans = MembershipPlan::orderBy('price')->get();
        $planTypes = config('membership.plan_types');
        $durationTypes = config('membership.duration_types');
        return view('employee.membership-plans', compact('plans', 'planTypes', 'durationTypes'));
    }

    public function paymentDetails($id)
    {
        $payment = Payment::with('member')->findOrFail($id);
        
        $html = view('membership.payments.details', compact('payment'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function exportPaymentsCsv(Request $request)
    {
        $query = Payment::with('member');

        // Apply same filters as payments page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'employee_payments_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Payment ID',
                'Member Name',
                'Member Email',
                'Member UID',
                'Plan Type',
                'Duration',
                'Amount',
                'Payment Date',
                'Payment Time',
                'Membership Start',
                'Membership End',
                'Status',
                'Notes'
            ]);

            // CSV data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->member ? $payment->member->full_name : 'N/A',
                    $payment->member ? $payment->member->email : 'N/A',
                    $payment->member ? $payment->member->uid : 'N/A',
                    ucfirst($payment->plan_type),
                    ucfirst($payment->duration_type),
                    number_format($payment->amount, 2),
                    $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A',
                    $payment->payment_time ?? 'N/A',
                    $payment->membership_start_date ? $payment->membership_start_date->format('Y-m-d') : 'N/A',
                    $payment->membership_expiration_date ? $payment->membership_expiration_date->format('Y-m-d') : 'N/A',
                    ucfirst($payment->status),
                    $payment->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function previewPaymentsCsv(Request $request)
    {
        $query = Payment::with('member');

        // Apply same filters as payments page
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            })->orWhere('id', 'like', "%{$search}%");
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        // Limit to first 10 rows for preview
        $payments = $query->orderBy('created_at', 'desc')->limit(10)->get();
        $totalCount = $query->count();

        $csvData = [];

        // CSV headers
        $csvData[] = [
            'Payment ID',
            'Member Name',
            'Member Email',
            'Member UID',
            'Plan Type',
            'Duration',
            'Amount',
            'Payment Date',
            'Payment Time',
            'Membership Start',
            'Membership End',
            'Status',
            'Notes'
        ];

        // CSV data (preview only)
        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->id,
                $payment->member ? $payment->member->full_name : 'N/A',
                $payment->member ? $payment->member->email : 'N/A',
                $payment->member ? $payment->member->uid : 'N/A',
                ucfirst($payment->plan_type),
                ucfirst($payment->duration_type),
                number_format($payment->amount, 2),
                $payment->payment_date ? $payment->payment_date->format('Y-m-d') : 'N/A',
                $payment->payment_time ?? 'N/A',
                $payment->membership_start_date ? $payment->membership_start_date->format('Y-m-d') : 'N/A',
                $payment->membership_expiration_date ? $payment->membership_expiration_date->format('Y-m-d') : 'N/A',
                ucfirst($payment->status),
                $payment->notes ?? ''
            ];
        }

        return response()->json([
            'success' => true,
            'preview_data' => $csvData,
            'total_records' => $totalCount,
            'preview_records' => count($csvData) - 1, // Exclude header
            'headers' => $csvData[0]
        ]);
    }

    public function printPayment($id)
    {
        $payment = Payment::with('member')->findOrFail($id);
        return view('membership.payments.receipt', compact('payment'));
    }

    // Employee Membership Plans API Methods
    public function getAllPlans()
    {
        $plans = MembershipPlan::orderBy('price')->get();
        return response()->json(['plans' => $plans]);
    }

    public function getPlanTypes()
    {
        $planTypes = config('membership.plan_types');
        return response()->json(['plan_types' => $planTypes]);
    }

    public function getDurationTypes()
    {
        $durationTypes = config('membership.duration_types');
        return response()->json(['duration_types' => $durationTypes]);
    }
}
