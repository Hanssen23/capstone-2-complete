<x-layout>
    <!-- NEW LOGIN VIEW WITH MODAL - FORCE REFRESH -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <div class="flex items-center justify-center min-h-screen w-full p-2 sm:p-4 relative">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img class="w-full h-full object-cover opacity-10" src="{{ asset('images/gym-image.png') }}" alt="Gym Background">
        </div>

        <!-- Login Form Container -->
        <div class="relative z-10 w-full max-w-sm sm:max-w-md lg:max-w-4xl bg-white rounded-lg shadow-lg lg:shadow-xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Login Form -->
                <div class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="text-center mb-4 sm:mb-6 lg:mb-8">
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-black mb-1 sm:mb-2">Silencio</h1>
                        <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-black mb-2 sm:mb-3">System</h2>
                        <p class="text-xs sm:text-sm text-gray-600">Log in to continue</p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-3 sm:mb-4 lg:mb-5 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="ml-2 sm:ml-3">
                                    <p class="text-xs sm:text-sm text-red-800 font-medium">{{ $errors->first() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="loginForm" method="POST" action="{{ route('login.post') }}" class="space-y-3 sm:space-y-4 lg:space-y-5">
                        @csrf
                        <div class="mb-3 sm:mb-4 lg:mb-5">
                            <label for="email" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Your email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" placeholder="name@example.com" required />
                        </div>
                        <div class="mb-3 sm:mb-4 lg:mb-5">
                            <label for="password" class="block mb-1 sm:mb-2 text-xs sm:text-sm font-medium text-gray-900">Your password</label>
                            <input type="password" id="password" name="password" class="bg-white border-2 border-gray-300 text-gray-900 text-xs sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 sm:p-3 lg:p-2.5 min-h-[40px] sm:min-h-[44px] shadow-sm" required />
                        </div>
                        <div class="flex flex-col gap-2 sm:gap-3 mb-3 sm:mb-4 lg:mb-5">
                            <a href="#" onclick="showSignupModal(event)" class="text-xs sm:text-sm text-blue-600 hover:underline text-center">Sign up</a>
                            <div class="flex items-center justify-center">
                                <a href="{{ route('member.password.request') }}" class="text-xs sm:text-sm text-blue-600 hover:underline">Set/Reset Password</a>
                            </div>
                        </div>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-xs sm:text-sm w-full px-4 sm:px-5 py-2 sm:py-3 lg:py-2.5 text-center min-h-[40px] sm:min-h-[44px]">Login</button>
                    </form>
                    
                    <!-- CSRF Token Refresh Script -->
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('loginForm');
                        const csrfInput = form.querySelector('input[name="_token"]');
                        
                        // Refresh CSRF token every 30 minutes
                        setInterval(function() {
                            fetch('/csrf-token')
                                .then(response => response.json())
                                .then(data => {
                                    csrfInput.value = data.csrf_token;
                                })
                                .catch(error => {
                                    console.error('Error refreshing CSRF token:', error);
                                });
                        }, 30 * 60 * 1000); // 30 minutes
                        
                        // Handle form submission
                        form.addEventListener('submit', function(e) {
                            // Ensure CSRF token is present
                            if (!csrfInput.value) {
                                e.preventDefault();
                                alert('Session expired. Please refresh the page and try again.');
                                window.location.reload();
                            }
                        });
                    });
                    </script>
                </div>
                <div class="hidden lg:flex lg:flex-2">
                    <img class="object-cover w-full h-full rounded-r-lg" src="{{ asset('images/gym-image.png') }}" alt="Gym Image">
                </div>
            </div>

            <!-- Signup Information Modal -->
            <div id="signupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
                    <!-- Close Button -->
                    <button onclick="closeSignupModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold">
                        ×
                    </button>

                    <!-- Modal Content -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Please Read:</h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Please make sure to input a <strong>valid email address</strong>.</span>
                            </p>
                            <p class="flex items-start">
                                <span class="text-blue-600 mr-2">•</span>
                                <span>Once done creating the account, please <strong>verify it</strong> by clicking/tapping on <strong>"Verify Email Address"</strong> sent to you by mail from <strong>Silencio Gym Management System</strong>.</span>
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button onclick="closeSignupModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                            Cancel
                        </button>
                        <a href="{{ route('member.register') }}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-center">
                            Continue to Sign Up
                        </a>
                    </div>
                </div>
            </div>

            <!-- Signup Modal Script -->
            <script>
                console.log('✅ NEW LOGIN VIEW - Signup modal script loaded!');

                function showSignupModal(event) {
                    console.log('✅ showSignupModal called from NEW VIEW');
                    event.preventDefault();
                    const modal = document.getElementById('signupModal');
                    console.log('✅ Modal element:', modal);
                    if (modal) {
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        console.log('✅ Modal should now be visible');
                        alert('✅ SUCCESS! Modal is working from the NEW login view!');
                    } else {
                        console.error('❌ Modal element not found!');
                        alert('❌ Modal element not found!');
                    }
                }

                function closeSignupModal() {
                    const modal = document.getElementById('signupModal');
                    if (modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto';
                        console.log('✅ Modal closed');
                    }
                }

                // Close modal when clicking outside
                document.getElementById('signupModal')?.addEventListener('click', function(event) {
                    if (event.target === this) {
                        closeSignupModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeSignupModal();
                    }
                });

                console.log('✅ All event listeners attached in NEW VIEW');
            </script>
        </div>
    </div>
</x-layout>
