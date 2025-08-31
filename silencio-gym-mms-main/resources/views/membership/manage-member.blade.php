<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-gray-100">
        <x-topbar>Member Plan Management</x-topbar>

        <div class="bg-gray-100 min-h-screen p-6">
            <!-- Member Selection -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Select Member</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Search Member</label>
                            <input type="text" id="memberSearch" placeholder="Search by name, email, or member number..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Or Select from List</label>
                            <select id="memberSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <option value="">Choose a member...</option>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}" data-member='@json($member)'>
                                    {{ $member->full_name }} ({{ $member->member_number }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Selection and Payment Form -->
            <div id="planSelectionForm" class="hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Plan Selection & Payment</h2>
                    
                    <!-- Selected Member Info -->
                    <div id="selectedMemberInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                        <h3 class="text-xl font-semibold text-blue-900 mb-4">Selected Member</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex items-center">
                                <span class="text-sm text-blue-600 font-medium">Name:</span>
                                <span id="memberName" class="ml-3 font-semibold text-blue-900"></span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-blue-600 font-medium">Member #:</span>
                                <span id="memberNumber" class="ml-3 font-semibold text-blue-900"></span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-blue-600 font-medium">Email:</span>
                                <span id="memberEmail" class="ml-3 font-semibold text-blue-900"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Configuration -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Plan Configuration</h3>
                            
                            <!-- Plan Type Selection -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Plan Type</label>
                                <div class="space-y-4">
                                    @foreach(config('membership.plan_types') as $key => $plan)
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="plan_type" value="{{ $key }}" class="mr-4 text-blue-600 focus:ring-blue-500" required>
                                        <div class="flex-1">
                                            <div class="text-lg font-semibold text-gray-900 mb-1">{{ $plan['name'] }}</div>
                                            <div class="text-sm text-gray-600">{{ $plan['description'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-600 text-lg">₱{{ number_format($plan['base_price'], 2) }}/month</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Duration Selection -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Duration</label>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach(config('membership.duration_types') as $key => $duration)
                                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="duration_type" value="{{ $key }}" class="mr-3 text-blue-600 focus:ring-blue-500" required>
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-900">{{ $duration['name'] }}</div>
                                            <div class="text-sm text-gray-600">{{ $duration['days'] }} days</div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Start Date Selection -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Membership Start Date</label>
                                <input type="date" id="startDate" name="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-6">Price Calculation & Payment</h3>
                            
                            <!-- Price Display -->
                            <div id="priceCalculation" class="bg-gray-50 border border-gray-200 rounded-lg p-8 mb-8">
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-blue-600 mb-3" id="totalPrice">₱0.00</div>
                                    <div class="text-lg text-gray-600" id="priceBreakdown">Select plan and duration</div>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Payment Amount</label>
                                    <input type="number" id="paymentAmount" name="amount" step="0.01" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50" readonly>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Notes (Optional)</label>
                                    <textarea name="notes" rows="4" placeholder="Any additional notes about this payment..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                                </div>

                                <!-- Confirmation Button -->
                                <button id="confirmPaymentBtn" onclick="confirmPayment()" class="w-full py-4 bg-green-600 text-white font-semibold text-lg rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm" disabled>
                                    <svg class="w-6 h-6 inline mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Confirm Payment & Activate Membership
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div id="successMessage" class="hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-12 text-center">
                    <svg class="w-20 h-20 text-green-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-3xl font-bold text-green-900 mb-4">Payment Successful!</h3>
                    <p class="text-green-700 text-lg mb-8">Membership has been activated successfully.</p>
                    <div class="flex justify-center space-x-6">
                        <button onclick="resetForm()" class="inline-flex items-center px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Process Another Payment
                        </button>
                        <a href="{{ route('membership.payments') }}" class="inline-flex items-center px-8 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            View All Payments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMember = null;
        let selectedPlanType = null;
        let selectedDurationType = null;

        // Set default start date to today
        document.getElementById('startDate').value = new Date().toISOString().split('T')[0];

        // Member selection handling
        document.getElementById('memberSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                selectedMember = JSON.parse(selectedOption.dataset.member);
                displayMemberInfo();
                showPlanSelectionForm();
            } else {
                hidePlanSelectionForm();
            }
        });

        // Member search functionality
        document.getElementById('memberSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = document.getElementById('memberSelect').options;
            
            for (let i = 1; i < options.length; i++) {
                const option = options[i];
                const memberData = JSON.parse(option.dataset.member);
                const searchText = `${memberData.full_name} ${memberData.email} ${memberData.member_number}`.toLowerCase();
                
                if (searchText.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        });

        // Plan type selection
        document.querySelectorAll('input[name="plan_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedPlanType = this.value;
                updatePriceCalculation();
            });
        });

        // Duration selection
        document.querySelectorAll('input[name="duration_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedDurationType = this.value;
                updatePriceCalculation();
            });
        });

        function displayMemberInfo() {
            document.getElementById('memberName').textContent = selectedMember.full_name;
            document.getElementById('memberNumber').textContent = selectedMember.member_number;
            document.getElementById('memberEmail').textContent = selectedMember.email;
        }

        function showPlanSelectionForm() {
            document.getElementById('planSelectionForm').classList.remove('hidden');
        }

        function hidePlanSelectionForm() {
            document.getElementById('planSelectionForm').classList.add('hidden');
        }

        function updatePriceCalculation() {
            if (selectedPlanType && selectedDurationType) {
                const planTypes = @json(config('membership.plan_types'));
                const durationTypes = @json(config('membership.duration_types'));
                
                const basePrice = planTypes[selectedPlanType].base_price;
                const multiplier = durationTypes[selectedDurationType].multiplier;
                const totalPrice = basePrice * multiplier;
                
                            document.getElementById('totalPrice').textContent = `₱${totalPrice.toFixed(2)}`;
            document.getElementById('priceBreakdown').textContent = `${planTypes[selectedPlanType].name} (₱${basePrice}/month) × ${durationTypes[selectedDurationType].name} (${multiplier}x)`;
                document.getElementById('paymentAmount').value = totalPrice.toFixed(2);
                
                // Enable confirm button
                document.getElementById('confirmPaymentBtn').disabled = false;
            }
        }

        function confirmPayment() {
            if (!selectedMember || !selectedPlanType || !selectedDurationType) {
                alert('Please select all required fields');
                return;
            }

            const formData = {
                member_id: selectedMember.id,
                plan_type: selectedPlanType,
                duration_type: selectedDurationType,
                amount: document.getElementById('paymentAmount').value,
                start_date: document.getElementById('startDate').value,
                notes: document.querySelector('textarea[name="notes"]').value,
                _token: '{{ csrf_token() }}'
            };

            // Show loading state
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<svg class="animate-spin w-6 h-6 inline mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
            confirmBtn.disabled = true;

            fetch('{{ route("membership.process-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('planSelectionForm').classList.add('hidden');
                    document.getElementById('successMessage').classList.remove('hidden');
                } else {
                    alert('Error: ' + data.message);
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing the payment');
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            });
        }

        function resetForm() {
            // Reset form
            document.getElementById('memberSelect').value = '';
            document.getElementById('memberSearch').value = '';
            document.querySelectorAll('input[name="plan_type"]').forEach(radio => radio.checked = false);
            document.querySelectorAll('input[name="duration_type"]').forEach(radio => radio.checked = false);
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            document.querySelector('textarea[name="notes"]').value = '';
            
            // Reset display
            selectedMember = null;
            selectedPlanType = null;
            selectedDurationType = null;
            
            // Hide forms
            hidePlanSelectionForm();
            document.getElementById('successMessage').classList.add('hidden');
            
            // Reset price calculation
            document.getElementById('totalPrice').textContent = '₱0.00';
            document.getElementById('priceBreakdown').textContent = 'Select plan and duration';
            document.getElementById('paymentAmount').value = '';
            document.getElementById('confirmPaymentBtn').disabled = true;
        }
    </script>
</x-layout>
