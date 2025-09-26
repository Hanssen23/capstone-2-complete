<x-layout>
    <div class="flex items-center justify-center h-screen w-screen">
        <div class="flex items-center justify-center w-450 rounded-lg shadow-lg overflow-hidden">
            <div class="flex flex-col flex-1 justify-center items-center gap-5 p-8">
                <h1 class="text-5xl font-bold">Silencio System</h1>
                <h2 class="text-[16px] text-gray-500">Create your account to start your fitness journey</h2>
                
                @php
                    // Only show errors that are specifically related to member registration
                    $memberRegistrationErrors = [];
                    $memberFields = ['first_name', 'last_name', 'email', 'mobile_number', 'password', 'password_confirmation'];
                    
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
                    <div class="w-full max-w-sm bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($memberRegistrationErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="w-full max-w-sm bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="w-full max-w-sm bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('member.register.post') }}" class="flex flex-col w-100 mx-auto">
                    @csrf
                    
                    <!-- First Name -->
                    <div class="mb-5">
                        <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900">First name</label>
                        <input 
                            type="text" 
                            id="first_name"
                            name="first_name" 
                            value="{{ old('first_name') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Enter your first name"
                            required 
                        />
                    </div>
                    
                    <!-- Last Name -->
                    <div class="mb-5">
                        <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900">Last name</label>
                        <input 
                            type="text" 
                            id="last_name"
                            name="last_name" 
                            value="{{ old('last_name') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Enter your last name"
                            required 
                        />
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email address</label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            value="{{ old('email') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="name@example.com"
                            required 
                        />
                    </div>
                    
                    <!-- Mobile Number -->
                    <div class="mb-5">
                        <label for="mobile_number" class="block mb-2 text-sm font-medium text-gray-900">Mobile number</label>
                        <div class="phone-input-container">
                            <img src="https://flagcdn.com/w40/ph.png" alt="Philippines" class="flag-icon w-6 h-4">
                            <span class="country-code">+63</span>
                            <div class="separator-line"></div>
                            <input 
                                type="tel" 
                                name="mobile_number" 
                                id="mobile_number" 
                                value="{{ old('mobile_number') }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pr-3 py-2.5" 
                                placeholder="912 345 6789" 
                                maxlength="13"
                                required 
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Enter your 10-digit mobile number (e.g., 912 345 6789)</p>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-5">
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Create a password"
                            required 
                        />
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-5">
                        <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Confirm password</label>
                        <input 
                            type="password" 
                            id="password_confirmation"
                            name="password_confirmation" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                            placeholder="Confirm your password"
                            required 
                        />
                    </div>
                    
                    <!-- Sign Up Link and Submit Button -->
                    <div class="flex items-start mb-5">
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline mr-4">Already have an account? Log in</a>
                    </div>
                    
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Create Account</button>
                </form>
            </div>
            <div class="flex-2">
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
            
            // Validate phone number on form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                console.log('Form submission started');
                const phoneValue = phoneInput.value.replace(/\D/g, '');
                console.log('Phone value:', phoneValue);
                
                if (phoneValue.length !== 10) {
                    e.preventDefault();
                    alert('Please enter a valid 10-digit Philippine mobile number. Current length: ' + phoneValue.length);
                    phoneInput.focus();
                    return false;
                }
                
                // Check if it starts with 9 (Philippine mobile numbers start with 9)
                if (!phoneValue.startsWith('9')) {
                    e.preventDefault();
                    alert('Philippine mobile numbers must start with 9. Current value: ' + phoneValue);
                    phoneInput.focus();
                    return false;
                }
                
                console.log('Phone validation passed, submitting form');
            });
        });
    </script>
</x-layout>