<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipPeriod;
use Illuminate\Http\Request;
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
            ]);

        // Filter by membership type if provided and valid
        if (in_array($selectedMembership, ['basic', 'premium', 'vip'])) {
            $membersQuery->where('membership', $selectedMembership);
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

        return view('members.list', compact('members', 'selectedMembership', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'uid' => 'required|string|unique:members,uid',
            'member_number' => 'required|string|unique:members,member_number',
            'membership' => 'required|in:basic,premium,vip',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|unique:members,email',
        ]);

        $member = Member::create([
            'uid' => $request->uid,
            'member_number' => $request->member_number,
            'membership' => $request->membership,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $member = Member::findOrFail($id);
        return view('members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $member = Member::findOrFail($id);
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $member = Member::findOrFail($id);

        $request->validate([
            'uid' => 'required|string|unique:members,uid,' . $id,
            'member_number' => 'required|string|unique:members,member_number,' . $id,
            'membership' => 'required|in:basic,premium,vip',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',
            'email' => 'required|email|unique:members,email,' . $id,
        ]);

        $member->update([
            'uid' => $request->uid,
            'member_number' => $request->member_number,
            'membership' => $request->membership,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully!');
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

        return view('members.membership-history', compact('member', 'membershipPeriods'));
    }
}