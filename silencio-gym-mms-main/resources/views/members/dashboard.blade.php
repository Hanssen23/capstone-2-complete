<x-layout>
    <x-nav-member></x-nav-member>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Member Dashboard</x-topbar>

        <div class="bg-gray-100 p-6">
            <!-- Welcome Section with Real-time Status -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold mb-2 text-black">Welcome back, {{ $member->full_name ?? $member->first_name }}!</h1>
                            <p class="text-black font-medium">Member #{{ $member->member_number }} • {{ ucfirst($membershipStatus) }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($isInGym)
                                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-sm text-black font-medium">In Gym</span>
                            @else
                                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                <span class="text-sm text-black font-medium">Not in Gym</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Profile Summary -->
            <div class="mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Member Info Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border-2 border-blue-300 shadow-lg">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $member->full_name }}</h3>
                                <p class="text-sm text-gray-600">Member #{{ $member->member_number }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Email:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $member->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Mobile:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $member->mobile_number ?: 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Membership Status Card -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border-2 border-green-300 shadow-lg">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Membership Status</h3>
                                <p class="text-sm text-gray-600">{{ $membershipStatus }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Current Plan:</span>
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($currentPlan ?: 'None') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Duration:</span>
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($currentDuration ?: 'N/A') }}</span>
                            </div>
                            @if($expiresAt)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Expires:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $expiresAt->format('M d, Y') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                     <!-- Current Session Card -->
                     <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border-2 border-purple-300 shadow-lg">
                         <div class="flex items-center mb-4">
                             <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mr-4">
                                 <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                 </svg>
                             </div>
                             <div>
                                 <h3 class="text-xl font-semibold text-gray-900">Current Session</h3>
                                 <p class="text-sm text-gray-600">{{ $isInGym ? 'Active Session' : 'No Active Session' }}</p>
                             </div>
                         </div>
                         @if($isInGym && $currentGymSession)
                             <div class="bg-white rounded-lg p-4 space-y-2">
                                 <div class="flex justify-between items-center">
                                     <span class="text-sm text-gray-600">Check-in:</span>
                                     <span class="text-sm font-medium text-gray-900">{{ $currentGymSession->check_in_time->format('M d, Y g:i A') }}</span>
                                 </div>
                                 <div class="flex justify-between items-center">
                                     <span class="text-sm text-gray-600">Duration:</span>
                                     <span class="text-sm font-medium text-purple-600">{{ $currentGymSession->check_in_time->diffForHumans(null, true) }}</span>
                                 </div>
                             </div>
                         @else
                             <div class="bg-white rounded-lg p-4">
                                 <p class="text-sm text-gray-500 text-center">Not currently in gym</p>
                                 <p class="text-xs text-gray-400 text-center mt-1">Tap your RFID card to check in</p>
                             </div>
                         @endif
                     </div>
                </div>
            </div>


            <!-- Enhanced Metrics Dashboard Cards -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard Metrics</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Today's Attendance Card -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl shadow-lg border-2 border-green-200 p-6 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Today</h3>
                                    <p class="text-xs text-gray-500">Attendance</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-bold text-green-600 mb-2">{{ $todayAttendance }}</p>
                            <p class="text-xs text-gray-500">Last updated: <span id="last-updated">{{ now()->format('H:i') }}</span></p>
                        </div>
                    </div>

                    <!-- Total Attendance Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-sky-100 rounded-xl shadow-lg border-2 border-blue-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Total</h3>
                                    <p class="text-xs text-gray-500">All Time Visits</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-4xl font-bold text-blue-600 mb-2">{{ $totalAttendance }}</p>
                            <p class="text-xs text-gray-500">Lifetime gym visits</p>
                        </div>
                    </div>

                    <!-- Membership Status Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl shadow-lg border-2 border-purple-200 p-6 hover:shadow-xl transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Plan</h3>
                                    <p class="text-xs text-gray-500">Membership</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600 mb-1">{{ ucfirst($currentPlan ?? 'None') }}</p>
                            <p class="text-sm text-gray-600 mb-2">{{ ucfirst($currentDuration ?? 'N/A') }}</p>
                            <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full {{ $membershipStatus === 'Active' ? 'bg-green-100 text-green-800' : ($membershipStatus === 'Expired' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $membershipStatus }}
                            </span>
                        </div>
                    </div>

                    <!-- Days Remaining Card -->
                    <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-xl shadow-lg border-2 border-orange-200 p-6 hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Days</h3>
                                    <p class="text-xs text-gray-500">Remaining</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            @if($expiresAt)
                                @php
                                    $expiresAtCarbon = \Carbon\Carbon::parse($expiresAt);
                                    $daysLeft = $member->days_until_expiration;
                                @endphp
                                @if($membershipStatus === 'Expired')
                                    <p class="text-4xl font-bold text-red-600 mb-2">0</p>
                                    <p class="text-xs text-gray-500 mb-2">Expired on {{ $expiresAtCarbon->format('M d, Y') }}</p>
                                    <span class="text-xs text-red-600 font-medium">Membership Expired</span>
                                @else
                                    <p class="text-4xl font-bold {{ $daysLeft > 7 ? 'text-green-600' : 'text-red-600' }} mb-2">{{ $daysLeft }}</p>
                                    <p class="text-xs text-gray-500 mb-2">Until {{ $expiresAtCarbon->format('M d, Y') }}</p>
                                    <span class="text-xs {{ $daysLeft > 7 ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $daysLeft }} days remaining</span>
                                @endif
                            @else
                                <p class="text-4xl font-bold text-gray-400 mb-2">∞</p>
                                <p class="text-xs text-gray-500">No Expiry Date</p>
                                <span class="text-xs text-green-600 font-medium">Unlimited Access</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($isInGym && $currentGymSession)
            <!-- Current Gym Session Info -->
            <div class="mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-2 text-black">Currently in Gym</h3>
                            <p class="text-black font-medium">Check-in time: {{ $currentGymSession->check_in_time->format('g:i A') }}</p>
                            <p class="text-black font-medium">Session duration: {{ $currentGymSession->check_in_time->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Real-time update script -->
    <script>
        // Update timestamp every minute
        function updateTimestamp() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
            const timestampElement = document.getElementById('last-updated');
            if (timestampElement) {
                timestampElement.textContent = timeString;
            }
        }

        // Update every minute
        setInterval(updateTimestamp, 60000);

        // Update gym presence status indicator
        function updateGymStatus() {
            const statusIndicator = document.querySelector('.animate-pulse');
            if (statusIndicator) {
                statusIndicator.classList.remove('animate-pulse');
                setTimeout(() => {
                    statusIndicator.classList.add('animate-pulse');
                }, 100);
            }
        }

        // Update status every 5 seconds
        setInterval(updateGymStatus, 5000);

        // Auto-refresh dashboard data every 2 minutes
        setInterval(function() {
            // In a real implementation, you'd make an AJAX call to update specific data
            console.log('Dashboard data refreshed at:', new Date().toLocaleTimeString());
        }, 120000);
    </script>
</x-layout>


