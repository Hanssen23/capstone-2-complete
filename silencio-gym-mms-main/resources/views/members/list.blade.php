<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-white">
        <x-topbar>All Memberships</x-topbar>

        <!-- Main Content -->
        <div class="p-4 sm:p-6 stable-layout resize-handler">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-white border rounded-lg" style="border-color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-lg sm:text-xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" style="color: #059669;">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Header Section -->
            <div class="bg-white rounded-lg border p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 sm:gap-6">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold mb-2" style="color: #1E40AF;">All Memberships</h1>
                        <p class="text-base sm:text-lg" style="color: #6B7280;">Manage member accounts and memberships</p>
                    </div>
                    <!-- Add Member Button Removed - Members can only self-register -->
                </div>
                
                <!-- Filter Pills -->
                <div class="mt-4 sm:mt-6">
                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        <a href="{{ route('members.index') }}" 
                           class="px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition-colors min-h-[44px] flex items-center justify-center {{ empty($selectedMembership) ? 'text-white' : 'text-gray-600' }}" 
                           style="background-color: {{ empty($selectedMembership) ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ empty($selectedMembership) ? '#1E40AF' : '#E5E7EB' }}'" 
                           onmouseout="this.style.backgroundColor='{{ empty($selectedMembership) ? '#1E40AF' : '#F3F4F6' }}'">
                            All
                        </a>
                        <a href="{{ route('members.index', ['membership' => 'basic']) }}" 
                           class="px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition-colors min-h-[44px] flex items-center justify-center {{ ($selectedMembership ?? '') === 'basic' ? 'text-white' : 'text-gray-600' }}" 
                           style="background-color: {{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#E5E7EB' }}'" 
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#F3F4F6' }}'">
                            Basic
                        </a>
                        <a href="{{ route('members.index', ['membership' => 'vip']) }}" 
                           class="px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition-colors min-h-[44px] flex items-center justify-center {{ ($selectedMembership ?? '') === 'vip' ? 'text-white' : 'text-gray-600' }}" 
                           style="background-color: {{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#E5E7EB' }}'" 
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#F3F4F6' }}'">
                            VIP
                        </a>
                        <a href="{{ route('members.index', ['membership' => 'premium']) }}" 
                           class="px-3 sm:px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition-colors min-h-[44px] flex items-center justify-center {{ ($selectedMembership ?? '') === 'premium' ? 'text-white' : 'text-gray-600' }}" 
                           style="background-color: {{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#E5E7EB' }}'" 
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#F3F4F6' }}'">
                            Premium
                        </a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <x-members-search :action="route('members.index')" :search="$search ?? ''" :selectedMembership="$selectedMembership ?? ''" />
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-lg border overflow-hidden members-table-container" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y members-table" style="border-color: #E5E7EB; min-width: 1000px; table-layout: fixed;">
                        <thead style="background-color: #1E40AF;">
                            <tr>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 120px; min-width: 120px;">UID</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 120px; min-width: 120px;">MEMBER NUMBER</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 100px; min-width: 100px;">MEMBERSHIP</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 150px; min-width: 150px;">FULL NAME</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 150px; min-width: 150px;">MOBILE NUMBER</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 200px; min-width: 200px;">EMAIL</th>
                                <th class="px-2 sm:px-3 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-white uppercase tracking-wider" style="width: 100px; min-width: 100px;">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="background-color: #FFFFFF; border-color: #E5E7EB;">
                            @forelse($members as $member)
                            <tr class="hover:bg-gray-50" style="background-color: {{ $loop->even ? '#F9FAFB' : '#FFFFFF' }};">
                                <!-- UID Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm font-medium" style="color: #000000;">
                                    {{ $member->uid }}
                                </td>
                                
                                <!-- MEMBER NUMBER Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm" style="color: #000000;">
                                    MEM{{ str_pad($member->id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                
                                <!-- MEMBERSHIP Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm">
                                    @php
                                        $currentPlan = $member->currentMembershipPeriod ? $member->currentMembershipPeriod->plan_type : null;
                                        $isActive = $member->currentMembershipPeriod && $member->currentMembershipPeriod->is_active;
                                        $hasPayments = $member->payments()->where('status', 'completed')->exists();
                                        $planFromPayments = $member->payments()->where('status', 'completed')->latest()->first();
                                    @endphp
                                    
                                    @if($currentPlan && $isActive)
                                        @php
                                            // Normalize plan type to lowercase for consistent color mapping
                                            $normalizedPlan = strtolower($currentPlan);
                                            $durationType = $member->currentMembershipPeriod ? $member->currentMembershipPeriod->duration_type : null;
                                            $planColors = [
                                                'basic' => '#059669',
                                                'vip' => '#F59E0B', 
                                                'premium' => '#F59E0B'
                                            ];
                                            $planColor = $planColors[$normalizedPlan] ?? '#059669';
                                            
                                            // Create combined text for the badge
                                            $badgeText = ucfirst($currentPlan);
                                        @endphp
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" 
                                              style="background-color: {{ $planColor }};">
                                            {{ $badgeText }}
                                        </span>
                                    @elseif($hasPayments && $planFromPayments)
                                        @php
                                            // Show plan from latest payment if no active membership period
                                            $paymentPlan = $planFromPayments->plan_type;
                                            $paymentDuration = $planFromPayments->duration_type;
                                            $normalizedPlan = strtolower($paymentPlan);
                                            $planColors = [
                                                'basic' => '#059669',
                                                'vip' => '#F59E0B', 
                                                'premium' => '#F59E0B'
                                            ];
                                            $planColor = $planColors[$normalizedPlan] ?? '#059669';
                                            
                                            // Create combined text for the badge
                                            $badgeText = ucfirst($paymentPlan);
                                        @endphp
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" 
                                              style="background-color: {{ $planColor }};">
                                            {{ $badgeText }}
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-gray-600 bg-gray-200">
                                            Not Subscribed
                                        </span>
                                    @endif
                                </td>
                                
                                <!-- FULL NAME Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm font-medium" style="color: #000000;">
                                    {{ $member->first_name }} {{ $member->last_name }}
                                </td>
                                
                                <!-- MOBILE NUMBER Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm" style="color: #000000;">
                                    @if($member->mobile_number)
                                        @php
                                            // Remove any existing +63 prefix and clean the number
                                            $cleanNumber = preg_replace('/^\+63\s*/', '', $member->mobile_number);
                                            $cleanNumber = preg_replace('/\D/', '', $cleanNumber); // Remove non-digits
                                            
                                            // Format as +63 XXX XXX XXXX
                                            if (strlen($cleanNumber) >= 10) {
                                                $formatted = '+63 ' . substr($cleanNumber, 0, 3) . ' ' . substr($cleanNumber, 3, 3) . ' ' . substr($cleanNumber, 6);
                                            } else {
                                                $formatted = '+63 ' . $cleanNumber;
                                            }
                                        @endphp
                                        {{ $formatted }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                
                                <!-- EMAIL Column -->
                                <td class="px-2 sm:px-3 py-2 sm:py-3 text-xs sm:text-sm" style="color: #000000; overflow: hidden; text-overflow: ellipsis;">
                                    <span title="{{ $member->email }}">{{ $member->email }}</span>
                                </td>
                                <td class="px-2 sm:px-3 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm font-medium">
                                    <div class="flex items-center gap-2 action-buttons">
                                        <a href="{{ route('members.profile', $member->id) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200" title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('members.edit', $member->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200" title="Edit Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <button onclick="deleteMember({{ $member->id }}, '{{ $member->first_name }} {{ $member->last_name }}', '{{ $member->membership_expires_at ? $member->membership_expires_at->toIso8601String() : '' }}')" class="text-red-600 hover:text-red-900 transition-colors duration-200" title="Delete Member">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center" style="color: #6B7280;">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">👥</div>
                                        <p class="text-lg font-medium mb-2" style="color: #000000;">No members found</p>
                                        <p class="mb-4">Members can register through the public registration page.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($members->hasPages())
                <div class="bg-white px-2 sm:px-4 lg:px-6 py-2 sm:py-3 lg:py-4 border-t" style="border-color: #E5E7EB;">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($members->onFirstPage())
                                <span class="relative inline-flex items-center px-2 sm:px-4 py-2 border text-xs sm:text-sm font-medium rounded-md" style="border-color: #E5E7EB; color: #6B7280;">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $members->previousPageUrl() }}" 
                                   class="relative inline-flex items-center px-2 sm:px-4 py-2 border text-xs sm:text-sm font-medium rounded-md transition-colors" 
                                   style="border-color: #E5E7EB; color: #6B7280;" 
                                   onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                    Previous
                                </a>
                            @endif

                            @if($members->hasMorePages())
                                <a href="{{ $members->nextPageUrl() }}" 
                                   class="ml-3 relative inline-flex items-center px-2 sm:px-4 py-2 border text-xs sm:text-sm font-medium rounded-md transition-colors" 
                                   style="border-color: #E5E7EB; color: #6B7280;" 
                                   onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-2 sm:px-4 py-2 border text-xs sm:text-sm font-medium rounded-md" style="border-color: #E5E7EB; color: #6B7280;">
                                    Next
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-xs sm:text-sm" style="color: #6B7280;">
                                    Showing
                                    <span class="font-medium" style="color: #000000;">{{ $members->firstItem() }}</span>
                                    to
                                    <span class="font-medium" style="color: #000000;">{{ $members->lastItem() }}</span>
                                    of
                                    <span class="font-medium" style="color: #000000;">{{ $members->total() }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                    {{ $members->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Modal Component -->
    <x-member-delete-modal />

    <script>

        // Auto-refresh members list every 30 seconds to ensure real-time updates
        let refreshInterval;
        
        function startAutoRefresh() {
            // Clear any existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            
            // Set up new interval for auto-refresh
            refreshInterval = setInterval(function() {
                console.log('Auto-refreshing members list...');
                window.location.reload();
            }, 30000); // Refresh every 30 seconds
        }
        
        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRefresh();
            
            // Stop auto-refresh when user is interacting with the page
            let userActivityTimeout;
            document.addEventListener('mousemove', function() {
                stopAutoRefresh();
                clearTimeout(userActivityTimeout);
                userActivityTimeout = setTimeout(function() {
                    startAutoRefresh();
                }, 60000); // Restart auto-refresh 1 minute after user stops interacting
            });
        });
        
        // Clean up interval when page is unloaded
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
        });

        // Window resize handling for zoom responsiveness
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Trigger layout recalculation
                document.body.style.overflow = 'hidden';
                setTimeout(function() {
                    document.body.style.overflow = '';
                }, 10);
            }, 150);
        });

        // Handle zoom level changes
        let lastZoomLevel = window.devicePixelRatio;
        window.addEventListener('resize', function() {
            const currentZoomLevel = window.devicePixelRatio;
            if (Math.abs(currentZoomLevel - lastZoomLevel) > 0.1) {
                lastZoomLevel = currentZoomLevel;
                // Force reflow to handle zoom changes
                document.body.style.transform = 'scale(1)';
                setTimeout(function() {
                    document.body.style.transform = '';
                }, 10);
            }
        });

        // Ensure table responsiveness on load
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('.members-table');
            if (table) {
                // Set initial table width
                const containerWidth = document.querySelector('.members-table-container').offsetWidth;
                if (containerWidth < 1000) {
                    table.style.minWidth = Math.max(containerWidth, 500) + 'px';
                }
            }
        });
    </script>

    <style>
        /* Members table container responsiveness */
        .members-table-container {
            width: 100%;
            overflow-x: auto;
            min-height: 400px;
        }
        
        /* Members table responsiveness */
        .members-table {
            min-width: 100%;
            table-layout: fixed;
            width: 100%;
        }
        
        /* Define column widths to prevent distortion */
        .members-table th:nth-child(1),
        .members-table td:nth-child(1) {
            width: 120px;
            min-width: 120px;
        }
        
        .members-table th:nth-child(2),
        .members-table td:nth-child(2) {
            width: 120px;
            min-width: 120px;
        }
        
        .members-table th:nth-child(3),
        .members-table td:nth-child(3) {
            width: 100px;
            min-width: 100px;
        }
        
        .members-table th:nth-child(4),
        .members-table td:nth-child(4) {
            width: 150px;
            min-width: 150px;
        }
        
        .members-table th:nth-child(5),
        .members-table td:nth-child(5) {
            width: 150px;
            min-width: 150px;
        }
        
        .members-table th:nth-child(6),
        .members-table td:nth-child(6) {
            width: 200px;
            min-width: 200px;
        }
        
        .members-table th:nth-child(7),
        .members-table td:nth-child(7) {
            width: 100px;
            min-width: 100px;
        }
        
        /* Responsive table for different zoom levels and window sizes */
        @media (max-width: 1400px) {
            .members-table {
                min-width: 900px;
            }
        }
        
        @media (max-width: 1200px) {
            .members-table {
                min-width: 800px;
            }
        }
        
        @media (max-width: 1024px) {
            .members-table {
                min-width: 700px;
            }
        }
        
        @media (max-width: 768px) {
            .members-table {
                min-width: 600px;
            }
        }
        
        @media (max-width: 640px) {
            .members-table {
                min-width: 500px;
            }
        }
        
        /* Ensure proper column widths and prevent squishing */
        .members-table th,
        .members-table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0.5rem 0.75rem;
        }
        
        /* Responsive text sizing */
        @media (max-width: 1024px) {
            .members-table th,
            .members-table td {
                font-size: 0.875rem;
                padding: 0.5rem 0.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .members-table th,
            .members-table td {
                font-size: 0.75rem;
                padding: 0.375rem 0.5rem;
            }
        }
        
        @media (max-width: 640px) {
            .members-table th,
            .members-table td {
                font-size: 0.7rem;
                padding: 0.25rem 0.375rem;
            }
        }
        
        /* Action buttons responsiveness */
        .action-buttons {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
            align-items: center;
            justify-content: flex-start;
            min-width: 80px;
        }
        
        .action-buttons a,
        .action-buttons button {
            min-width: 28px;
            min-height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        .action-buttons a:hover,
        .action-buttons button:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: scale(1.05);
        }
        
        /* Zoom level adjustments */
        @media (min-resolution: 1.25dppx) {
            .members-table th,
            .members-table td {
                font-size: 0.9rem;
            }
        }
        
        @media (min-resolution: 1.5dppx) {
            .members-table th,
            .members-table td {
                font-size: 0.85rem;
            }
        }
        
        /* High DPI display adjustments */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .members-table th,
            .members-table td {
                padding: 0.6rem 0.8rem;
            }
        }
        
        /* Window resize handling */
        .resize-handler {
            transition: all 0.3s ease;
        }
        
        /* Prevent content jumping during resize */
        .stable-layout {
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Smooth transitions for all interactive elements */
        * {
            transition: all 0.2s ease;
        }
        
        /* Focus states for accessibility */
        .members-table button:focus,
        .members-table a:focus {
            outline: 2px solid #2563EB;
            outline-offset: 2px;
        }
        
        /* Print styles */
        @media print {
            .members-table {
                overflow: visible;
            }

            .members-table table {
                min-width: auto;
                width: 100%;
            }

            .action-buttons {
                display: none;
            }
        }
    </style>

    <script>
        // ============================================
        // REAL-TIME SEARCH FILTERING (NO PAGE REFRESH)
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('membersSearchInput');
            const searchForm = document.getElementById('membersSearchForm');
            const tableRows = document.querySelectorAll('.members-table tbody tr');
            const filterPills = document.querySelectorAll('a[href*="membership="]');

            // Prevent form submission (we're doing client-side filtering)
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    applyFilters();
                });
            }

            // Add event listener for real-time search
            if (searchInput) {
                searchInput.addEventListener('input', debounce(applyFilters, 300));
            }

            // Make filter pills work with client-side filtering
            filterPills.forEach(pill => {
                pill.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const membership = url.searchParams.get('membership');
                    applyFilters(membership);

                    // Update active pill styling
                    filterPills.forEach(p => {
                        p.classList.remove('text-white');
                        p.classList.add('text-gray-600');
                        p.style.backgroundColor = '#F3F4F6';
                    });
                    this.classList.remove('text-gray-600');
                    this.classList.add('text-white');
                    this.style.backgroundColor = '#1E40AF';
                });
            });

            function applyFilters(membershipFilter = null) {
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

                // Get current membership filter from URL or parameter
                const urlParams = new URLSearchParams(window.location.search);
                const currentMembership = membershipFilter !== null ? membershipFilter : urlParams.get('membership');

                let visibleCount = 0;

                tableRows.forEach(row => {
                    // Skip empty state row
                    if (row.querySelector('td[colspan]')) {
                        return;
                    }

                    // Get row data
                    const uid = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                    const memberNumber = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    const membership = row.querySelector('td:nth-child(3) span')?.textContent.toLowerCase() || '';
                    const fullName = row.querySelector('td:nth-child(4)')?.textContent.toLowerCase() || '';
                    const mobile = row.querySelector('td:nth-child(5)')?.textContent.toLowerCase() || '';
                    const email = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';

                    // Apply filters
                    let showRow = true;

                    // Search filter
                    if (searchTerm) {
                        const searchableText = `${uid} ${memberNumber} ${fullName} ${mobile} ${email}`;
                        if (!searchableText.includes(searchTerm)) {
                            showRow = false;
                        }
                    }

                    // Membership filter
                    if (currentMembership && currentMembership !== '') {
                        if (!membership.includes(currentMembership.toLowerCase())) {
                            showRow = false;
                        }
                    }

                    // Show/hide row
                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show "no results" message if no rows visible
                updateNoResultsMessage(visibleCount);
            }

            function updateNoResultsMessage(visibleCount) {
                const tbody = document.querySelector('.members-table tbody');
                let noResultsRow = tbody.querySelector('.no-results-row');

                if (visibleCount === 0) {
                    if (!noResultsRow) {
                        noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = `
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="text-6xl mb-4">👥</div>
                                    <p class="text-lg font-medium mb-2" style="color: #000000;">No members found</p>
                                    <p class="mb-4">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(noResultsRow);
                    }
                    noResultsRow.style.display = '';
                } else {
                    if (noResultsRow) {
                        noResultsRow.style.display = 'none';
                    }
                }
            }

            // Debounce function to limit how often filtering runs
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        });
    </script>
</x-layout>