<aside class="sticky top-0 z-50 h-16 sm:h-20 flex justify-between items-center px-4 sm:px-6 py-3 sm:py-4 bg-white border-b border-gray-300 shadow-sm">
    <div class="flex items-center gap-2 sm:gap-4">
        <!-- Mobile Toggle Button -->
        <button class="sidebar-toggle lg:hidden cursor-pointer p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <!-- Page Title with Icon -->
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/icons/dashboard-icon.svg') }}" alt="Dashboard Icon" class="w-7 h-7 sm:w-9 sm:h-9" style="filter: brightness(0);">
            <h1 class="text-lg sm:text-xl font-semibold text-gray-800">{{ $slot }}</h1>
        </div>
    </div>
</aside>