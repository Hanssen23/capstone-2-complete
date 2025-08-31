<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Members</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="mb-6 p-4 bg-gray-900 border border-gray-800 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-100">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Action Buttons and Filter Container -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-800 p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('members.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Member
                        </a>
                        <div class="relative">
                            <form id="filters-form" method="GET" action="{{ route('members.index') }}" class="flex gap-3">
                                <div class="relative">
                                    <!-- remove leading icon as requested -->
                                    <!-- removed chevron as requested -->
                                    <select aria-label="Filter by membership" name="membership" onchange="document.getElementById('filters-form').submit();" class="bg-gray-800 border border-gray-500 rounded-lg pl-4 pr-4 py-2 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                                    <option class="bg-gray-900 text-white" value="" {{ empty($selectedMembership) ? 'selected' : '' }}>All Memberships</option>
                                    <option class="bg-gray-900 text-white" value="basic" {{ ($selectedMembership ?? '') === 'basic' ? 'selected' : '' }}>Basic</option>
                                    <option class="bg-gray-900 text-white" value="premium" {{ ($selectedMembership ?? '') === 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option class="bg-gray-900 text-white" value="vip" {{ ($selectedMembership ?? '') === 'vip' ? 'selected' : '' }}>VIP</option>
                                    </select>
                                </div>
                                <input type="hidden" name="search" value="{{ $search ?? '' }}" />
                            </form>
                        </div>
                    </div>
                    <div class="relative w-full sm:w-auto">
                        <form method="GET" action="{{ route('members.index') }}" class="w-full" id="members-search-form">
                            <div class="flex items-center bg-gray-800 border border-gray-600 rounded-lg px-4 py-2 hover:border-red-500 transition-colors duration-200 focus-within:border-red-500">
                                <svg aria-hidden="true" class="w-5 h-5 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input id="members-search" name="search" value="{{ $search ?? '' }}" type="text" placeholder="Filter search" class="bg-transparent outline-none text-white placeholder-gray-200 flex-1 min-w-48" aria-label="Search members">
                                @if(!empty($selectedMembership))
                                    <input type="hidden" name="membership" value="{{ $selectedMembership }}">
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Members Table -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800 text-white">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">UID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Member Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Membership</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Full Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Mobile Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-100 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800">
                            @forelse($members as $member)
                            <tr class="hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $member->uid }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $member->member_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($member->membership === 'premium')
                                        Premium
                                    @elseif($member->membership === 'vip')
                                        VIP
                                    @else
                                        Basic
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $member->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $member->mobile_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white">{{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('members.profile', $member->id) }}" class="p-2 text-yellow-500 hover:text-yellow-400 hover:bg-gray-800 rounded-lg transition-colors duration-200" title="View Profile">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('members.edit', $member->id) }}" class="p-2 text-red-600 hover:text-red-500 hover:bg-gray-800 rounded-lg transition-colors duration-200" title="Edit">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('members.destroy', $member->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:text-red-500 hover:bg-gray-800 rounded-lg transition-colors duration-200" title="Delete">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-300">
                                    No members found. <a href="{{ route('members.create') }}" class="text-red-500 hover:text-red-400">Create your first member</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($members->hasPages())
                <div class="bg-gray-900 px-4 py-3 border-t border-gray-800 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($members->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-700 text-sm font-medium rounded-md text-gray-300 bg-gray-900">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $members->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-700 text-sm font-medium rounded-md text-gray-200 bg-gray-900 hover:bg-gray-800">
                                    Previous
                                </a>
                            @endif

                            @if($members->hasMorePages())
                                <a href="{{ $members->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-700 text-sm font-medium rounded-md text-gray-200 bg-gray-900 hover:bg-gray-800">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-700 text-sm font-medium rounded-md text-gray-300 bg-gray-900">
                                    Next
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-300">
                                    Showing
                                    <span class="font-medium">{{ $members->firstItem() }}</span>
                                    to
                                    <span class="font-medium">{{ $members->lastItem() }}</span>
                                    of
                                    <span class="font-medium">{{ $members->total() }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    {{ $members->links() }}
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>