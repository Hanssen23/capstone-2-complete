<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-white">
        <x-topbar>Members</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('members.index') }}" class="flex items-center gap-2 transition-colors duration-200" style="color: #6B7280;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6B7280'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                </div>
                <h2 class="text-2xl font-bold mt-2" style="color: #1E40AF;">Edit Member: {{ $member->full_name }}</h2>
            </div>

            <!-- Edit Member Form -->
            <div class="bg-white rounded-lg border p-6" style="border-color: #E5E7EB; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg" style="background-color: #FEF2F2; border-color: #FECACA;">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5" style="color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium" style="color: #DC2626;">There were errors with your submission</h3>
                                <div class="mt-2 text-sm" style="color: #DC2626;">
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
                <form method="POST" action="{{ route('members.update', $member->id) }}" id="editMemberForm" class="space-y-8" onsubmit="return confirmSave(event);">
                    @method('PUT')
                    @csrf
                    
                    <!-- Personal Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name Field -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" name="first_name" required
                                       pattern="[A-Z][a-zA-Z\s]*"
                                       title="First name must start with a capital letter and contain only letters and spaces"
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter first name"
                                       value="{{ old('first_name', $member->first_name) }}">
                                @error('first_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Middle Name Field -->
                            <div>
                                <label for="middle_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name"
                                       pattern="[A-Z][a-zA-Z\s]*"
                                       title="Middle name must start with a capital letter and contain only letters and spaces"
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter middle name (optional)"
                                       value="{{ old('middle_name', $member->middle_name) }}">
                                @error('middle_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Last Name Field -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" name="last_name" required
                                       pattern="[A-Z][a-zA-Z\s]*"
                                       title="Last name must start with a capital letter and contain only letters and spaces"
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter last name"
                                       value="{{ old('last_name', $member->last_name) }}">
                                @error('last_name')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Age Field -->
                            <div>
                                <label for="age" class="block text-sm font-medium mb-2" style="color: #6B7280;">Age</label>
                                <input type="number" id="age" name="age" required
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter age (1-120)"
                                       value="{{ old('age', $member->age) }}"
                                       min="1"
                                       max="120">
                                @error('age')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Gender Field -->
                            <div>
                                <label for="gender" class="block text-sm font-medium mb-2" style="color: #6B7280;">Gender</label>
                                <select id="gender" name="gender" required
                                        class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        style="border-color: #E5E7EB;">
                                    <option value="">Select gender</option>
                                    <option value="Male" {{ old('gender', $member->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $member->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $member->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                    <option value="Prefer not to say" {{ old('gender', $member->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                                @error('gender')
                                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email Field (Readonly) -->
                            <div>
                                <label for="email" class="block text-sm font-medium mb-2" style="color: #6B7280;">
                                    Email
                                </label>
                                <input type="email" id="email" name="email" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed"
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Email cannot be edited"
                                       value="{{ old('email', $member->email) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">Email address cannot be changed for security reasons</p>
                            </div>

                            <!-- Mobile Number Field -->
                            <div>
                                <label for="mobile_number" class="block text-sm font-medium mb-2" style="color: #6B7280;">
                                    Mobile Number
                                </label>
                                <input type="tel" id="mobile_number" name="mobile_number" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Phone number cannot be edited"
                                       value="{{ old('mobile_number', $member->mobile_number) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Membership Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Membership Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Member Number Field (Readonly) -->
                            <div>
                                <label for="member_number" class="block text-sm font-medium mb-2" style="color: #6B7280;">Member Number</label>
                                <input type="text" id="member_number" name="member_number" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed"
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Member number cannot be edited"
                                       value="{{ old('member_number', $member->member_number) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">Member number is automatically assigned</p>
                            </div>

                            <!-- UID Field -->
                            <div>
                                <label for="uid" class="block text-sm font-medium mb-2" style="color: #6B7280;">UID</label>
                                <input type="text" id="uid" name="uid" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter UID"
                                       value="{{ old('uid', $member->uid) }}">
                            </div>

                            <!-- Membership Status Display -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Membership Status</label>
                                <div class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                     style="border-color: #E5E7EB; color: #6B7280;">
                                    @if($member->membership_status === 'Expired')
                                        {{ ucfirst($member->membership ?? 'No Plan') }} 
                                        <span class="text-red-600">(Expired)</span>
                                    @elseif($member->membership_status === 'Expiring Soon')
                                        {{ ucfirst($member->membership ?? 'No Plan') }} 
                                        <span class="text-yellow-600">(Expiring Soon)</span>
                                    @elseif($member->membership_status === 'Active')
                                        {{ ucfirst($member->membership ?? 'No Plan') }} 
                                        <span class="text-green-600">(Active)</span>
                                    @else
                                        <span class="text-gray-500">No Active Membership</span>
                                    @endif
                                </div>
                                <p class="text-xs mt-1" style="color: #6B7280;">Membership type is automatically set when payment is processed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Plan Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Current Plan -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Current Plan</label>
                                <div class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                     style="border-color: #E5E7EB; color: #6B7280;">
                                    @if($member->current_plan_type)
                                        {{ ucfirst($member->current_plan_type) }}
                                        @if($member->current_duration_type)
                                            ({{ ucfirst($member->current_duration_type) }})
                                        @endif
                                    @else
                                        <span class="text-gray-500">No Plan Assigned</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Membership Expiration -->
                            <div>
                                <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Membership Expiration</label>
                                <div class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                     style="border-color: #E5E7EB; color: #6B7280;">
                                    @if($member->membership_expires_at)
                                        {{ $member->membership_expires_at->format('M d, Y') }}
                                        @if($member->is_expired)
                                            <span class="text-red-600 ml-2">(Expired)</span>
                                        @elseif($member->days_until_expiration <= 7)
                                            <span class="text-yellow-600 ml-2">(Expires in {{ $member->days_until_expiration }} days)</span>
                                        @else
                                            <span class="text-green-600 ml-2">({{ $member->days_until_expiration }} days remaining)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500">No Expiration Date</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex items-center gap-4 pt-8">
                        <button type="submit" 
                                class="px-6 py-3 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2" 
                                style="background-color: #2563EB;" 
                                onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                onmouseout="this.style.backgroundColor='#2563EB'">
                            Save
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        <a href="{{ route('members.index') }}" 
                           class="px-6 py-3 border rounded-lg font-medium transition-colors duration-200" 
                           style="border-color: #E5E7EB; color: #6B7280;" 
                           onmouseover="this.style.backgroundColor='#F3F4F6'" 
                           onmouseout="this.style.backgroundColor='transparent'">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: #FEF3C7;">
                    <svg class="w-6 h-6" style="color: #F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold" style="color: #1E40AF;">Confirm Changes</h3>
            </div>
            <p class="mb-6" style="color: #6B7280;">Are you sure you want to save these changes to the member's information? This action cannot be undone.</p>
            <div class="flex items-center gap-3">
                <button type="button" id="confirmSaveBtn"
                        class="flex-1 px-4 py-2 text-white rounded-lg font-medium transition-colors duration-200"
                        style="background-color: #2563EB;"
                        onmouseover="this.style.backgroundColor='#1D4ED8'"
                        onmouseout="this.style.backgroundColor='#2563EB'">
                    Yes, Save Changes
                </button>
                <button type="button" id="cancelSaveBtn"
                        class="flex-1 px-4 py-2 border rounded-lg font-medium transition-colors duration-200"
                        style="border-color: #E5E7EB; color: #6B7280;"
                        onmouseover="this.style.backgroundColor='#F3F4F6'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmSave(event) {
            event.preventDefault();
            const modal = document.getElementById('confirmationModal');
            modal.style.display = 'flex';
            return false;
        }

        document.getElementById('confirmSaveBtn').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
            document.getElementById('editMemberForm').submit();
        });

        document.getElementById('cancelSaveBtn').addEventListener('click', function() {
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Close modal when clicking outside
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</x-layout>