<aside class="sidebar bg-white" id="sidebar">
    <div class="flex flex-col items-center h-full p-5">
        <!-- Logo/Brand - Much Smaller Size -->
        <div class="flex justify-center items-center w-full mb-4 mt-1">
            <img src="{{ asset('images/rba-logo/rba logo.png') }}" alt="RBA Logo" class="w-25 h-25 object-contain" style="filter: none !important; width: 100px !important; height: 100px !important;">
        </div>
        
        <hr class="border border-gray-300 w-full mb-4">
        
        <!-- User Profile Section - Compact Layout -->
        <div class="w-full mb-4">
            <div class="flex items-center justify-start gap-2">
                <!-- User Avatar - Smaller -->
                <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #000000 !important; stroke: #000000 !important;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                
                <!-- User Info - Compact -->
                <div>
                    <div class="text-xs font-bold uppercase tracking-wide" style="color: #000000;">
                        {{ strtoupper(auth()->user()->role ?? 'USER') }}
                    </div>
                    <div class="text-xs font-medium" style="color: #6B7280;">
                        {{ strtoupper(auth()->user()->name ?? 'UNKNOWN') }}
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="border border-gray-300 w-full mb-3">
        
        <!-- Navigation - Compact Spacing -->
        <nav class="w-full flex flex-col gap-2 flex-grow mt-1">
            <x-nav-item src="images/icons/dashboard-icon.svg" href="/dashboard">Dashboard</x-nav-item>
            
            <x-nav-item src="images/icons/members-icon.svg" href="/members">Members</x-nav-item>
            
            <x-nav-item src="images/icons/members-icon.svg" href="/membership/plans">Membership Plans</x-nav-item>
            
            <x-nav-item src="images/icons/payments-icon.svg" href="/membership/payments">All Payments</x-nav-item>
            
            <x-nav-item src="images/icons/clipboard-icon.svg" href="/membership/manage-member">Member Plans</x-nav-item>
            
            <x-nav-item src="images/icons/card-icon.svg" href="/rfid-monitor">RFID Monitor</x-nav-item>
            
            <x-nav-item src="images/icons/accounts-icon.svg" href="/accounts">Accounts</x-nav-item>
        </nav>

        <!-- Logout Section at Bottom -->
        <div class="w-full mt-auto">
            <hr class="border border-gray-300 w-full mb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link flex items-center gap-3 h-12 w-full p-3 rounded-lg">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="nav-text text-sm font-medium whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>