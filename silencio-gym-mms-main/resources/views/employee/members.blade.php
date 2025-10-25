<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>All Memberships</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-white border rounded-lg" style="border-color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium" style="color: #059669;">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Header Section -->
            <div class="bg-white rounded-lg border p-8 mb-6" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div>
                        <h1 class="text-3xl font-bold mb-2" style="color: #1E40AF;">All Memberships</h1>
                        <p class="text-lg" style="color: #6B7280;">Manage member accounts and memberships</p>
                    </div>
                    <a href="/employee/members/create" 
                       class="px-6 py-3 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2" 
                       style="background-color: #2563EB;" 
                       onmouseover="this.style.backgroundColor='#1D4ED8'" 
                       onmouseout="this.style.backgroundColor='#2563EB'">
                        <span class="text-xl">➕</span>
                            Add Member
                    </a>
                </div>
                
                <!-- Filter Pills -->
                <div class="mt-6">
                    <div class="flex flex-wrap gap-3">
                        <a href="/employee/members"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ empty($selectedMembership) && empty($filter) ? 'text-white' : 'text-gray-600' }}"
                           style="background-color: {{ empty($selectedMembership) && empty($filter) ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ empty($selectedMembership) && empty($filter) ? '#1E40AF' : '#E5E7EB' }}'"
                           onmouseout="this.style.backgroundColor='{{ empty($selectedMembership) && empty($filter) ? '#1E40AF' : '#F3F4F6' }}'">
                            All
                        </a>
                        <a href="/employee/members?{{ http_build_query(array_filter(['membership' => 'basic', 'filter' => $filter ?? null])) }}"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ ($selectedMembership ?? '') === 'basic' ? 'text-white' : 'text-gray-600' }}"
                           style="background-color: {{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#E5E7EB' }}'"
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'basic' ? '#1E40AF' : '#F3F4F6' }}'">
                            Basic
                        </a>
                        <a href="/employee/members?{{ http_build_query(array_filter(['membership' => 'vip', 'filter' => $filter ?? null])) }}"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ ($selectedMembership ?? '') === 'vip' ? 'text-white' : 'text-gray-600' }}"
                           style="background-color: {{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#E5E7EB' }}'"
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'vip' ? '#1E40AF' : '#F3F4F6' }}'">
                            VIP
                        </a>
                        <a href="/employee/members?{{ http_build_query(array_filter(['membership' => 'premium', 'filter' => $filter ?? null])) }}"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ ($selectedMembership ?? '') === 'premium' ? 'text-white' : 'text-gray-600' }}"
                           style="background-color: {{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#E5E7EB' }}'"
                           onmouseout="this.style.backgroundColor='{{ ($selectedMembership ?? '') === 'premium' ? '#1E40AF' : '#F3F4F6' }}'">
                            Premium
                        </a>
                        <a href="/employee/members?{{ ($filter ?? '') === 'expired' ? http_build_query(array_filter(['membership' => $selectedMembership ?? null])) : http_build_query(array_filter(['filter' => 'expired', 'membership' => $selectedMembership ?? null])) }}"
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ ($filter ?? '') === 'expired' ? 'text-white' : 'text-gray-600' }}"
                           style="background-color: {{ ($filter ?? '') === 'expired' ? '#DC2626' : '#F3F4F6' }};"
                           onmouseover="this.style.backgroundColor='{{ ($filter ?? '') === 'expired' ? '#DC2626' : '#E5E7EB' }}'"
                           onmouseout="this.style.backgroundColor='{{ ($filter ?? '') === 'expired' ? '#DC2626' : '#F3F4F6' }}'">
                            Expired
                        </a>
                    </div>
                </div>
                
                <!-- Search Bar -->
                <div class="mt-6">
                    <form method="GET" action="/employee/members" class="w-full max-w-md">
                        <div class="relative">
                            <input name="search" value="{{ $search ?? '' }}" type="text" 
                                   placeholder="Search by name, email, or member number..." 
                                   class="w-full px-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-base" 
                                   style="border-color: #E5E7EB; color: #000000;">
                                @if(!empty($selectedMembership))
                                    <input type="hidden" name="membership" value="{{ $selectedMembership }}">
                                @endif
                            </div>
                        </form>
                </div>
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-lg border overflow-hidden" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y" style="border-color: #E5E7EB;">
                        <thead style="background-color: #1E40AF;">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">UID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">MEMBER NUMBER</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">PLAN</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">NAME</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">EMAIL</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">PHONE</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="background-color: #FFFFFF; border-color: #E5E7EB;">
                            @forelse($members as $member)
                            <tr class="hover:bg-gray-50" style="background-color: {{ $loop->even ? '#F9FAFB' : '#FFFFFF' }};">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: #000000;">
                                    <span class="truncate block max-w-24" title="{{ $member->uid }}">{{ Str::limit($member->uid, 8) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #000000;">{{ $member->member_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                            if ($durationType) {
                                                $badgeText .= ' + ' . ucfirst($durationType);
                                            }
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
                                            if ($paymentDuration) {
                                                $badgeText .= ' + ' . ucfirst($paymentDuration);
                                            }
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: #000000;">{{ $member->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #6B7280;">
                                    <span class="truncate block max-w-32" title="{{ $member->email }}">{{ Str::limit($member->email, 20) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: #6B7280;">
                                    {{ $member->mobile_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="/employee/members/{{ $member->id }}/profile" class="p-2 text-blue-600 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-colors duration-200" title="View Profile">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="/employee/members/{{ $member->id }}/edit" class="p-2 text-gray-600 hover:text-gray-500 hover:bg-gray-50 rounded-lg transition-colors duration-200" title="Edit">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <!-- Delete button removed for employees -->
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center" style="color: #6B7280;">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">👥</div>
                                        <p class="text-lg font-medium mb-2" style="color: #000000;">No members found</p>
                                        <p class="mb-4">Get started by adding your first member.</p>
                                        <a href="/employee/members/create" 
                                           class="px-6 py-3 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2" 
                                           style="background-color: #2563EB;" 
                                           onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                           onmouseout="this.style.backgroundColor='#2563EB'">
                                            <span class="text-xl">➕</span>
                                            Add Member
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($members->hasPages())
                <div class="bg-white px-6 py-4 border-t" style="border-color: #E5E7EB;">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($members->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md" style="border-color: #E5E7EB; color: #6B7280;">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $members->previousPageUrl() }}" 
                                   class="relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md transition-colors" 
                                   style="border-color: #E5E7EB; color: #6B7280;" 
                                   onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                    Previous
                                </a>
                            @endif

                            @if($members->hasMorePages())
                                <a href="{{ $members->nextPageUrl() }}" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md transition-colors" 
                                   style="border-color: #E5E7EB; color: #6B7280;" 
                                   onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                   onmouseout="this.style.backgroundColor='transparent'">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md" style="border-color: #E5E7EB; color: #6B7280;">
                                    Next
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm" style="color: #6B7280;">
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

    <!-- Delete functionality removed for employees -->

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
    </script>
</x-layout>