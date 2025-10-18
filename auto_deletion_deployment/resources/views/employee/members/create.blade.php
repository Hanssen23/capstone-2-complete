<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>Create New Member</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6 sticky top-20 z-10 -mx-6 px-6 py-3 bg-white/90 backdrop-blur border-b border-gray-200">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('employee.members.index') }}" class="flex items-center gap-2 text-black hover:text-blue-600 transition-colors duration-200">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                    <h2 class="text-xl md:text-2xl font-bold text-black">Create New Member</h2>
                </div>
            </div>

            <!-- Create Member Form -->
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
                <form method="POST" action="{{ route('employee.members.store') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Auto-generated fields info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-700">
                                Member Number and UID will be automatically generated
                            </p>
                        </div>
                    </div>

                    <!-- Subscription Info -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">No Subscription Plan Assigned</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    Members will need to select and pay for a plan after registration to access paid features.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- First Name Field -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter first name (must start with capital letter)"
                               value="{{ old('first_name') }}"
                               pattern="^[A-Z][a-zA-Z\s]*$"
                               title="First name must start with a capital letter and contain only letters and spaces">
                        @error('first_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Middle Name Field -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                        <input type="text" id="middle_name" name="middle_name"
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter middle name (must start with capital letter)"
                               value="{{ old('middle_name') }}"
                               pattern="^[A-Z][a-zA-Z\s]*$"
                               title="Middle name must start with a capital letter and contain only letters and spaces">
                        @error('middle_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Last Name Field -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter last name (must start with capital letter)"
                               value="{{ old('last_name') }}"
                               pattern="^[A-Z][a-zA-Z\s]*$"
                               title="Last name must start with a capital letter and contain only letters and spaces">
                        @error('last_name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Age Field -->
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                        <input type="number" id="age" name="age" required
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter age (1-120)"
                               value="{{ old('age') }}"
                               min="1"
                               max="120">
                        @error('age')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Gender Field -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select id="gender" name="gender" required
                                class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="">Select gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                        @error('gender')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mobile Number Field -->
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" required 
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="e.g., +63 917 123 4567"
                               value="{{ old('mobile_number') }}">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter email address"
                               value="{{ old('email') }}">
                    </div>

                    <!-- Password Field (Optional for employee-created accounts) -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password (Optional)</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Leave blank to let user set password later"
                               value="{{ old('password') }}">
                        <p class="text-xs text-gray-500 mt-1">If provided, user can login immediately. If left blank, user will need to set password later.</p>
                    </div>

                    <!-- Password Confirmation Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Confirm password"
                               value="{{ old('password_confirmation') }}">
                    </div>

                    <!-- Role Field (Employee can only create members) -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                        <select id="role" name="role" 
                                class="w-full px-4 py-2 border border-gray-300 bg-white text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Employees can only create member accounts.</p>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                                Create Member
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        <a href="{{ route('employee.members.index') }}" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
