<x-layout>
    <div class="flex items-center justify-center min-h-screen w-full p-2 sm:p-4 relative">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover opacity-10" src="{{ asset('images/gym-image.png') }}" alt="Gym Background">
        </div>
        
        <div class="flex flex-col lg:flex-row items-center justify-center w-full max-w-sm sm:max-w-md lg:max-w-4xl rounded-lg shadow-lg overflow-hidden bg-white relative z-10">
            <div class="flex flex-col flex-1 justify-center items-center gap-3 sm:gap-4 lg:gap-5 p-4 sm:p-6 lg:p-8 w-full">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold text-center text-gray-900">Silencio System</h1>
                <h2 class="text-xs sm:text-sm lg:text-base text-gray-600 text-center">Create your account to start your fitness journey</h2>
                
                @php
                    // Only show errors that are specifically related to member registration
                    $memberRegistrationErrors = [];
                    $memberFields = ['first_name', 'middle_name', 'last_name', 'age', 'gender', 'email', 'mobile_number', 'password', 'password_confirmation'];
                    
                    foreach ($memberFields as $field) {
                        if ($errors->has($field)) {
                            $memberRegistrationErrors = array_merge($memberRegistrationErrors, $errors->get($field));
                        }
                    }
                    
                    // Also check for member-specific error messages
                    foreach ($errors->all() as $error) {
                        if (str_contains($error, 'member registration') || str_contains($error, 'member')) {
                            $memberRegistrationErrors[] = $error;
                        }
                    }
                @endphp
                
                @if (!empty($memberRegistrationErrors))
                    <div class="w-full max-w-xs sm:max-w-sm bg-red-50 border border-red-200 text-red-700 px-2 sm:px-3 lg:px-4 py-2 sm:py-3 rounded-lg">
                        <ul class="list-disc list-inside text-xs sm:text-sm">
                            @foreach ($memberRegistrationErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="w-full max-w-xs sm:max-w-sm bg-green-50 border border-green-200 text-green-700 px-2 sm:px-3 lg:px-4 py-2 sm:py-3 rounded-lg">
                        <p class="text-xs sm:text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="w-full max-w-xs sm:max-w-sm bg-red-50 border border-red-200 text-red-700 px-2 sm:px-3 lg:px-4 py-2 sm:py-3 rounded-lg">
                        <p class="text-xs sm:text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('member.register.post') }}" class="flex flex-col w-full max-w-xs sm:max-w-sm mx-auto">
                    @csrf
                    
                    <!-- First Name -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="first_name" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">First name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Enter your first name" pattern="[A-Z][a-zA-Z\s]*" title="Must start with a capital letter and contain only letters and spaces" required />
                    </div>
                    
                    <!-- Middle Name -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="middle_name" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Middle name (Optional)</label>
                        <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Enter your middle name" pattern="[A-Z][a-zA-Z\s]*" title="Must start with a capital letter and contain only letters and spaces" />
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="last_name" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Last name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Enter your last name" pattern="[A-Z][a-zA-Z\s]*" title="Must start with a capital letter and contain only letters and spaces" required />
                    </div>

                    <!-- Age -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="age" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Age</label>
                        <input type="number" id="age" name="age" value="{{ old('age') }}" required class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Enter your age" min="1" max="120" />
                    </div>

                    <!-- Gender -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="gender" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Gender</label>
                        <select id="gender" name="gender" required class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm">
                            <option value="">Select your gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            <option value="Prefer not to say" {{ old('gender') == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                        </select>
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="email" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Email address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="name@example.com" required />
                    </div>
                    
                    <!-- Mobile Number -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="mobile_number" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Mobile number</label>
                        <div id="phone-input-wrapper" class="phone-input-container bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus-within:ring-blue-500 focus-within:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm">
                            <img src="https://flagcdn.com/w40/ph.png" alt="Philippines" class="flag-icon w-5 h-3 sm:w-6 sm:h-4">
                            <span class="country-code text-sm sm:text-base">+63</span>
                            <div class="separator-line"></div>
                            <input type="tel" name="mobile_number" id="mobile_number" value="{{ old('mobile_number') }}" class="phone-input" placeholder="912 345 6789" maxlength="13" required />
                        </div>
                        <p id="mobile-helper-text" class="mt-1 text-xs text-gray-500">Enter your 10-digit mobile number (e.g., 912 345 6789)</p>
                        <p id="mobile-error-text" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="password" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Password</label>
                        <input type="password" id="password" name="password" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Create a password" required />
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <label for="password_confirmation" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Confirm password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="Confirm your password" required />
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="mb-3 sm:mb-4 lg:mb-5">
                        <div class="flex items-center justify-center">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="accept_terms" name="accept_terms" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" required>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="accept_terms" class="text-gray-700 text-center">
                                        I agree to the
                                        <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:underline font-medium">Terms and Conditions</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('accept_terms')
                            <p class="mt-1 text-xs text-red-600 text-center">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Sign Up Link -->
                    <div class="flex flex-col gap-2 sm:gap-3 mb-3 sm:mb-4 lg:mb-5">
                        <a href="{{ route('login.show') }}" class="text-xs sm:text-sm text-blue-600 hover:underline text-center">Already have an account? Log in</a>
                    </div>
                    
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs sm:text-sm w-full px-4 sm:px-5 py-2 sm:py-3 lg:py-2.5 text-center min-h-[40px] sm:min-h-[44px]">Create Account</button>
                </form>
            </div>
            <div class="hidden lg:flex lg:flex-2">
                <img class="object-cover w-full h-full rounded-r-lg" src="{{ asset('images/gym-image.png') }}" alt="Gym Image">
            </div>
        </div>
    </div>

    <style>
        .phone-input-container {
            position: relative;
        }
        
        .phone-input-container .flag-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .phone-input-container .separator-line {
            position: absolute;
            left: 68px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 2px;
            height: 24px;
            background-color: #000000;
            pointer-events: none;
        }
        
        .phone-input-container .country-code {
            position: absolute;
            left: 40px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #374151;
            font-weight: 500;
            font-size: 14px;
            pointer-events: none;
        }
        
        .phone-input-container input {
            padding-left: 80px !important;
        }
        
        .phone-input-container input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('mobile_number');
            
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
            
            // Prevent user from deleting the +63 prefix
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
            
            // Function to show mobile number error
            function showMobileError(message) {
                const wrapper = document.getElementById('phone-input-wrapper');
                const helperText = document.getElementById('mobile-helper-text');
                const errorText = document.getElementById('mobile-error-text');

                // Add error styling to input wrapper
                wrapper.classList.remove('border-gray-300', 'focus-within:ring-blue-500', 'focus-within:border-blue-500');
                wrapper.classList.add('border-red-500', 'focus-within:ring-red-500', 'focus-within:border-red-500');

                // Hide helper text and show error text
                helperText.classList.add('hidden');
                errorText.textContent = message;
                errorText.classList.remove('hidden');

                phoneInput.focus();
            }

            // Function to clear mobile number error
            function clearMobileError() {
                const wrapper = document.getElementById('phone-input-wrapper');
                const helperText = document.getElementById('mobile-helper-text');
                const errorText = document.getElementById('mobile-error-text');

                // Remove error styling
                wrapper.classList.remove('border-red-500', 'focus-within:ring-red-500', 'focus-within:border-red-500');
                wrapper.classList.add('border-gray-300', 'focus-within:ring-blue-500', 'focus-within:border-blue-500');

                // Show helper text and hide error text
                helperText.classList.remove('hidden');
                errorText.classList.add('hidden');
                errorText.textContent = '';
            }

            // Clear error when user starts typing
            phoneInput.addEventListener('input', function() {
                clearMobileError();
            });

            // Validate phone number on form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                console.log('Form submission started');
                const phoneValue = phoneInput.value.replace(/\D/g, '');
                console.log('Phone value:', phoneValue);

                // Clear any previous errors
                clearMobileError();

                if (phoneValue.length !== 10) {
                    e.preventDefault();
                    showMobileError('Please enter a valid 10-digit Philippine mobile number. Current length: ' + phoneValue.length);
                    return false;
                }

                // Check if it starts with 9 (Philippine mobile numbers start with 9)
                if (!phoneValue.startsWith('9')) {
                    e.preventDefault();
                    showMobileError('Philippine mobile numbers must start with 9. Current value: ' + phoneValue);
                    return false;
                }

                console.log('Phone validation passed, submitting form');
            });

            // Name validation for first name and last name
            const nameFields = document.querySelectorAll('#first_name, #last_name');

            nameFields.forEach(field => {
                // Prevent typing numbers and special characters
                field.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    // Only allow letters and spaces
                    if (!/[A-Za-z\s]/.test(char)) {
                        e.preventDefault();
                        showNameError(field, 'Only letters and spaces are allowed');
                        return false;
                    }
                });

                // Clean input on typing
                field.addEventListener('input', function(e) {
                    let value = e.target.value;
                    // Remove any non-letter characters except spaces
                    const cleanValue = value.replace(/[^A-Za-z\s]/g, '');
                    // Prevent multiple consecutive spaces
                    const finalValue = cleanValue.replace(/\s+/g, ' ');

                    if (value !== finalValue) {
                        e.target.value = finalValue;
                        showNameError(field, 'Numbers and special characters are not allowed');
                    } else {
                        clearNameError(field);
                    }
                });

                // Handle paste events
                field.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanPaste = paste.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ');
                    e.target.value = cleanPaste;

                    if (paste !== cleanPaste) {
                        showNameError(field, 'Numbers and special characters have been removed');
                    }
                });

                // Validate on blur
                field.addEventListener('blur', function(e) {
                    const value = e.target.value.trim();
                    if (value && !/^[A-Za-z\s]+$/.test(value)) {
                        showNameError(field, 'Only letters and spaces are allowed');
                    } else {
                        clearNameError(field);
                    }
                });
            });

            function showNameError(field, message) {
                clearNameError(field);

                const errorDiv = document.createElement('div');
                errorDiv.className = 'name-error text-red-500 text-xs mt-1';
                errorDiv.textContent = message;

                field.parentNode.appendChild(errorDiv);
                field.classList.add('border-red-500', 'focus:border-red-500');
                field.classList.remove('border-gray-300', 'focus:border-blue-500');

                // Auto-hide error after 3 seconds
                setTimeout(() => {
                    clearNameError(field);
                }, 3000);
            }

            function clearNameError(field) {
                const existingError = field.parentNode.querySelector('.name-error');
                if (existingError) {
                    existingError.remove();
                }

                field.classList.remove('border-red-500', 'focus:border-red-500');
                field.classList.add('border-gray-300', 'focus:border-blue-500');
            }
        });
    </script>

    <!-- Registration Information Modal (Auto-popup) -->
    <div id="registrationInfoModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 pointer-events-none">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full p-8 relative pointer-events-auto" style="border: 4px solid black;">
            <!-- Modal Content -->
            <div class="mb-6">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-3xl font-bold text-gray-900">Please Read:</h3>
                    <!-- Close Button -->
                    <button onclick="closeRegistrationInfoModal()" class="text-gray-600 hover:text-gray-900 text-4xl font-bold transition-colors leading-none" style="margin-top: -8px;">
                        ×
                    </button>
                </div>
                <div class="space-y-5 text-lg text-gray-700">
                    <p class="flex items-start">
                        <span class="text-blue-600 mr-3 font-bold text-2xl">•</span>
                        <span>Please make sure to input a <strong>valid email address</strong>.</span>
                    </p>
                    <p class="flex items-start">
                        <span class="text-blue-600 mr-3 font-bold text-2xl">•</span>
                        <span>Once done creating the account, please <strong>verify it</strong> by clicking/tapping on <strong>"Verify Email Address"</strong> sent to you by mail from <strong>Silencio Gym Management System</strong>.</span>
                    </p>
                </div>
            </div>

            <!-- Action Button -->
            <div class="flex justify-end mt-8">
                <button onclick="closeRegistrationInfoModal()" class="px-8 py-3 bg-blue-600 text-white text-lg rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    I Understand
                </button>
            </div>
        </div>
    </div>

    <!-- Registration Info Modal Script -->
    <script>
        console.log('✅ Registration info modal script loaded');

        function closeRegistrationInfoModal() {
            const modal = document.getElementById('registrationInfoModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scrolling
                console.log('✅ Registration info modal closed');
            }
        }

        // Show modal automatically when page loads (unless coming from terms page)
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('registrationInfoModal');
            const urlParams = new URLSearchParams(window.location.search);
            const fromTerms = urlParams.get('from_terms');

            if (modal && !fromTerms) {
                // Prevent background scrolling when modal is open
                document.body.style.overflow = 'hidden';
                console.log('✅ Registration info modal shown automatically');
            } else if (fromTerms) {
                // Hide modal if coming from terms page
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                console.log('✅ Skipped modal - returning from terms page');
            }
        });

        // Close modal when clicking outside
        document.getElementById('registrationInfoModal')?.addEventListener('click', function(event) {
            if (event.target === this) {
                closeRegistrationInfoModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('registrationInfoModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeRegistrationInfoModal();
                }
            }
        });

        console.log('✅ All registration info modal event listeners attached');
    </script>
</x-layout>