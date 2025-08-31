<aside class="sidebar bg-gray-900 transition-all duration-300 ease-in-out collapsed" id="sidebar">
    <div class="flex flex-col items-center h-full p-5">
        <!-- Logo/Brand -->
        <div class="flex justify-center items-center w-full mb-2">
            <img src="{{ asset('images/rba-logo/rba logo.png') }}" alt="RBA Logo" class="w-20 h-20 object-contain">
        </div>
        
        <hr class="border border-gray-700 w-full">
        
        <!-- Navigation -->
        <nav class="w-full flex flex-col gap-3 flex-grow">
            <x-nav-item src="images/icons/dashboard-icon.svg" href="/dashboard" dataTitle="Dashboard">Dashboard</x-nav-item>
            
            <x-nav-item src="images/icons/members-icon.svg" dropdown="true" dataTitle="Members">
                Members
                <x-slot name="dropdownContent">
                    <x-nav-sub-item src="images/icons/members-icon.svg" href="/members">Members</x-nav-sub-item>
                    <x-nav-sub-item src="images/icons/members-icon.svg" href="/membership/plans">Membership Plans</x-nav-sub-item>
                </x-slot>
            </x-nav-item>
            
            <x-nav-item src="images/icons/payments-icon.svg" href="/membership/payments" dataTitle="All Payments">All Payments</x-nav-item>
            
            <x-nav-item src="images/icons/clipboard-icon.svg" href="/membership/manage-member" dataTitle="Member Plan Management">Member Plans</x-nav-item>
            
            <x-nav-item src="images/icons/card-icon.svg" href="/rfid-monitor" dataTitle="RFID Monitor">RFID Monitor</x-nav-item>
        </nav>

        <!-- Logout Section at Bottom -->
        <div class="w-full mt-auto">
            <hr class="border border-gray-700 w-full mb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link flex items-center gap-3 h-12 w-full hover:bg-gray-800 transition-all duration-200 p-3 rounded-lg group" data-title="Logout">
                    <svg class="w-6 h-6 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="nav-text text-sm text-white font-medium whitespace-nowrap">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>