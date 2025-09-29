<aside class="sidebar bg-white" id="sidebar">
    <div class="flex flex-col items-center h-full p-3 lg:p-5">
        <!-- Logo/Brand - Responsive Size -->
        <div class="flex justify-center items-center w-full mb-4 mt-1">
            <img src="{{ asset('images/rba-logo/rba logo.png') }}" alt="RBA Logo" class="w-16 h-16 sm:w-20 sm:h-20 lg:w-25 lg:h-25 object-contain" style="filter: none !important;">
        </div>
        
        <hr class="border border-gray-300 w-full mb-4">
        
        <!-- User Profile Section - Responsive Layout -->
        <div class="w-full mb-4">
            <div class="flex items-center justify-start gap-2">
                <!-- User Avatar - Responsive -->
                <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-200 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #000000 !important; stroke: #000000 !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                
                <!-- User Info - Responsive -->
                <div class="text-left">
                    @php
                        $user = auth()->user();
                        $userRole = $user->role ?? 'member';
                        $userName = '';
                        
                        if ($userRole === 'member') {
                            $userName = ($user->first_name ?? '') . ' ' . ($user->last_name ?? '');
                        } else {
                            $userName = $user->name ?? 'User';
                        }
                        
                        $roleDisplay = match($userRole) {
                            'admin' => 'ADMIN',
                            'employee' => 'EMPLOYEE', 
                            'member' => 'MEMBER',
                            default => 'USER'
                        };
                    @endphp
                    
                    <div class="text-xs sm:text-sm font-bold uppercase tracking-wide" style="color: #1E40AF;">
                        {{ $roleDisplay }}
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-wrap max-w-20 sm:max-w-none" style="color: #374151;" title="{{ trim($userName) }}">
                        {{ trim($userName) }}
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="border border-gray-300 w-full mb-3">
        
        <!-- Navigation - Responsive Spacing -->
        <nav class="w-full flex flex-col gap-1 sm:gap-2 flex-grow mt-1">
            <x-nav-item src="images/icons/dashboard-icon.svg" href="/dashboard">Dashboard</x-nav-item>
            
            <x-nav-item src="images/icons/members-icon.svg" href="/members">Members</x-nav-item>
            
            <x-nav-item src="images/icons/members-icon.svg" href="/membership/plans">Membership Plans</x-nav-item>
            
            <x-nav-item src="images/icons/payments-icon.svg" href="/membership/payments">All Payments</x-nav-item>
            
            <x-nav-item src="images/icons/clipboard-icon.svg" href="/membership/manage-member">Member Plans</x-nav-item>
            
            <x-nav-item src="images/icons/card-icon.svg" href="/rfid-monitor">RFID Monitor</x-nav-item>
            
            <x-nav-item src="images/icons/accounts-icon.svg" href="/accounts">Accounts</x-nav-item>
            
            <!-- Logout Button - Inline with other nav items -->
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="nav-link flex items-center gap-2 sm:gap-3 h-10 sm:h-12 w-full p-2 sm:p-3 rounded-lg mt-4" style="color: #000000;">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #000000;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="nav-text text-xs sm:text-sm font-medium whitespace-nowrap" style="color: #000000;">Logout</span>
                </button>
            </form>
        </nav>

    </div>
</aside>