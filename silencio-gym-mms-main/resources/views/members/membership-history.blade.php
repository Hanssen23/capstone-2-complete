<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Membership History - {{ $member->full_name }}</x-topbar>

        <div class="bg-gray-50 min-h-screen p-6">
            <!-- Member Info Header -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ $member->full_name }}</h2>
                            <p class="text-gray-600 text-lg mt-2">Member #{{ $member->member_number }} | {{ $member->email }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 mb-2">Current Status</div>
                            <div class="text-2xl font-semibold text-blue-600">{{ $member->membership_status }}</div>
                        </div>
                    </div>
                    
                    <!-- Current Membership Info -->
                    @if($member->currentMembershipPeriod)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-8">
                        <h3 class="text-2xl font-semibold text-blue-900 mb-6">Current Active Membership</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <span class="text-sm text-blue-600 font-medium block mb-2">Plan:</span>
                                <span class="text-lg font-semibold text-blue-900">{{ ucfirst($member->currentMembershipPeriod->plan_type) }}</span>
                            </div>
                            <div class="text-center">
                                <span class="text-sm text-blue-600 font-medium block mb-2">Duration:</span>
                                <span class="text-lg font-semibold text-blue-900">{{ ucfirst($member->currentMembershipPeriod->duration_type) }}</span>
                            </div>
                            <div class="text-center">
                                <span class="text-sm text-blue-600 font-medium block mb-2">Start Date:</span>
                                <span class="text-lg font-semibold text-blue-900">{{ $member->currentMembershipPeriod->start_date }}</span>
                            </div>
                            <div class="text-center">
                                <span class="text-sm text-blue-600 font-medium block mb-2">Expires:</span>
                                <span class="text-lg font-semibold text-blue-900">{{ $member->currentMembershipPeriod->expiration_date }}</span>
                            </div>
                        </div>
                        @if($member->days_until_expiration > 0)
                        <div class="mt-6 text-center">
                            <span class="inline-flex px-6 py-3 text-lg font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $member->days_until_expiration }} days remaining
                            </span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                        <p class="text-gray-600 text-lg mb-4">No active membership</p>
                        <a href="{{ route('membership.manage-member') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Activate Membership
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Membership History Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-200">
                    <h3 class="text-2xl font-semibold text-gray-900">Membership History</h3>
                    <p class="text-gray-600 text-lg mt-2">All membership periods and their status</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan & Duration</th>
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($membershipPeriods as $period)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-semibold text-lg">{{ $period->start_date }}</div>
                                        <div class="text-gray-500 mt-1">to {{ $period->expiration_date }}</div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-semibold text-lg">{{ ucfirst($period->plan_type) }}</span>
                                        <span class="text-gray-500 mx-2">+</span>
                                        <span class="font-semibold text-lg">{{ ucfirst($period->duration_type) }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($period->status === 'active')
                                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($period->status === 'expired')
                                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                            Expired
                                        </span>
                                    @else
                                        <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($period->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if($period->payment)
                                    <div class="text-sm text-gray-900">
                                        <div class="font-semibold text-lg">₱{{ number_format($period->payment->amount, 2) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ $period->payment->payment_date }}</div>
                                        @if($period->payment->payment_time)
                                            <div class="text-xs text-gray-400">{{ $period->payment->payment_time->setTimezone('Asia/Manila')->format('H:i:s') }}</div>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-gray-400 font-medium">No payment</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $period->notes ?: 'No notes' }}
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-8 py-16 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xl font-semibold text-gray-900 mb-3">No membership history</p>
                                        <p class="text-gray-600 text-lg mb-6">This member hasn't had any memberships yet</p>
                                        <a href="{{ route('membership.manage-member') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create First Membership
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($membershipPeriods->hasPages())
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-8 py-6">
                    {{ $membershipPeriods->links() }}
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
                    <div class="flex space-x-6">
                        <a href="{{ route('membership.manage-member') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Process New Payment
                        </a>
                        <a href="{{ route('members.index') }}" class="inline-flex items-center px-8 py-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Members
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
