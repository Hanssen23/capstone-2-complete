<aside class="h-20 flex justify-between items-center px-6 py-4 bg-white border-b border-gray-300">
    <div class="flex items-center gap-4">
        <!-- Mobile Toggle Button -->
        <button class="sidebar-toggle md:hidden cursor-pointer p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <!-- Page Title with Icon -->
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/icons/dashboard-icon.svg') }}" alt="Dashboard Icon" class="w-9 h-9" style="filter: brightness(0);">
            <h1 class="text-xl font-semibold text-gray-800">{{ $slot }}</h1>
        </div>
    </div>
</aside>