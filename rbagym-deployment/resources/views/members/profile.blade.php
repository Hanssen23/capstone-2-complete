<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-white">
        <x-topbar>Member Profile</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('members.index') }}" class="flex items-center gap-2 transition-colors duration-200" style="color: #6B7280;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6B7280'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                </div>
                <h2 class="text-2xl font-bold mt-2" style="color: #1E40AF;">{{ $member->full_name }}'s Profile</h2>
            </div>

            <!-- Two-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-lg border p-6" style="border-color: #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2" style="color: #1E40AF;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Basic Information
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">UID</label>
                                <p class="text-sm font-mono mt-1" style="color: #000000;">{{ $member->uid }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Member Number</label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $member->member_number }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Full Name</label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $member->full_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium flex items-center gap-2" style="color: #6B7280;">
                                    📧 Email
                                </label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $member->email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium flex items-center gap-2" style="color: #6B7280;">
                                    📱 Mobile Number
                                </label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $member->mobile_number }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Current Membership Card -->
                    <div class="bg-white rounded-lg border p-6" style="border-color: #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2" style="color: #1E40AF;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Current Membership
                        </h3>
                        <div class="space-y-4">
                            @if($currentMembership)
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Plan Type</label>
                                <div class="mt-1">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" 
                                          style="background-color: {{ $currentMembership->plan_type === 'premium' ? '#059669' : ($currentMembership->plan_type === 'vip' ? '#F59E0B' : '#2563EB') }};">
                                        {{ ucfirst($currentMembership->plan_type) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Duration</label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ ucfirst($currentMembership->duration_type) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Start Date</label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $currentMembership->start_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Expires At</label>
                                <p class="text-sm mt-1" style="color: #000000;">{{ $currentMembership->expiration_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Days Until Expiration</label>
                                <p class="text-sm mt-1" style="color: #000000;">
                                    @if($currentMembership->days_until_expiration >= 0)
                                        {{ $currentMembership->days_until_expiration }} days
                                    @else
                                        Expired {{ abs($currentMembership->days_until_expiration) }} days ago
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Status</label>
                                <div class="mt-1">
                                    @if($currentMembership->status === 'active')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #059669;">
                                            Active
                                        </span>
                                    @elseif($currentMembership->status === 'expired')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #DC2626;">
                                            Expired
                                        </span>
                                    @elseif($currentMembership->status === 'cancelled')
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #DC2626;">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #D97706;">
                                            {{ ucfirst($currentMembership->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Current Plan</label>
                                <p class="text-sm mt-1" style="color: #6B7280;">No active membership</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Expires At</label>
                                <p class="text-sm mt-1" style="color: #6B7280;">No expiration date set</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Days Until Expiration</label>
                                <p class="text-sm mt-1" style="color: #6B7280;">No expiration</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="bg-white rounded-lg border p-6" style="border-color: #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2" style="color: #1E40AF;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Quick Stats
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Total Visits</label>
                                <p class="text-2xl font-bold mt-1" style="color: #000000;">{{ $member->attendances->count() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">This Month</label>
                                <p class="text-2xl font-bold mt-1" style="color: #2563EB;">{{ $member->attendances->where('check_in_time', '>=', now()->startOfMonth())->count() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium" style="color: #6B7280;">Currently Active</label>
                                <p class="text-2xl font-bold mt-1" style="color: {{ $member->activeSessions->where('status', 'active')->count() > 0 ? '#059669' : '#6B7280' }};">
                                    {{ $member->activeSessions->where('status', 'active')->count() > 0 ? 'Yes' : 'No' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">





                    <!-- Recent Activity Tabs -->
                    <div class="bg-white rounded-lg border" style="border-color: #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div class="border-b" style="border-color: #E5E7EB;">
                            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                                <button onclick="showTab('attendance')" class="tab-button active py-4 px-1 border-b-2 font-medium text-sm" style="border-color: #1E40AF; color: #1E40AF;">
                                    Recent Attendance
                                </button>
                                <button onclick="showTab('rfid')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm transition-colors" style="color: #6B7280;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6B7280'">
                                    RFID Activity
                                </button>
                                <button onclick="showTab('payments')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm transition-colors" style="color: #6B7280;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6B7280'">
                                    Payment History
                                </button>
                            </nav>
                        </div>

                <!-- Attendance Tab -->
                <div id="attendance-tab" class="tab-content p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold" style="color: #1E40AF;">Recent Attendance</h3>
                        <div class="flex items-center space-x-2">
                            <button onclick="previousPage()" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">
                                < Previous
                            </button>
                            <span class="px-3 py-1 rounded-md" style="background-color: #F9FAFB; color: #000000;" id="current-page">1</span>
                            <button onclick="nextPage()" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">
                                Next >
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="border-color: #E5E7EB;">
                            <thead style="background-color: #1E40AF;">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Check In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Check Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="background-color: #FFFFFF; border-color: #E5E7EB;">
                                @forelse($attendances as $attendance)
                                <tr class="transition-colors" style="background-color: {{ $loop->even ? '#F9FAFB' : '#FFFFFF' }};" onmouseover="this.style.backgroundColor='#F3F4F6'" onmouseout="this.style.backgroundColor='{{ $loop->even ? '#F9FAFB' : '#FFFFFF' }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $attendance->check_in_time->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $attendance->check_in_time->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        @if($attendance->session_duration)
                                            @php
                                                $duration = $attendance->session_duration;
                                                if (is_string($duration) && strpos($duration, 'h') !== false) {
                                                    // Handle format like "0.20366143194444h 12m"
                                                    $parts = explode('h', $duration);
                                                    $hours = floatval($parts[0]);
                                                    $minutes = 0;
                                                    if (isset($parts[1])) {
                                                        $minPart = trim($parts[1]);
                                                        if (strpos($minPart, 'm') !== false) {
                                                            $minutes = intval(str_replace('m', '', $minPart));
                                                        }
                                                    }
                                                    
                                                    // Convert decimal hours to minutes and add the existing minutes
                                                    $decimalMinutes = round($hours * 60);
                                                    $totalMinutes = $decimalMinutes + $minutes;
                                                    
                                                    $displayHours = floor($totalMinutes / 60);
                                                    $displayMinutes = $totalMinutes % 60;
                                                    
                                                    if ($displayHours > 0) {
                                                        echo $displayHours . 'h ' . $displayMinutes . 'm';
                                                    } else {
                                                        echo $displayMinutes . 'm';
                                                    }
                                                } else {
                                                    echo $duration;
                                                }
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->status === 'checked_in')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #059669;">
                                                Checked In
                                            </span>
                                        @elseif($attendance->status === 'checked_out')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #2563EB;">
                                                Checked Out
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #6B7280;">
                                                {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm" style="color: #6B7280;">
                                        No attendance records found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Attendance Pagination -->
                    @if($attendances->hasPages())
                    <div class="mt-6">
                        {{ $attendances->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>

                <!-- RFID Activity Tab -->
                <div id="rfid-tab" class="tab-content p-6 hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold" style="color: #1E40AF;">RFID Activity</h3>
                        @if($rfidLogs->hasPages())
                        <div class="flex items-center space-x-2">
                            @if($rfidLogs->onFirstPage())
                                <span class="px-3 py-1 rounded-md" style="background-color: #F3F4F6; color: #6B7280;">< Previous</span>
                            @else
                                <a href="{{ $rfidLogs->previousPageUrl() }}" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">< Previous</a>
                            @endif
                            
                            <span class="px-3 py-1 rounded-md" style="background-color: #F9FAFB; color: #000000;">{{ $rfidLogs->currentPage() }}</span>
                            
                            @if($rfidLogs->hasMorePages())
                                <a href="{{ $rfidLogs->nextPageUrl() }}" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">Next ></a>
                            @else
                                <span class="px-3 py-1 rounded-md" style="background-color: #F3F4F6; color: #6B7280;">Next ></span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="border-color: #E5E7EB;">
                            <thead style="background-color: #1E40AF;">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Message</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="background-color: #FFFFFF; border-color: #E5E7EB;">
                                @forelse($rfidLogs as $log)
                                <tr class="transition-colors" style="background-color: {{ $loop->even ? '#F9FAFB' : '#FFFFFF' }};" onmouseover="this.style.backgroundColor='#F3F4F6'" onmouseout="this.style.backgroundColor='{{ $loop->even ? '#F9FAFB' : '#FFFFFF' }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ \Carbon\Carbon::parse($log->timestamp)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->status === 'success')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #059669;">
                                                Success
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #DC2626;">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm" style="color: #000000;">
                                        {{ $log->message }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm" style="color: #6B7280;">
                                        No RFID activity found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History Tab -->
                <div id="payments-tab" class="tab-content p-6 hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold" style="color: #1E40AF;">Payment History</h3>
                        @if($payments->hasPages())
                        <div class="flex items-center space-x-2">
                            @if($payments->onFirstPage())
                                <span class="px-3 py-1 rounded-md" style="background-color: #F3F4F6; color: #6B7280;">< Previous</span>
                            @else
                                <a href="{{ $payments->previousPageUrl() }}" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">< Previous</a>
                            @endif
                            
                            <span class="px-3 py-1 rounded-md" style="background-color: #F9FAFB; color: #000000;">{{ $payments->currentPage() }}</span>
                            
                            @if($payments->hasMorePages())
                                <a href="{{ $payments->nextPageUrl() }}" class="px-3 py-1 rounded-md transition-colors" style="background-color: #F3F4F6; color: #6B7280;" onmouseover="this.style.backgroundColor='#E5E7EB'" onmouseout="this.style.backgroundColor='#F3F4F6'">Next ></a>
                            @else
                                <span class="px-3 py-1 rounded-md" style="background-color: #F3F4F6; color: #6B7280;">Next ></span>
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y" style="border-color: #E5E7EB;">
                            <thead style="background-color: #1E40AF;">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Plan Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="background-color: #FFFFFF; border-color: #E5E7EB;">
                                @forelse($payments as $payment)
                                <tr class="transition-colors" style="background-color: {{ $loop->even ? '#F9FAFB' : '#FFFFFF' }};" onmouseover="this.style.backgroundColor='#F3F4F6'" onmouseout="this.style.backgroundColor='{{ $loop->even ? '#F9FAFB' : '#FFFFFF' }}'">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                        @if($payment->payment_time)
                                            <br><span class="text-xs" style="color: #6B7280;">{{ \Carbon\Carbon::parse($payment->payment_time)->setTimezone('Asia/Manila')->format('h:i:s A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ ucfirst($payment->plan_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">
                                        {{ ucfirst($payment->duration_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->status === 'completed')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #059669;">
                                                Completed
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #D97706;">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" style="background-color: #DC2626;">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm" style="color: #6B7280;">
                                        No payment history found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const totalPages = {{ $attendances->lastPage() }};

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePage();
            }
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePage();
            }
        }

        function updatePage() {
            document.getElementById('current-page').textContent = currentPage;
            // Here you would typically make an AJAX call to load the new page data
            // For now, we'll just update the page number display
        }

        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Reset all tab buttons to inactive state
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
                button.style.borderColor = 'transparent';
                button.style.color = '#6B7280';
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active styling to clicked button
            event.target.classList.add('active');
            event.target.style.borderColor = '#1E40AF';
            event.target.style.color = '#1E40AF';
        }
    </script>
</x-layout>
