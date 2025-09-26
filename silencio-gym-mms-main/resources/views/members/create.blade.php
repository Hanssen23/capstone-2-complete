<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Members</x-topbar>

        <!-- Main Content -->
        <div class="p-6">
            <!-- Header with Back Button -->
            <div class="mb-6 sticky top-20 z-10 -mx-6 px-6 py-3 bg-white/90 backdrop-blur border-b border-gray-200">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('members.index') }}" class="flex items-center gap-2 text-black hover:text-red-600 transition-colors duration-200">
                        <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="font-medium">Back to Members</span>
                    </a>
                    <h2 class="text-xl md:text-2xl font-bold text-black">Create New Member</h2>
                </div>
            </div>

            <!-- Create Member Form -->
            <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-800 p-6">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-700 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-300">There were errors with your submission</h3>
                                <div class="mt-2 text-sm text-red-200">
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
                <form method="POST" action="{{ route('members.store') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Auto-generated fields info -->
                    <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-300">
                                Member Number and UID will be automatically generated
                            </p>
                        </div>
                    </div>

                    <!-- Subscription Info -->
                    <div class="bg-yellow-900/20 border border-yellow-700 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-300">No Subscription Plan Assigned</p>
                                <p class="text-xs text-yellow-200 mt-1">
                                    Members will need to select and pay for a plan after registration to access paid features.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- First Name Field -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-300 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" required 
                               class="w-full px-4 py-2 border border-gray-600 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter first name"
                               value="{{ old('first_name') }}">
                    </div>

                    <!-- Last Name Field -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-300 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required 
                               class="w-full px-4 py-2 border border-gray-600 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter last name"
                               value="{{ old('last_name') }}">
                    </div>

                    <!-- Mobile Number Field -->
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-300 mb-2">Mobile Number</label>
                        <input type="tel" id="mobile_number" name="mobile_number" required 
                               class="w-full px-4 py-2 border border-gray-600 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-colors duration-200 placeholder-gray-400"
                               placeholder="e.g., +63 917 123 4567"
                               value="{{ old('mobile_number') }}">
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-2 border border-gray-600 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 transition-colors duration-200 placeholder-gray-400"
                               placeholder="Enter email address"
                               value="{{ old('email') }}">
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex items-center gap-4 pt-4">
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                                Create
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </button>
                        <a href="{{ route('members.index') }}" 
                           class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg font-medium hover:bg-gray-800 transition-colors duration-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>