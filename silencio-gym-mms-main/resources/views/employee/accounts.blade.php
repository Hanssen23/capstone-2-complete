<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>My Account</x-topbar>

        <div class="bg-white p-6">
            <!-- My Account Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-2xl font-bold mb-6" style="color: #1E40AF;">Edit My Account</h2>
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('employee.accounts.update', auth()->user()->id) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">First Name</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name', auth()->user()->first_name) }}" required
                                       class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                       placeholder="Enter first name">
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="middle_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">Middle Name</label>
                                <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', auth()->user()->middle_name) }}"
                                       class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                       placeholder="Enter middle name (optional)">
                                @error('middle_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium mb-2" style="color: #6B7280;">Last Name</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name', auth()->user()->last_name) }}" required
                                       class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                       placeholder="Enter last name">
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Age -->
                            <div>
                                <label for="age" class="block text-sm font-medium mb-2" style="color: #6B7280;">Age</label>
                                <input type="number" id="age" name="age" value="{{ old('age', auth()->user()->age) }}" min="1" max="120"
                                       class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                       placeholder="Enter age">
                                @error('age')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Gender (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Gender</label>
                            <div class="w-full px-3 py-3 border rounded-md bg-gray-100" style="border-color: #E5E7EB; color: #6B7280;">
                                {{ auth()->user()->gender ?: 'Not specified' }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Gender cannot be changed. Please contact an administrator.</p>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2" style="color: #6B7280;">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                   placeholder="Enter email address">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Mobile Number -->
                        <div>
                            <label for="mobile_number" class="block text-sm font-medium mb-2" style="color: #6B7280;">Mobile Number</label>
                            <div class="phone-input-container">
                                <img src="https://flagcdn.com/w40/ph.png" alt="Philippines" class="flag-icon w-6 h-4">
                                <span class="country-code">+63</span>
                                <div class="separator-line"></div>
                                <input type="tel" id="mobile_number" name="mobile_number" 
                                       class="phone-input"
                                       placeholder="912 345 6789" 
                                       maxlength="13">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter your 10-digit mobile number (e.g., 912 345 6789)</p>
                            @error('mobile_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium mb-2" style="color: #6B7280;">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password"
                                   class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                   placeholder="Enter new password (optional)">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Password Confirmation -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium mb-2" style="color: #6B7280;">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3 py-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   style="border-color: #E5E7EB; background-color: #F9FAFB; color: #000000;"
                                   placeholder="Confirm new password">
                                </div>
                        
                        <!-- Role Display (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Account Type</label>
                            <div class="w-full px-3 py-3 border rounded-md bg-gray-100" style="border-color: #E5E7EB; color: #6B7280;">
                                {{ ucfirst(auth()->user()->role) }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Account type cannot be changed</p>
                        </div>
                        
                        <!-- Account Status Display (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Account Status</label>
                            <div class="w-full px-3 py-3 border rounded-md bg-gray-100" style="border-color: #E5E7EB; color: #6B7280;">
                                @if(auth()->user()->email_verified_at)
                                    <span class="text-green-600 font-medium">Active</span>
                                @else
                                    <span class="text-red-600 font-medium">Inactive</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Account status cannot be changed</p>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 text-white rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                                    style="background-color: #2563EB;" 
                                    onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                    onmouseout="this.style.backgroundColor='#2563EB'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                Update Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h3 class="text-xl font-bold mb-6" style="color: #1E40AF;">Account Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Full Name</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->first_name }} {{ auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '' }}{{ auth()->user()->last_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Email Address</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->email }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Mobile Number</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->mobile_number ?: 'Not provided' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Age</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->age ?: 'Not provided' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Gender</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->gender ?: 'Not specified' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Account Created</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Last Updated</label>
                            <div class="px-3 py-2 bg-gray-50 border rounded-md" style="border-color: #E5E7EB;">
                                {{ auth()->user()->updated_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Phone Input Container Styles */
        .phone-input-container {
            position: relative;
            display: flex;
            align-items: center;
            background-color: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .phone-input-container:focus-within {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .phone-input-container .flag-icon {
            width: 24px;
            height: 16px;
            border-radius: 2px;
            margin-right: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .phone-input-container .country-code {
            font-weight: 500;
            color: #374151;
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }
        
        .phone-input-container .separator-line {
            width: 1px;
            height: 20px;
            background-color: #D1D5DB;
            margin-right: 0.75rem;
        }
        
        .phone-input-container .phone-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 1rem;
            color: #000000;
            background: transparent;
            padding: 0;
        }
        
        .phone-input-container .phone-input::placeholder {
            color: #9CA3AF;
        }
        
        .phone-input-container.error {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('mobile_number');
            
            // Format existing mobile number if it exists
            let existingMobile = phoneInput.value;
            if (existingMobile) {
                // Remove +63 prefix and format as 912 345 6789
                existingMobile = existingMobile.replace(/^\+63/, '').replace(/\D/g, '');
                if (existingMobile.length === 10) {
                    existingMobile = existingMobile.substring(0, 3) + ' ' + existingMobile.substring(3, 6) + ' ' + existingMobile.substring(6);
                }
                phoneInput.value = existingMobile;
            }
            
            // Format phone number as user types
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
                
                // Limit to 10 digits (Philippine mobile numbers)
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                
                // Format with spaces: 912 345 6789
                if (value.length > 0) {
                    if (value.length <= 3) {
                        value = value;
                    } else if (value.length <= 6) {
                        value = value.substring(0, 3) + ' ' + value.substring(3);
                    } else {
                        value = value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
                    }
                }
                
                e.target.value = value;
            });
            
            // Prevent user from entering non-digits
            phoneInput.addEventListener('keydown', function(e) {
                // Allow backspace, delete, arrow keys, tab, etc.
                if ([8, 9, 27, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }

                // Allow only digits
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
                }
            });
        });
    </script>
</x-layout>