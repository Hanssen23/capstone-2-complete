<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipPlan;
use App\Models\Member;
use App\Models\Payment;
use App\Models\MembershipPeriod;
use Carbon\Carbon;

class MembershipController extends Controller
{
    /**
     * Display the membership plans configuration page
     */
    public function index()
    {
        $plans = MembershipPlan::all();
        $planTypes = config('membership.plan_types');
        $durationTypes = config('membership.duration_types');
        
        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.membership-plans-full', compact('plans', 'planTypes', 'durationTypes'));
        }
        
        return view('membership.plans.index', compact('plans', 'planTypes', 'durationTypes'));
    }

    /**
     * Display the member plan management page
     */
    public function manageMember()
    {
        $members = Member::with('currentMembershipPeriod')->get();
        $planTypes = config('membership.plan_types');
        $durationTypes = config('membership.duration_types');
        
        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('membership.manage-member', compact('members', 'planTypes', 'durationTypes'));
        }
        
        return view('membership.manage-member', compact('members', 'planTypes', 'durationTypes'));
    }

    /**
     * Calculate membership price based on plan type and duration
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|string',
            'duration_type' => 'required|string',
        ]);

        $planType = $request->plan_type;
        $durationType = $request->duration_type;

        $planTypes = config('membership.plan_types');
        $durationTypes = config('membership.duration_types');

        if (!isset($planTypes[$planType]) || !isset($durationTypes[$durationType])) {
            return response()->json(['error' => 'Invalid plan type or duration'], 400);
        }

        $basePrice = $planTypes[$planType]['base_price'];
        $multiplier = $durationTypes[$durationType]['multiplier'];
        $totalPrice = $basePrice * $multiplier;

        return response()->json([
            'base_price' => $basePrice,
            'multiplier' => $multiplier,
            'total_price' => $totalPrice,
            'duration_days' => $durationTypes[$durationType]['days']
        ]);
    }

    /**
     * Check if member has active membership plan
     */
    public function checkActiveMembership(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $member = Member::with(['currentMembershipPeriod', 'membershipPeriods' => function($query) {
            $query->active()->orderBy('expiration_date', 'desc');
        }])->findOrFail($request->member_id);

        // Check for active membership periods
        $activeMembership = $member->membershipPeriods()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('expiration_date', '>', now())
            ->orderBy('expiration_date', 'desc')
            ->first();

        if ($activeMembership) {
            $planTypes = config('membership.plan_types');
            $planName = $planTypes[$activeMembership->plan_type]['name'] ?? $activeMembership->plan_type;

            return response()->json([
                'has_active_plan' => true,
                'plan_name' => $planName,
                'plan_type' => $activeMembership->plan_type,
                'expiration_date' => $activeMembership->expiration_date->format('F j, Y'),
                'start_date' => $activeMembership->start_date->format('F j, Y'),
            ]);
        }

        return response()->json([
            'has_active_plan' => false
        ]);
    }

    /**
     * Process payment and create membership period
     */
    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'plan_type' => 'required|string',
                'duration_type' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'start_date' => 'required|date',
                'notes' => 'nullable|string',
                'tin' => 'nullable|string',
                'is_pwd' => 'nullable|boolean',
                'is_senior_citizen' => 'nullable|boolean',
                'discount_amount' => 'nullable|numeric|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
            ]);

            \Log::info('Processing payment', [
                'member_id' => $validated['member_id'],
                'plan_type' => $validated['plan_type'],
                'duration_type' => $validated['duration_type'],
                'amount' => $validated['amount']
            ]);

            // Initialize variables outside transaction
            $payment = null;
            $membershipPeriod = null;

            // Use database transaction for data consistency
            \DB::transaction(function () use ($validated, &$payment, &$membershipPeriod) {
                // Calculate expiration date
                $startDate = Carbon::parse($validated['start_date']);
                $durationTypes = config('membership.duration_types');

                if (!isset($durationTypes[$validated['duration_type']])) {
                    throw new \Exception('Invalid duration type: ' . $validated['duration_type']);
                }

                $durationDays = $durationTypes[$validated['duration_type']]['days'];
                $expirationDate = $startDate->copy()->addDays($durationDays);
                $now = now()->setTimezone('Asia/Manila');

                // Create payment record
                $payment = Payment::create([
                    'member_id' => $validated['member_id'],
                    'amount' => $validated['amount'],
                    'payment_date' => $now->toDateString(),
                    'payment_time' => $now->format('H:i:s'),
                    'status' => 'completed',
                    'plan_type' => $validated['plan_type'],
                    'duration_type' => $validated['duration_type'],
                    'membership_start_date' => $startDate,
                    'membership_expiration_date' => $expirationDate,
                    'notes' => $validated['notes'] ?? null,
                    'tin' => $validated['tin'] ?? null,
                    'is_pwd' => $validated['is_pwd'] ?? false,
                    'is_senior_citizen' => $validated['is_senior_citizen'] ?? false,
                    'discount_amount' => $validated['discount_amount'] ?? 0.00,
                    'discount_percentage' => $validated['discount_percentage'] ?? 0.00,
                ]);

                \Log::info('Payment created', ['payment_id' => $payment->id]);

                // Create membership period
                $membershipPeriod = MembershipPeriod::create([
                    'member_id' => $validated['member_id'],
                    'payment_id' => $payment->id,
                    'plan_type' => $validated['plan_type'],
                    'duration_type' => $validated['duration_type'],
                    'start_date' => $startDate,
                    'expiration_date' => $expirationDate,
                    'status' => 'active',
                    'notes' => $validated['notes'] ?? null,
                ]);

                \Log::info('Membership period created', ['membership_period_id' => $membershipPeriod->id]);

                // Update member's current membership
                Member::where('id', $validated['member_id'])->update([
                    'current_membership_period_id' => $membershipPeriod->id,
                    'membership_starts_at' => $startDate,
                    'membership_expires_at' => $expirationDate,
                    'current_plan_type' => $validated['plan_type'],
                    'current_duration_type' => $validated['duration_type'],
                    'membership' => $validated['plan_type'],
                    'subscription_status' => 'active',
                    'status' => 'active',
                ]);

                \Log::info('Member updated', ['member_id' => $validated['member_id']]);
            });

            \Log::info('Payment processing completed successfully', [
                'payment_id' => $payment->id,
                'membership_period_id' => $membershipPeriod->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed and membership activated successfully',
                'payment_id' => $payment->id,
                'membership_period_id' => $membershipPeriod->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Payment validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors()))
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the payment. Please try again or contact support.'
            ], 500);
        }
    }

    /**
     * Display all payments with filtering
     */
    public function payments(Request $request)
    {
        $query = Payment::with('member');

        // Apply filters
        if ($request->filled('plan_type') && $request->plan_type !== '') {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('date') && $request->date !== '') {
            $query->whereDate('payment_date', $request->date);
        }

        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.payments', compact('payments'));
        }

        return view('membership.payments.index', compact('payments'));
    }

    /**
     * Export payments to CSV
     */
    public function exportToCsv(Request $request)
    {
        $query = Payment::with('member')
            ->select([
                'id',
                'member_id',
                'plan_type',
                'duration_type',
                'amount',
                'payment_date',
                'payment_time',
                'membership_start_date',
                'membership_expiration_date'
            ]);

        // Apply the same filters as the payments page
        if ($request->filled('plan_type') && $request->plan_type !== '') {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('date') && $request->date !== '') {
            $query->whereDate('payment_date', $request->date);
        }

        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $filename = 'payments_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Payment ID',
                'Member Name',
                'Member Number',
                'Plan Type',
                'Duration Type',
                'Amount',
                'Payment Date',
                'Payment Time',
                'Membership Start Date',
                'Membership Expiration Date'
            ]);

            // Add payment data
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->member->first_name . ' ' . $payment->member->last_name,
                    $payment->member->member_number,
                    ucfirst($payment->plan_type),
                    ucfirst($payment->duration_type),
                    number_format($payment->amount, 2),
                    Carbon::parse($payment->payment_date)->format('m/d/Y'),
                    $payment->payment_time ? Carbon::parse($payment->payment_time)->format('h:i:s A') : 'N/A',
                    Carbon::parse($payment->membership_start_date)->format('m/d/Y'),
                    Carbon::parse($payment->membership_expiration_date)->format('m/d/Y')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Preview CSV data before export
     */
    public function previewCsv(Request $request)
    {
        $query = Payment::with('member')
            ->select([
                'id',
                'member_id',
                'plan_type',
                'duration_type',
                'amount',
                'payment_date',
                'payment_time',
                'membership_start_date',
                'membership_expiration_date',
                'status',
                'notes'
            ]);

        // Apply the same filters as the payments page
        if ($request->filled('plan_type') && $request->plan_type !== '') {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('date') && $request->date !== '') {
            $query->whereDate('payment_date', $request->date);
        }

        if ($request->filled('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        // Get total count and preview data
        $totalCount = $query->count();
        $payments = $query->orderBy('payment_date', 'desc')->limit(10)->get();

        $csvData = [];

        // CSV headers
        $csvData[] = [
            'Payment ID',
            'Member Name',
            'Member UID',
            'Plan Type',
            'Duration',
            'Amount',
            'Payment Date',
            'Payment Time',
            'Membership Start',
            'Membership End'
        ];

        // Add payment data (preview only)
        foreach ($payments as $payment) {
            $csvData[] = [
                $payment->id,
                $payment->member->first_name . ' ' . $payment->member->last_name,
                $payment->member->uid,
                ucfirst($payment->plan_type),
                ucfirst($payment->duration_type),
                number_format($payment->amount, 2),
                Carbon::parse($payment->payment_date)->format('m/d/Y'),
                $payment->payment_time ? Carbon::parse($payment->payment_time)->format('h:i:s A') : 'N/A',
                Carbon::parse($payment->membership_start_date)->format('m/d/Y'),
                Carbon::parse($payment->membership_expiration_date)->format('m/d/Y')
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

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:completed,failed,pending'
        ]);

        try {
            $oldStatus = $payment->status;
            $newStatus = $request->status;

            // Update payment status
            $payment->update(['status' => $newStatus]);

            // If completing a payment, activate the membership
            if ($oldStatus === 'pending' && $newStatus === 'completed') {
                $this->activateMembership($payment);
            }

            // If failing a payment, deactivate the membership
            if ($oldStatus === 'pending' && $newStatus === 'failed') {
                $this->deactivateMembership($payment);
            }

            return response()->json([
                'success' => true,
                'message' => "Payment status updated to {$newStatus} successfully"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details for modal
     */
    public function getPaymentDetails(Payment $payment)
    {
        $payment->load(['member', 'membershipPeriod']);
        
        return response()->json([
            'success' => true,
            'payment' => $payment,
            'membership_period' => $payment->membershipPeriod
        ]);
    }

    /**
     * Print payment receipt
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['member', 'membershipPeriod']);
        
        return view('membership.payments.receipt', compact('payment'));
    }

    /**
     * Activate membership for a completed payment
     */
    private function activateMembership(Payment $payment)
    {
        // Find or create membership period
        $membershipPeriod = MembershipPeriod::where('payment_id', $payment->id)->first();
        
        if ($membershipPeriod) {
            $membershipPeriod->update(['status' => 'active']);
            
            // Update member's current membership
            $member = $payment->member;
            $member->update([
                'current_membership_period_id' => $membershipPeriod->id,
                'membership_starts_at' => $payment->membership_start_date,
                'membership_expires_at' => $payment->membership_expiration_date,
                'current_plan_type' => $payment->plan_type,
                'current_duration_type' => $payment->duration_type,
                'subscription_status' => 'active',
                'status' => 'active',
            ]);
        }
    }

    /**
     * Deactivate membership for a failed payment
     */
    private function deactivateMembership(Payment $payment)
    {
        // Find membership period and mark as cancelled
        $membershipPeriod = MembershipPeriod::where('payment_id', $payment->id)->first();
        
        if ($membershipPeriod) {
            $membershipPeriod->update(['status' => 'cancelled']);
            
            // Clear member's current membership if this was their active one
            $member = $payment->member;
            if ($member->current_membership_period_id === $membershipPeriod->id) {
                $member->update([
                    'current_membership_period_id' => null,
                    'membership_starts_at' => null,
                    'membership_expires_at' => null,
                    'current_plan_type' => null,
                    'current_duration_type' => null,
                    'subscription_status' => 'cancelled',
                    'status' => 'inactive',
                ]);
            }
        }
    }

    /**
     * Get all membership plans (API endpoint)
     */
    public function getAllPlans()
    {
        $plans = \App\Models\MembershipPlan::orderBy('price')->get();
        return response()->json(['plans' => $plans]);
    }

    /**
     * Store a new membership plan
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $plan = \App\Models\MembershipPlan::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Membership plan created successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Update a membership plan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $plan = \App\Models\MembershipPlan::findOrFail($id);
        $plan->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Membership plan updated successfully',
            'plan' => $plan
        ]);
    }

    /**
     * Delete a membership plan
     */
    public function destroy($id)
    {
        $plan = \App\Models\MembershipPlan::findOrFail($id);
        $plan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Membership plan deleted successfully'
        ]);
    }

    /**
     * Update duration types
     */
    public function updateDurationTypes(Request $request)
    {
        $request->validate([
            'duration_types' => 'required|array',
        ]);

        // Update config or database with new duration types
        // This is a placeholder implementation
        return response()->json([
            'success' => true,
            'message' => 'Duration types updated successfully'
        ]);
    }
}