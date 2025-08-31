<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Membership Plans Configuration</x-topbar>

        <div class="bg-gray-100 min-h-screen p-6">
            <!-- Plan Types Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Plan Types</h2>
                        <button onclick="openAddPlanModal()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Plan Type
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($plans as $plan)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-semibold text-gray-900">{{ $plan->name }}</h3>
                                <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    {{ $plan->currency ?? '₱' }}{{ number_format($plan->price, 2) }}/month
                                </span>
                            </div>
                            <p class="text-gray-600 mb-6 leading-relaxed">{{ $plan->description }}</p>
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                    <span class="text-sm text-gray-600">Monthly:</span>
                                    <span class="font-semibold text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                    <span class="text-sm text-gray-600">Quarterly:</span>
                                    <span class="font-semibold text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 3, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                    <span class="text-sm text-gray-600">Biannually:</span>
                                    <span class="font-semibold text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 6, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                    <span class="text-sm text-gray-600">Annually:</span>
                                    <span class="font-semibold text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 12, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="editPlanType('{{ $plan->id }}')" class="flex-1 px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                    Edit
                                </button>
                                <button onclick="deletePlanType('{{ $plan->id }}')" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Duration Types Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Duration Types</h2>
                        <button onclick="openAddDurationModal()" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New Duration
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        @foreach(config('membership.duration_types') as $key => $duration)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="text-center">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $duration['name'] }}</h3>
                                <div class="text-4xl font-bold text-blue-600 mb-3">{{ $duration['multiplier'] }}x</div>
                                <p class="text-sm text-gray-600 mb-6">{{ $duration['days'] }} days</p>
                                <div class="space-y-3 mb-6">
                                    @foreach(config('membership.plan_types') as $planKey => $plan)
                                    <div class="flex items-center justify-between py-2 px-3 bg-white rounded border">
                                        <span class="text-sm text-gray-600">{{ $plan['name'] }}:</span>
                                        <span class="font-semibold text-gray-900">₱{{ number_format($plan['base_price'] * $duration['multiplier'], 2) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="editDurationType('{{ $key }}')" class="flex-1 px-4 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="deleteDurationType('{{ $key }}')" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pricing Calculator Preview -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Pricing Calculator Preview</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Plan Type + Duration = Total Price</h3>
                            <div class="space-y-4">
                                @foreach(config('membership.plan_types') as $planKey => $plan)
                                    @foreach(config('membership.duration_types') as $durationKey => $duration)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900">{{ $plan['name'] }}</span>
                                            <span class="text-gray-400">+</span>
                                            <span class="font-medium text-gray-900">{{ $duration['name'] }}</span>
                                        </div>
                                        <span class="font-bold text-blue-600 text-lg">₱{{ number_format($plan['base_price'] * $duration['multiplier'], 2) }}</span>
                                    </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h3>
                            <div class="space-y-4">
                                <a href="{{ route('membership.manage-member') }}" class="block w-full p-6 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors group">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-blue-900 mb-1">Manage Member Plans</h4>
                                            <p class="text-blue-600">Process payments and activate memberships</p>
                                        </div>
                                    </div>
                                </a>
                                <a href="{{ route('membership.payments') }}" class="block w-full p-6 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors group">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-200 transition-colors">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-green-900 mb-1">View All Payments</h4>
                                            <p class="text-green-600">See payment history and manage records</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Type Modal -->
    <div id="addPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Plan Type</h3>
                    <button onclick="closeAddPlanModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="addPlanForm" class="p-6">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Plan Name</label>
                        <input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Base Price (per month)</label>
                        <input type="number" name="base_price" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                    </div>
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" required></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeAddPlanModal()" class="px-6 py-3 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddPlanModal() {
            document.getElementById('addPlanModal').classList.remove('hidden');
        }

        function closeAddPlanModal() {
            document.getElementById('addPlanModal').classList.add('hidden');
        }

        function editPlanType(planKey) {
            // TODO: Implement edit functionality
            alert('Edit functionality for ' + planKey + ' will be implemented next');
        }

        function deletePlanType(planKey) {
            if (confirm('Are you sure you want to delete this plan type? This action cannot be undone.')) {
                // TODO: Implement delete functionality
                alert('Delete functionality for ' + planKey + ' will be implemented next');
            }
        }

        function openAddDurationModal() {
            // TODO: Implement add duration modal
            alert('Add duration functionality will be implemented next');
        }

        function editDurationType(durationKey) {
            // TODO: Implement edit functionality
            alert('Edit functionality for ' + durationKey + ' will be implemented next');
        }

        function deleteDurationType(durationKey) {
            if (confirm('Are you sure you want to delete this duration type? This action cannot be undone.')) {
                // TODO: Implement delete functionality
                alert('Delete functionality for ' + durationKey + ' will be implemented next');
            }
        }

        // Close modal when clicking outside
        document.getElementById('addPlanModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddPlanModal();
            }
        });
    </script>
</x-layout>
