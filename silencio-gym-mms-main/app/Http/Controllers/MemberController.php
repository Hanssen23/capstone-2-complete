<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedMembership = $request->query('membership');
        $search = $request->query('search');

        $membersQuery = Member::query()
            ->with([
                'currentMembershipPeriod',
                'attendances' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            // Show all members to admin/employee users, including unverified ones
            ->where('status', '!=', 'deleted'); // Only exclude members marked as deleted

        // Filter by membership type if provided and valid
        if (in_array($selectedMembership, ['basic', 'premium', 'vip'])) {
            $membersQuery->whereHas('currentMembershipPeriod', function ($query) use ($selectedMembership) {
                $query->where('plan_type', $selectedMembership)
                      ->where('status', 'active')
                      ->where('expiration_date', '>', now());
            });
        }

        // Search filter (name, email, member_number, uid, mobile)
        if (!empty($search)) {
            $membersQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_number', 'like', "%{$search}%")
                    ->orWhere('uid', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $members = $membersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.members', compact('members', 'selectedMembership', 'search'));
        }

        return view('members.list', compact('members', 'selectedMembership', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.members.create');
        }
        
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'middle_name' => 'nullable|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'last_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:Male,Female,Other,Prefer not to say',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|unique:members,email,NULL,id,deleted_at,NULL|unique:users,email',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|in:member,admin,employee',
        ], [
            'first_name.regex' => 'First name must start with a capital letter and can only contain letters and spaces',
            'middle_name.regex' => 'Middle name must start with a capital letter and can only contain letters and spaces',
            'last_name.regex' => 'Last name must start with a capital letter and can only contain letters and spaces',
            'age.integer' => 'Age must be a valid number',
            'age.min' => 'Age must be at least 1',
            'age.max' => 'Age must not exceed 120',
            'gender.in' => 'Please select a valid gender option',
            'email.unique' => 'This email is already registered',
        ]);

        // Restrict role creation based on user type
        $requestedRole = $request->role ?? 'member';
        if (request()->is('employee/*')) {
            // Employees can only create member accounts
            $requestedRole = 'member';
        } elseif (auth()->user()->role !== 'admin') {
            // Only admins can create admin/employee accounts
            $requestedRole = 'member';
        }

        // Get an available UID from the pool
        $availableUid = Member::getAvailableUid();
        
        if (!$availableUid) {
            $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
            return redirect()->route($redirectRoute)
                ->with('error', 'No UIDs available in the pool. Please contact administrator.');
        }

        // Clean mobile number (remove spaces and add +63 prefix)
        $mobileNumber = '+63' . preg_replace('/\D/', '', $request->mobile_number);

        $memberData = [
            'uid' => $availableUid,
            'member_number' => Member::generateMemberNumber(),
            'membership' => null, // No plan assigned initially
            'subscription_status' => 'not_subscribed',
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'gender' => $request->gender,
            'mobile_number' => $mobileNumber,
            'email' => $request->email,
            'status' => 'active',
            'role' => $requestedRole, // Use the restricted role
        ];

        // Add password if provided (for admin-created accounts)
        if ($request->password) {
            $memberData['password'] = Hash::make($request->password);
        }

        try {
            $member = Member::create($memberData);
        } catch (\Exception $e) {
            // If member creation fails, return the UID to the pool
            Member::returnUidToPool($availableUid);
            throw $e; // Re-throw the exception
        }

        $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Member created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::findOrFail($id);
        
        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.members.show', compact('member'));
        }
        
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = Member::findOrFail($id);
        
        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.members.edit', compact('member'));
        }
        
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);

        // Check if this is an employee request
        if (request()->is('employee/*')) {
            // Employee can only edit name and personal info fields
            $request->validate([
                'first_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'middle_name' => 'nullable|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'last_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|in:Male,Female,Other,Prefer not to say',
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
            ]);
        } else {
            // Admin can edit all fields
            $request->validate([
                'uid' => 'required|string|unique:members,uid,' . $id,
                'member_number' => 'required|string|unique:members,member_number,' . $id,
                'first_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'middle_name' => 'nullable|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'last_name' => 'required|string|max:255|regex:/^[A-Z][a-zA-Z\s]*$/',
                'age' => 'required|integer|min:1|max:120',
                'gender' => 'required|in:Male,Female,Other,Prefer not to say',
                'email' => 'required|email|unique:members,email,' . $id . ',id,deleted_at,NULL|unique:users,email',
                'mobile_number' => 'nullable|string|max:20',
                // membership is not validated as it's automatically set during payment processing
            ], [
                'first_name.regex' => 'First name must start with a capital letter and can only contain letters and spaces',
                'middle_name.regex' => 'Middle name must start with a capital letter and can only contain letters and spaces',
                'last_name.regex' => 'Last name must start with a capital letter and can only contain letters and spaces',
                'age.integer' => 'Age must be a valid number',
                'age.min' => 'Age must be at least 1',
                'age.max' => 'Age must not exceed 120',
                'gender.in' => 'Please select a valid gender option',
                'email.unique' => 'This email is already registered',
            ]);

            $member->update([
                'uid' => $request->uid,
                'member_number' => $request->member_number,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'age' => $request->age,
                'gender' => $request->gender,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                // membership is not updated as it's automatically set during payment processing
            ]);
        }

        $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
        return redirect()->route($redirectRoute)
            ->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * This performs a HARD DELETE - the member record will be completely removed.
     */
    public function destroy(string $id)
    {
        try {
            $member = Member::findOrFail($id);

            // VALIDATION: Check if member has active membership
            if ($member->membership_expires_at && $member->membership_expires_at->isFuture()) {
                \Log::warning('Attempted to delete member with active membership', [
                    'member_id' => $member->id,
                    'member_name' => $member->full_name,
                    'membership_expires_at' => $member->membership_expires_at->toDateTimeString(),
                    'attempted_by' => auth()->user()->id ?? 'unknown'
                ]);

                $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
                return redirect()->route($redirectRoute)
                    ->with('error', 'Cannot delete member with active membership. Please wait until membership expires or cancel it first.');
            }

            // Store member info for logging before deletion
            $memberInfo = [
                'id' => $member->id,
                'member_number' => $member->member_number,
                'email' => $member->email,
                'full_name' => $member->full_name,
                'uid' => $member->uid,
                'membership_expires_at' => $member->membership_expires_at ? $member->membership_expires_at->toDateTimeString() : null
            ];

            // Note: UID is automatically returned to pool via Member model's boot() method
            // Related data will be handled by database foreign key constraints:
            // - payments, attendances, active_sessions, membership_periods: CASCADE DELETE
            // - rfid_logs: SET NULL
            $member->delete();

            \Log::info('Member hard deleted successfully', $memberInfo);

            $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
            return redirect()->route($redirectRoute)
                ->with('success', 'Member deleted permanently. The email address can now be used for new registrations.');

        } catch (\Exception $e) {
            \Log::error('Member deletion error', [
                'member_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $redirectRoute = request()->is('employee/*') ? 'employee.members.index' : 'members.index';
            return redirect()->route($redirectRoute)
                ->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    /**
     * Display member profile with membership history
     */
    public function profile(string $id, Request $request)
    {
        $member = Member::with([
            'currentMembershipPeriod',
            'membershipPeriods.payment',
            'activeSessions' => function($query) {
                $query->where('status', 'active');
            }
        ])->findOrFail($id);
        
        // Get all membership periods (current and historical) ordered by purchase date
        $membershipPeriods = $member->membershipPeriods()
            ->with('payment')
            ->orderBy('created_at', 'desc') // Order by purchase date (created_at)
            ->get();

        // Get current active membership
        $currentMembership = $member->currentMembershipPeriod;

        // Get paginated attendance records (5 per page)
        $attendances = $member->attendances()
            ->orderBy('check_in_time', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Get paginated RFID activity (5 per page)
        $rfidLogs = \App\Models\RfidLog::where('card_uid', $member->uid)
            ->orderBy('timestamp', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Get paginated payment history (5 per page)
        $payments = $member->payments()
            ->orderBy('payment_date', 'desc')
            ->paginate(5)
            ->withQueryString();

        // Check if this is an employee request
        if (request()->is('employee/*')) {
            return view('employee.members.profile', compact(
                'member', 
                'membershipPeriods', 
                'currentMembership', 
                'payments',
                'attendances',
                'rfidLogs'
            ));
        }
        
        return view('members.profile', compact(
            'member', 
            'membershipPeriods', 
            'currentMembership', 
            'payments',
            'attendances',
            'rfidLogs'
        ));
    }

    /**
     * Display membership history for a member
     */
    public function membershipHistory(string $id)
    {
        $member = Member::findOrFail($id);
        
        $membershipPeriods = $member->membershipPeriods()
            ->with('payment')
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('employee.members.membership-history', compact('member', 'membershipPeriods'));
    }

    /**
     * Update member UID
     */
    public function updateUid(Request $request, string $id)
    {
        $member = Member::findOrFail($id);
        
        $request->validate([
            'uid' => 'required|string|unique:members,uid,' . $id,
        ]);

        $oldUid = $member->uid;
        $member->update(['uid' => $request->input('uid')]);

        return response()->json([
            'success' => true,
            'message' => "UID updated from {$oldUid} to {$request->input('uid')}",
            'member' => [
                'id' => $member->id,
                'name' => $member->first_name . ' ' . $member->last_name,
                'uid' => $member->uid,
            ]
        ]);
    }
}