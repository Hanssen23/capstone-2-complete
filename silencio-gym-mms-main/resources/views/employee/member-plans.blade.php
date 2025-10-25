<x-layout>
    <x-nav-employee></x-nav-employee>
    <div class="flex-1 bg-white">
        <x-topbar>Set Plans</x-topbar>

        <div class="bg-white min-h-screen p-6">
            <!-- Member Selection -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-3xl font-bold mb-8" style="color: #1E40AF;">Select Member</h2>
                    
                    <!-- Search Bar -->
                    <div class="mb-8">
                        <div class="relative">
                            <input type="text" id="memberSearch" placeholder="Search by name, email, or member number..." 
                                   class="w-full px-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-base" 
                                   style="border-color: #E5E7EB; color: #000000;">
                        </div>
                    </div>

                    <!-- Member Results -->
                    <div id="memberResults" class="space-y-4">
                        @foreach($members as $member)
                        <div class="member-card bg-white border rounded-lg p-6 cursor-pointer transition-all duration-200 hover:shadow-md" 
                             style="border-color: #E5E7EB;" 
                             data-member='@json($member)'
                             onclick="selectMember(this)">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <h3 class="text-lg font-semibold" style="color: #000000;">{{ $member->full_name }}</h3>
                                        @php
                                            $currentPlan = $member->currentMembershipPeriod ? $member->currentMembershipPeriod->plan_type : null;
                                            $isActive = $member->currentMembershipPeriod && $member->currentMembershipPeriod->is_active;
                                            $durationType = $member->currentMembershipPeriod ? $member->currentMembershipPeriod->duration_type : null;
                                            $hasPayments = $member->payments()->where('status', 'completed')->exists();
                                            $planFromPayments = $member->payments()->where('status', 'completed')->latest()->first();
                                        @endphp
                                        
                                        @if($currentPlan && $isActive)
                                            @php
                                                $normalizedPlan = strtolower($currentPlan);
                                                $planColors = [
                                                    'basic' => '#059669',
                                                    'vip' => '#F59E0B', 
                                                    'premium' => '#F59E0B'
                                                ];
                                                $planColor = $planColors[$normalizedPlan] ?? '#059669';
                                                
                                                $badgeText = ucfirst($currentPlan);
                                                if ($durationType) {
                                                    $badgeText .= ' + ' . ucfirst($durationType);
                                                }
                                            @endphp
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" 
                                                  style="background-color: {{ $planColor }};">
                                                {{ $badgeText }}
                                            </span>
                                        @elseif($hasPayments && $planFromPayments)
                                            @php
                                                // Show plan from latest payment if no active membership period
                                                $paymentPlan = $planFromPayments->plan_type;
                                                $paymentDuration = $planFromPayments->duration_type;
                                                $normalizedPlan = strtolower($paymentPlan);
                                                $planColors = [
                                                    'basic' => '#059669',
                                                    'vip' => '#F59E0B', 
                                                    'premium' => '#F59E0B'
                                                ];
                                                $planColor = $planColors[$normalizedPlan] ?? '#059669';
                                                
                                                $badgeText = ucfirst($paymentPlan);
                                                if ($paymentDuration) {
                                                    $badgeText .= ' + ' . ucfirst($paymentDuration);
                                                }
                                            @endphp
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-white" 
                                                  style="background-color: {{ $planColor }};">
                                                {{ $badgeText }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full text-gray-600 bg-gray-200">
                                                Not Subscribed
                                            </span>
                                        @endif
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm" style="color: #6B7280;">
                                        <div>
                                            <span class="font-medium">Member #:</span> {{ $member->member_number }}
                                        </div>
                        <div>
                                            <span class="font-medium">Email:</span> {{ $member->email }}
                        </div>
                        <div>
                                            <span class="font-medium">Phone:</span> {{ $member->mobile_number }}
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <button class="px-4 py-2 border rounded-lg font-medium transition-colors" 
                                            style="border-color: #2563EB; color: #2563EB;" 
                                            onmouseover="this.style.backgroundColor='#2563EB'; this.style.color='#FFFFFF'" 
                                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2563EB'">
                                        Select
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Plan Selection and Payment Form -->
            <div id="planSelectionForm" class="hidden">
                <div class="bg-white rounded-lg border p-8 mb-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-3xl font-bold mb-8" style="color: #1E40AF;">Plan Selection & Payment</h2>
                    
                    <!-- Selected Member Card -->
                    <div id="selectedMemberInfo" class="bg-white border rounded-lg p-6 mb-8" style="border-color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <h3 class="text-xl font-semibold mb-4" style="color: #1E40AF;">Selected Member</h3>
                        <div class="flex items-center gap-6">
                            <div class="flex-1">
                                <div class="text-lg font-semibold" style="color: #000000;" id="memberName"></div>
                                <div class="text-sm" style="color: #6B7280;" id="memberNumber"></div>
                            </div>
                            <div class="text-sm" style="color: #6B7280;" id="memberEmail"></div>
                        </div>
                    </div>

                    <!-- Plan Selection -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xl font-semibold mb-6" style="color: #1E40AF;">Plan Options</h3>
                            
                            <!-- Plan Cards -->
                            <div class="space-y-4 mb-8">
                                    @foreach(config('membership.plan_types') as $key => $plan)
                                <div class="plan-card bg-white border rounded-lg p-6 cursor-pointer transition-all duration-200 hover:shadow-md" 
                                     style="border-color: #E5E7EB;" 
                                     data-plan="{{ $key }}"
                                     onclick="selectPlan('{{ $key }}')">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h4 class="text-lg font-bold" style="color: #000000;">{{ $plan['name'] }}</h4>
                                                @if($key === 'basic')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
                                                      style="background-color: #059669;">
                                                    Basic
                                                </span>
                                                @elseif($key === 'vip' || $key === 'premium')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white" 
                                                      style="background-color: #F59E0B;">
                                                    {{ $key === 'vip' ? 'VIP' : 'Premium' }}
                                                </span>
                                                @endif
                                            </div>
                                            <p class="text-sm mb-3" style="color: #6B7280;">{{ $plan['description'] }}</p>
                                            <div class="text-lg font-bold" style="color: #000000;">₱{{ number_format($plan['base_price'], 2) }}/month</div>
                                        </div>
                                        <div class="ml-4">
                                            <button class="select-plan-btn px-4 py-2 border rounded-lg font-medium transition-colors" 
                                                    style="border-color: #2563EB; color: #2563EB;" 
                                                    onmouseover="this.style.backgroundColor='#2563EB'; this.style.color='#FFFFFF'" 
                                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2563EB'">
                                                Select
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Duration Selection -->
                            <div class="mb-8">
                                <h4 class="text-lg font-semibold mb-4" style="color: #1E40AF;">Duration</h4>
                                <div class="flex gap-2">
                                    @foreach(config('membership.duration_types') as $key => $duration)
                                    <button class="duration-btn px-4 py-2 border rounded-lg font-medium transition-colors" 
                                            style="border-color: #E5E7EB; color: #6B7280;" 
                                            data-duration="{{ $key }}"
                                            onclick="selectDuration('{{ $key }}')"
                                            onmouseover="this.style.backgroundColor='#F3F4F6'" 
                                            onmouseout="this.style.backgroundColor='transparent'">
                                        {{ $duration['name'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Membership Start Date</label>
                                <input type="date" id="startDate" name="start_date" 
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                       style="border-color: #E5E7EB;" required>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xl font-semibold mb-6" style="color: #1E40AF;">Payment Details</h3>
                            
                            <!-- Price Calculation -->
                            <div id="priceCalculation" class="bg-gray-50 border rounded-lg p-8 mb-8 text-center" style="border-color: #E5E7EB;">
                                <div class="text-4xl font-bold mb-3" style="color: #000000;" id="totalPrice">₱0.00</div>
                                <div class="text-lg" style="color: #6B7280;" id="priceBreakdown">Select plan and duration</div>
                            </div>

                            <!-- Payment Form -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Payment Amount</label>
                                    <input type="number" id="paymentAmount" name="amount" step="0.01" min="0" 
                                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50" 
                                           style="border-color: #E5E7EB;" readonly>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Notes (Optional)</label>
                                    <textarea name="notes" rows="4" placeholder="Any additional notes about this payment..." 
                                              class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" 
                                              style="border-color: #E5E7EB;"></textarea>
                                </div>

                                <!-- Confirm Payment Button -->
                                <button id="confirmPaymentBtn" onclick="confirmPayment()" 
                                        class="w-full py-4 text-white font-semibold text-lg rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-3" 
                                        style="background-color: #059669;" 
                                        onmouseover="this.style.backgroundColor='#047857'" 
                                        onmouseout="this.style.backgroundColor='#059669'" 
                                        disabled>
                                    <span class="text-xl">💳</span>
                                    Confirm Payment & Activate Membership
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div id="successMessage" class="hidden">
                <div class="bg-white border rounded-lg p-12 text-center" style="border-color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="text-6xl mb-6">✅</div>
                    <h3 class="text-3xl font-bold mb-4" style="color: #059669;">Payment Successful!</h3>
                    <p class="text-lg mb-8" style="color: #6B7280;">Membership has been activated successfully.</p>
                    <div class="flex justify-center space-x-6">
                        <button onclick="resetForm()" 
                                class="inline-flex items-center px-8 py-4 text-white rounded-lg transition-colors shadow-sm" 
                                style="background-color: #059669;" 
                                onmouseover="this.style.backgroundColor='#047857'" 
                                onmouseout="this.style.backgroundColor='#059669'">
                            <span class="text-xl mr-2">➕</span>
                            Process Another Payment
                        </button>
                        <a href="{{ route('employee.membership.payments') }}" 
                           class="inline-flex items-center px-8 py-4 text-white rounded-lg transition-colors shadow-sm" 
                           style="background-color: #2563EB;" 
                           onmouseover="this.style.backgroundColor='#1D4ED8'" 
                           onmouseout="this.style.backgroundColor='#2563EB'">
                            <span class="text-xl mr-2">👁️</span>
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

        // Member search functionality
        document.getElementById('memberSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const memberCards = document.querySelectorAll('.member-card');
            
            memberCards.forEach(card => {
                const memberData = JSON.parse(card.dataset.member);
                const searchText = `${memberData.full_name} ${memberData.email} ${memberData.member_number}`.toLowerCase();
                
                if (searchText.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Member selection
        function selectMember(cardElement) {
            // Remove previous selection
            document.querySelectorAll('.member-card').forEach(card => {
                card.style.borderColor = '#E5E7EB';
            });
            
            // Highlight selected member
            cardElement.style.borderColor = '#059669';
            
            selectedMember = JSON.parse(cardElement.dataset.member);
            displayMemberInfo();
            showPlanSelectionForm();
        }

        // Plan selection
        function selectPlan(planType) {
            // Remove previous selection
            document.querySelectorAll('.plan-card').forEach(card => {
                card.style.borderColor = '#E5E7EB';
                const btn = card.querySelector('.select-plan-btn');
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#2563EB';
                btn.textContent = 'Select';
            });
            
            // Highlight selected plan
            const selectedCard = document.querySelector(`[data-plan="${planType}"]`);
            selectedCard.style.borderColor = '#059669';
            const btn = selectedCard.querySelector('.select-plan-btn');
            btn.style.backgroundColor = '#059669';
            btn.style.color = '#FFFFFF';
            btn.textContent = 'Selected';
            
            selectedPlanType = planType;
            updatePriceCalculation();
        }

        // Duration selection
        function selectDuration(durationType) {
            // Remove previous selection
            document.querySelectorAll('.duration-btn').forEach(btn => {
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#6B7280';
                btn.style.borderColor = '#E5E7EB';
            });
            
            // Highlight selected duration
            const selectedBtn = document.querySelector(`[data-duration="${durationType}"]`);
            selectedBtn.style.backgroundColor = '#1E40AF';
            selectedBtn.style.color = '#FFFFFF';
            selectedBtn.style.borderColor = '#1E40AF';
            
            selectedDurationType = durationType;
            updatePriceCalculation();
        }

        function displayMemberInfo() {
            document.getElementById('memberName').textContent = selectedMember.full_name;
            document.getElementById('memberNumber').textContent = `Member #: ${selectedMember.member_number}`;
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

            fetch('{{ route("employee.membership.process-payment") }}', {
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
            document.getElementById('memberSearch').value = '';
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            document.querySelector('textarea[name="notes"]').value = '';
            
            // Reset member selection
            document.querySelectorAll('.member-card').forEach(card => {
                card.style.borderColor = '#E5E7EB';
            });
            
            // Reset plan selection
            document.querySelectorAll('.plan-card').forEach(card => {
                card.style.borderColor = '#E5E7EB';
                const btn = card.querySelector('.select-plan-btn');
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#2563EB';
                btn.textContent = 'Select';
            });
            
            // Reset duration selection
            document.querySelectorAll('.duration-btn').forEach(btn => {
                btn.style.backgroundColor = 'transparent';
                btn.style.color = '#6B7280';
                btn.style.borderColor = '#E5E7EB';
            });
            
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
