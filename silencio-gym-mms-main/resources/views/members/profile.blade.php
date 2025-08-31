<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Member Profile</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('members.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mt-2">{{ $member->full_name }}'s Profile</h2>
            </div>

            <!-- Member Information Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Basic Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">UID</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $member->uid }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Member Number</label>
                            <p class="text-sm text-gray-900">{{ $member->member_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-sm text-gray-900">{{ $member->full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-sm text-gray-900">{{ $member->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Mobile Number</label>
                            <p class="text-sm text-gray-900">{{ $member->mobile_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Membership Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Current Membership
                    </h3>
                    <div class="space-y-3">
                        @if($currentMembership)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Plan Type</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($currentMembership->plan_type === 'premium') bg-green-100 text-green-800
                                @elseif($currentMembership->plan_type === 'vip') bg-purple-100 text-purple-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($currentMembership->plan_type) }}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duration</label>
                            <p class="text-sm text-gray-900">{{ ucfirst($currentMembership->duration_type) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Start Date</label>
                            <p class="text-sm text-gray-900">{{ $currentMembership->start_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Expires At</label>
                            <p class="text-sm text-gray-900">{{ $currentMembership->expiration_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Days Until Expiration</label>
                            <p class="text-sm text-gray-900">
                                @if($currentMembership->days_until_expiration >= 0)
                                    {{ $currentMembership->days_until_expiration }} days
                                @else
                                    Expired {{ abs($currentMembership->days_until_expiration) }} days ago
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                @if($currentMembership->status === 'active') bg-green-100 text-green-800
                                @elseif($currentMembership->status === 'expired') bg-red-100 text-red-800
                                @elseif($currentMembership->status === 'cancelled') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($currentMembership->status) }}
                            </span>
                        </div>
                        @else
                        <div>
                            <label class="text-sm font-medium text-gray-500">Current Plan</label>
                            <p class="text-sm text-gray-500">No active membership</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Expires At</label>
                            <p class="text-sm text-gray-500">No expiration date set</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Days Until Expiration</label>
                            <p class="text-sm text-gray-500">No expiration</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Quick Stats
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Visits</label>
                            <p class="text-2xl font-bold text-gray-900">{{ $member->attendances->count() }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">This Month</label>
                            <p class="text-2xl font-bold text-blue-600">{{ $member->attendances->where('check_in_time', '>=', now()->startOfMonth())->count() }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Currently Active</label>
                            <p class="text-2xl font-bold {{ $member->activeSessions->where('status', 'active')->count() > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $member->activeSessions->where('status', 'active')->count() > 0 ? 'Yes' : 'No' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>





            <!-- Recent Activity Tabs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('attendance')" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                            Recent Attendance
                        </button>
                        <button onclick="showTab('rfid')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                            RFID Activity
                        </button>
                        <button onclick="showTab('payments')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                            Payment History
                        </button>
                    </nav>
                </div>

                <!-- Attendance Tab -->
                <div id="attendance-tab" class="tab-content p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in_time->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in_time->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->session_duration ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($attendance->status === 'checked_in') bg-green-100 text-green-800
                                            @elseif($attendance->status === 'checked_out') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($rfidLogs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($log->timestamp)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($log->status === 'success') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $log->message }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No RFID activity found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- RFID Activity Pagination -->
                    @if($rfidLogs->hasPages())
                    <div class="mt-6">
                        {{ $rfidLogs->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>

                <!-- Payment History Tab -->
                <div id="payments-tab" class="tab-content p-6 hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $payment->payment_date->format('M d, Y') }}
                                        @if($payment->payment_time)
                                            <br><span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_time)->setTimezone('Asia/Manila')->format('h:i:s A') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($payment->plan_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ucfirst($payment->duration_type) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            @if($payment->status === 'completed') bg-green-100 text-green-800
                                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No payment history found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Payment History Pagination -->
                    @if($payments->hasPages())
                    <div class="mt-6">
                        {{ $payments->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Add active class to clicked button
            event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }
    </script>
</x-layout>
