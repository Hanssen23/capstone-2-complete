<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Members</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('members.index') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Member: {{ $member->full_name }}</h2>
            </div>

            <!-- Edit Member Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('members.update', $member->id) }}" class="space-y-6">
                    @method('PUT')
                    @csrf
                    
                    <!-- Member Number Field -->
                    <div>
                        <label for="member_number" class="block text-sm font-medium text-gray-700 mb-2">Member Number</label>
                        <input type="text" id="member_number" name="member_number" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter member number"
                               value="{{ old('member_number', $member->member_number) }}">
                    </div>

                    <!-- UID Field -->
                    <div>
                        <label for="uid" class="block text-sm font-medium text-gray-700 mb-2">UID</label>
                        <input type="text" id="uid" name="uid" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter UID"
                               value="{{ old('uid', $member->uid) }}">
                    </div>

                    <!-- Membership Field -->
                    <div>
                        <label for="membership" class="block text-sm font-medium text-gray-700 mb-2">Membership</label>
                        <select id="membership" name="membership" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select Membership Type</option>
                            <option value="basic" {{ old('membership', $member->membership) == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('membership', $member->membership) == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="vip" {{ old('membership', $member->membership) == 'vip' ? 'selected' : '' }}>VIP</option>
                        </select>
                    </div>

                    <!-- First Name Field -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter first name"
                               value="{{ old('first_name', $member->first_name) }}">
                    </div>

                    <!-- Last Name Field -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter last name"
                               value="{{ old('last_name', $member->last_name) }}">
                    </div>

                    <!-- Mobile Number Field -->
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="e.g., +63 917 123 4567"
                               value="{{ old('mobile_number', $member->mobile_number) }}">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               placeholder="Enter email address"
                               value="{{ old('email', $member->email) }}">
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                                Save
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        <a href="{{ route('members.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>