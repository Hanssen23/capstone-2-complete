<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>Members</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('employee.members.index') }}" class="flex items-center gap-2 transition-colors duration-200" style="color: #6B7280;" onmouseover="this.style.color='#000000'" onmouseout="this.style.color='#6B7280'">
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
                <form id="edit-member-form" method="POST" action="{{ route('employee.members.update', $member->id) }}" class="space-y-8">
                    @method('PUT')
                    @csrf
                    
                    <!-- Personal Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name Field -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">First Name</label>
                                <input type="text" id="first_name" name="first_name" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter first name"
                                       value="{{ old('first_name', $member->first_name) }}">
                            </div>

                            <!-- Last Name Field -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required 
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200" 
                                       style="border-color: #E5E7EB;"
                                       placeholder="Enter last name"
                                       value="{{ old('last_name', $member->last_name) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium mb-2" style="color: #6B7280;">
                                    Email
                                </label>
                                <input type="email" id="email" name="email" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Email cannot be edited"
                                       value="{{ old('email', $member->email) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">Email cannot be modified for security reasons</p>
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
                                <p class="text-xs mt-1" style="color: #6B7280;">Phone number cannot be modified by employees</p>
                            </div>
                        </div>
                    </div>

                    <!-- Membership Information Section -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Membership Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Member Number Field -->
                            <div>
                                <label for="member_number" class="block text-sm font-medium mb-2" style="color: #6B7280;">Member Number</label>
                                <input type="text" id="member_number" name="member_number" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Member number cannot be edited"
                                       value="{{ old('member_number', $member->member_number) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">Member number cannot be modified by employees</p>
                            </div>

                            <!-- UID Field -->
                            <div>
                                <label for="uid" class="block text-sm font-medium mb-2" style="color: #6B7280;">UID</label>
                                <input type="text" id="uid" name="uid" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="UID cannot be edited"
                                       value="{{ old('uid', $member->uid) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">UID cannot be modified by employees</p>
                            </div>

                            <!-- Membership Field -->
                            <div>
                                <label for="membership" class="block text-sm font-medium mb-2" style="color: #6B7280;">Membership</label>
                                <input type="text" id="membership" name="membership" readonly
                                       class="w-full px-4 py-3 border rounded-lg bg-gray-100 cursor-not-allowed" 
                                       style="border-color: #E5E7EB; color: #6B7280;"
                                       placeholder="Membership cannot be edited"
                                       value="{{ old('membership', ucfirst($member->membership)) }}">
                                <p class="text-xs mt-1" style="color: #6B7280;">Membership cannot be modified by employees</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex items-center gap-4 pt-8">
                        <button type="button"
                                onclick="showConfirmationModal()"
                                class="px-6 py-3 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                                style="background-color: #2563EB;"
                                onmouseover="this.style.backgroundColor='#1D4ED8'"
                                onmouseout="this.style.backgroundColor='#2563EB'">
                            Save
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        <a href="{{ route('employee.members.index') }}" 
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
    <div id="confirmation-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mt-4">Confirm Changes</h3>
                <div class="mt-4 px-4">
                    <p class="text-sm text-gray-600 text-center mb-4">Are you sure you want to save these changes to the member's information?</p>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm">
                        <p class="font-semibold text-gray-700 mb-2">Changes Summary:</p>
                        <ul class="space-y-1 text-gray-600">
                            <li><strong>Name:</strong> <span id="confirm-name"></span></li>
                            <li><strong>Age:</strong> <span id="confirm-age"></span></li>
                            <li><strong>Gender:</strong> <span id="confirm-gender"></span></li>
                            <li><strong>Mobile:</strong> <span id="confirm-mobile"></span></li>
                        </ul>
                    </div>
                </div>
                <div class="flex gap-4 mt-6 px-4 pb-4">
                    <button onclick="closeConfirmationModal()"
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmSave()"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Confirm & Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showConfirmationModal() {
            // Get form values
            const firstName = document.getElementById('first_name').value;
            const middleName = document.getElementById('middle_name').value;
            const lastName = document.getElementById('last_name').value;
            const age = document.getElementById('age').value;
            const gender = document.getElementById('gender').value;
            const mobile = document.getElementById('mobile_number').value;

            // Validate required fields
            if (!firstName || !lastName || !age || !gender || !mobile) {
                alert('Please fill in all required fields before saving.');
                return;
            }

            // Update confirmation modal
            const fullName = `${firstName} ${middleName ? middleName + ' ' : ''}${lastName}`;
            document.getElementById('confirm-name').textContent = fullName;
            document.getElementById('confirm-age').textContent = age;
            document.getElementById('confirm-gender').textContent = gender;
            document.getElementById('confirm-mobile').textContent = mobile;

            // Show modal
            document.getElementById('confirmation-modal').classList.remove('hidden');
        }

        function closeConfirmationModal() {
            document.getElementById('confirmation-modal').classList.add('hidden');
        }

        function confirmSave() {
            // Submit the form
            document.getElementById('edit-member-form').submit();
        }
    </script>
</x-layout>