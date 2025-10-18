<x-layout>
    <x-nav></x-nav>
    <div class="flex-1 bg-white">
        <x-topbar>Member Plan Management</x-topbar>

        <div class="bg-white min-h-screen p-4 sm:p-6">
            <!-- Member Selection -->
            <div class="mb-6 sm:mb-8">
                <div class="bg-white rounded-lg border p-4 sm:p-6 lg:p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8" style="color: #1E40AF;">Select Member</h2>
                    
                    <!-- Search Bar -->
                    <div class="mb-6 sm:mb-8">
                        <div class="relative">
                            <input type="text" id="memberSearch" placeholder="Search by name, email, or member number..." 
                                   class="w-full px-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm sm:text-base min-h-[44px]" 
                                   style="border-color: #E5E7EB; color: #000000;">
                        </div>
                    </div>

                    <!-- Member Results -->
                    <div id="memberResults" class="space-y-4">
                        @foreach($members as $member)
                        <div class="member-card bg-white border rounded-lg p-4 sm:p-6 cursor-pointer transition-all duration-200 hover:shadow-md" 
                             style="border-color: #E5E7EB;" 
                             data-member='@json($member)'
                             onclick="selectMember(this)">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-2">
                                        <h3 class="text-base sm:text-lg font-semibold" style="color: #000000;">{{ $member->full_name }}</h3>
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
                                <div class="flex justify-end sm:ml-4">
                                    <button class="px-4 py-2 border rounded-lg font-medium transition-colors min-h-[44px] w-full sm:w-auto" 
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
                <div class="bg-white rounded-lg border p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8" style="color: #1E40AF;">Plan Selection & Payment</h2>
                    
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12">
                        <div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6" style="color: #1E40AF;">Plan Options</h3>
                            
                            <!-- Plan Cards -->
                            <div class="space-y-3 sm:space-y-4 mb-6 sm:mb-8">
                                    @foreach(config('membership.plan_types') as $key => $plan)
                                <div class="plan-card bg-white border rounded-lg p-4 sm:p-6 cursor-pointer transition-all duration-200 hover:shadow-md" 
                                     style="border-color: #E5E7EB;" 
                                     data-plan="{{ $key }}"
                                     onclick="selectPlan('{{ $key }}')">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                        <div class="flex-1">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-2">
                                                <h4 class="text-base sm:text-lg font-bold" style="color: #000000;">{{ $plan['name'] }}</h4>
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
                                            <p class="text-xs sm:text-sm mb-3" style="color: #6B7280;">{{ $plan['description'] }}</p>
                                            <div class="text-base sm:text-lg font-bold" style="color: #000000;">₱{{ number_format($plan['base_price'], 2) }}/month</div>
                                        </div>
                                        <div class="flex justify-end sm:ml-4">
                                            <button class="select-plan-btn px-4 py-2 border rounded-lg font-medium transition-colors min-h-[44px] w-full sm:w-auto" 
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
                            <div class="mb-6 sm:mb-8">
                                <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4" style="color: #1E40AF;">Duration</h4>
                                <div class="grid grid-cols-2 sm:flex gap-2">
                                    @foreach(config('membership.duration_types') as $key => $duration)
                                    <button class="duration-btn px-3 sm:px-4 py-2 border rounded-lg font-medium transition-colors min-h-[44px] text-xs sm:text-sm" 
                                            style="border-color: #E5E7EB; color: #6B7280;" 
                                            data-duration="{{ $key }}"
                                            onclick="selectDuration('{{ $key }}')"
                                            onmouseover="if(!this.classList.contains('selected')) this.style.backgroundColor='#F3F4F6'" 
                                            onmouseout="if(!this.classList.contains('selected')) this.style.backgroundColor='transparent'">
                                        {{ $duration['name'] }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="mb-6 sm:mb-8">
                                <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Membership Start Date</label>
                                <input type="date" id="startDate" name="start_date" 
                                       class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors min-h-[44px]" 
                                       style="border-color: #E5E7EB;" required>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg sm:text-xl font-semibold mb-4 sm:mb-6" style="color: #1E40AF;">Payment Details</h3>
                            
                            <!-- Price Calculation -->
                            <div id="priceCalculation" class="bg-gray-50 border rounded-lg p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 text-center" style="border-color: #E5E7EB;">
                                <div class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3" style="color: #000000;" id="totalPrice">₱0.00</div>
                                <div class="text-sm sm:text-base lg:text-lg" style="color: #6B7280;" id="priceBreakdown">Select plan and duration</div>
                            </div>

                            <!-- Payment Form -->
                            <div class="space-y-4 sm:space-y-6">
                                <!-- Discount Options -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold mb-3" style="color: #1E40AF;">Discount Options</h4>
                                    <div class="space-y-3">
                                        <label class="flex items-center">
                                            <input type="checkbox" id="isPwd" name="is_pwd" value="1" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                                   onchange="calculateDiscount()">
                                            <span class="ml-2 text-sm font-medium" style="color: #374151;">PWD (Person With Disability) - 20% discount</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" id="isSeniorCitizen" name="is_senior_citizen" value="1" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                                                   onchange="calculateDiscount()">
                                            <span class="ml-2 text-sm font-medium" style="color: #374151;">Senior Citizen - 20% discount</span>
                                        </label>
                                    </div>
                                    <div id="discountInfo" class="mt-3 p-3 bg-green-50 border border-green-200 rounded hidden">
                                        <div class="text-sm" style="color: #059669;">
                                            <div id="discountBreakdown"></div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Original Amount</label>
                                    <input type="number" id="originalAmount" step="0.01" min="0" 
                                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 min-h-[44px]" 
                                           style="border-color: #E5E7EB;" readonly>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Discount Amount</label>
                                    <input type="number" id="discountAmount" name="discount_amount" step="0.01" min="0" 
                                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 min-h-[44px]" 
                                           style="border-color: #E5E7EB;" readonly>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Final Payment Amount</label>
                                    <input type="number" id="paymentAmount" name="amount" step="0.01" min="0" 
                                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-gray-50 min-h-[44px]" 
                                           style="border-color: #E5E7EB;" readonly>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium mb-3" style="color: #6B7280;">Notes (Optional)</label>
                                    <textarea name="notes" rows="3" placeholder="Any additional notes about this payment..." 
                                              class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none min-h-[44px]" 
                                              style="border-color: #E5E7EB;"></textarea>
                                </div>

                                <!-- Confirm Payment Button -->
                                <button id="confirmPaymentBtn" onclick="showReceiptPreview()" 
                                        class="w-full py-3 sm:py-4 text-white font-semibold text-base sm:text-lg rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm flex items-center justify-center gap-3 min-h-[44px]" 
                                        style="background-color: #059669;" 
                                        onmouseover="this.style.backgroundColor='#047857'" 
                                        onmouseout="this.style.backgroundColor='#059669'" 
                                        disabled>
                                    <span class="text-lg sm:text-xl">💳</span>
                                    <span class="text-sm sm:text-base">Preview Receipt & Confirm Payment</span>
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
                    <p class="text-sm mb-4" style="color: #9CA3AF;">Redirecting to payments list in 1 second...</p>
                    <div class="flex justify-center space-x-4">
                        <button onclick="resetForm()" 
                                class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors shadow-sm" 
                                style="background-color: #059669;" 
                                onmouseover="this.style.backgroundColor='#047857'" 
                                onmouseout="this.style.backgroundColor='#059669'">
                            <span class="text-lg mr-2">➕</span>
                            Process Another Payment
                        </button>
                        <button onclick="printReceipt()" 
                                class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors shadow-sm" 
                                style="background-color: #DC2626;" 
                                onmouseover="this.style.backgroundColor='#B91C1C'" 
                                onmouseout="this.style.backgroundColor='#DC2626'">
                            <span class="text-lg mr-2">🖨️</span>
                            Print Receipt
                        </button>
                        <a href="{{ route('membership.payments.index') }}" 
                           class="inline-flex items-center px-6 py-3 text-white rounded-lg transition-colors shadow-sm" 
                           style="background-color: #2563EB;" 
                           onmouseover="this.style.backgroundColor='#1D4ED8'" 
                           onmouseout="this.style.backgroundColor='#2563EB'">
                            <span class="text-lg mr-2">👁️</span>
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
                btn.classList.remove('selected');
            });
            
            // Highlight selected duration
            const selectedBtn = document.querySelector(`[data-duration="${durationType}"]`);
            selectedBtn.style.backgroundColor = '#1E40AF';
            selectedBtn.style.color = '#FFFFFF';
            selectedBtn.style.borderColor = '#1E40AF';
            selectedBtn.classList.add('selected');
            
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
                const originalPrice = basePrice * multiplier;
                
                // Set original amount
                document.getElementById('originalAmount').value = originalPrice.toFixed(2);
                
                // Calculate discount
                calculateDiscount();
                
                document.getElementById('totalPrice').textContent = `₱${originalPrice.toFixed(2)}`;
            document.getElementById('priceBreakdown').textContent = `${planTypes[selectedPlanType].name} (₱${basePrice}/month) × ${durationTypes[selectedDurationType].name} (${multiplier}x)`;
                
                // Enable confirm button
                document.getElementById('confirmPaymentBtn').disabled = false;
            }
        }

        function calculateDiscount() {
            const originalAmount = parseFloat(document.getElementById('originalAmount').value) || 0;
            const isPwd = document.getElementById('isPwd').checked;
            const isSeniorCitizen = document.getElementById('isSeniorCitizen').checked;
            
            let discountPercentage = 0;
            let discountDescriptions = [];
            
            if (isPwd) {
                discountPercentage += 20;
                discountDescriptions.push('PWD (20%)');
            }
            
            if (isSeniorCitizen) {
                discountPercentage += 20;
                discountDescriptions.push('Senior Citizen (20%)');
            }
            
            const discountAmount = (originalAmount * discountPercentage) / 100;
            const finalAmount = originalAmount - discountAmount;
            
            // Update discount fields
            document.getElementById('discountAmount').value = discountAmount.toFixed(2);
            document.getElementById('paymentAmount').value = finalAmount.toFixed(2);
            
            // Show/hide discount info
            const discountInfo = document.getElementById('discountInfo');
            const discountBreakdown = document.getElementById('discountBreakdown');
            
            if (discountPercentage > 0) {
                discountInfo.classList.remove('hidden');
                discountBreakdown.innerHTML = `
                    <strong>Discount Applied:</strong> ${discountDescriptions.join(' + ')}<br>
                    <strong>Total Discount:</strong> ${discountPercentage}% (₱${discountAmount.toFixed(2)})<br>
                    <strong>Final Amount:</strong> ₱${finalAmount.toFixed(2)}
                `;
            } else {
                discountInfo.classList.add('hidden');
            }
        }

        function showReceiptPreview() {
            if (!selectedMember || !selectedPlanType || !selectedDurationType) {
                alert('Please select all required fields');
                return;
            }

            const planTypes = @json(config('membership.plan_types'));
            const durationTypes = @json(config('membership.duration_types'));
            const originalAmount = parseFloat(document.getElementById('originalAmount').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;
            const finalAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
            const isPwd = document.getElementById('isPwd').checked;
            const isSeniorCitizen = document.getElementById('isSeniorCitizen').checked;
            const startDate = document.getElementById('startDate').value;
            const notes = document.querySelector('textarea[name="notes"]').value;

            // Calculate expiration date
            const start = new Date(startDate);
            const durationDays = durationTypes[selectedDurationType].days;
            const expiration = new Date(start.getTime() + (durationDays * 24 * 60 * 60 * 1000));

            // Create modal without background overlay - appears as floating popup
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 flex items-center justify-center z-50 pointer-events-none';
            modal.style.backgroundColor = 'transparent';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-2xl w-[700px] max-w-[90vw] max-h-[90vh] overflow-auto transform transition-all duration-300 scale-95 opacity-0 border border-gray-300 pointer-events-auto"
                     id="receiptModalContent"
                     style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
                    <!-- Modal Header with Close Button -->
                    <div class="flex justify-between items-center p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Payment Receipt</h3>
                        <button onclick="window.closeReceiptPreviewModal()" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Receipt Content -->
                    <div class="p-6">
                        <!-- Header Section with Logo and Gym Info -->
                        <div class="text-center mb-6 border-b border-gray-200 pb-4">
                            <div class="flex justify-center mb-3">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center border-2 border-orange-500 shadow-lg">
                                    <img src="{{ asset('images/rba-logo/rba logo.png') }}" alt="RBA Logo" class="w-12 h-12 object-contain">
                                </div>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-1">Ripped Body Anytime</h2>
                            <p class="text-sm text-gray-600 mb-1">Blk. 168 Deparo, City of Caloocan</p>
                            <p class="text-sm text-gray-600 mb-1">Caloocan, Philippines, 1400</p>
                            <p class="text-sm text-gray-600">Contact: +63 123 456 7890</p>
                        </div>

                        <!-- Payment Receipt Title -->
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Receipt</h3>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-3xl font-bold text-green-600 mb-1">₱${finalAmount.toFixed(2)}</p>
                                <p class="text-sm text-gray-600">${planTypes[selectedPlanType].name} Membership | ${durationTypes[selectedDurationType].name}</p>
                            </div>
                        </div>

                        <!-- Payment Details Section -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                </svg>
                                Payment Details
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Receipt ID</p>
                                    <p class="font-semibold text-gray-900">RBA-${Date.now().toString().slice(-6)}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-semibold text-gray-900">${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Time</p>
                                    <p class="font-semibold text-gray-900">${new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Payment Method</p>
                                    <p class="font-semibold text-gray-900">Cash</p>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Details Section -->
                        <div class="mb-6 bg-blue-50 rounded-lg p-4">
                            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Membership Details
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Member Name</p>
                                    <p class="font-semibold text-gray-900">${selectedMember.first_name} ${selectedMember.last_name}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Plan Type</p>
                                    <p class="font-semibold text-gray-900">${planTypes[selectedPlanType].name}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Duration</p>
                                    <p class="font-semibold text-gray-900">${durationTypes[selectedDurationType].name}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Start Date</p>
                                    <p class="font-semibold text-gray-900">${new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-600">Expiration Date</p>
                                    <p class="font-semibold text-green-600">${expiration.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary Section -->
                        <div class="mb-6 bg-green-50 rounded-lg p-4 border border-green-200">
                            <h4 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7 18c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                                Payment Summary
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Original Amount</span>
                                    <span class="font-semibold text-gray-900">₱${originalAmount.toFixed(2)}</span>
                                </div>
                                ${originalAmount !== finalAmount ? `
                                    <div class="flex justify-between items-center text-red-600">
                                        <span>Discount Applied</span>
                                        <span class="font-semibold">-₱${discountAmount.toFixed(2)}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm text-gray-600">
                                        <span>Discount Type</span>
                                        <span>${document.getElementById('is_pwd').checked ? 'PWD Discount' : document.getElementById('is_senior_citizen').checked ? 'Senior Citizen Discount' : 'Special Discount'}</span>
                                    </div>
                                ` : ''}
                                <div class="border-t border-green-300 pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total Amount</span>
                                        <span class="text-2xl font-bold text-green-600">₱${finalAmount.toFixed(2)}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Change</span>
                                    <span class="font-semibold text-gray-900">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Message -->
                        <div class="text-center border-t border-gray-200 pt-4 mt-6">
                            <div class="mb-3">
                                <p class="text-sm font-medium text-gray-700 mb-1">Thank you for choosing Ripped Body Anytime!</p>
                                <p class="text-xs text-gray-500">Your fitness journey starts here</p>
                            </div>
                            <div class="text-xs text-gray-400 space-y-1">
                                <p>Generated on ${new Date().toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}</p>
                                <p>Powered by Silencio Gym Management System</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                        <button onclick="window.handlePaymentConfirmationClick()" type="button" class="w-full px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-semibold text-base shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            Confirm Payment & Activate Membership
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // No click outside to close since there's no background overlay

            // Trigger animation
            setTimeout(() => {
                const content = document.getElementById('receiptModalContent');
                if (content) {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
        }

        // Define window-level functions for modal buttons
        window.closeReceiptPreviewModal = function() {
            console.log('closeReceiptPreviewModal called');
            closeReceiptPreview();
        };

        window.handlePaymentConfirmationClick = async function() {
            console.log('handlePaymentConfirmationClick called');
            await handlePaymentConfirmation();
        };

        function closeReceiptPreview() {
            console.log('closeReceiptPreview called');
            const modal = document.querySelector('.fixed.inset-0.flex');
            const content = document.getElementById('receiptModalContent');

            console.log('Modal element:', modal);
            console.log('Content element:', content);

            if (content) {
                // Trigger close animation
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    if (modal) {
                        console.log('Removing modal from DOM');
                        modal.remove();
                    }
                }, 300);
            } else if (modal) {
                console.log('Removing modal immediately (no content element)');
                modal.remove();
            } else {
                console.error('No modal found to close!');
            }
        }

        // Handler for payment confirmation button click
        async function handlePaymentConfirmation() {
            console.log('Payment confirmation clicked');
            console.log('Selected member:', selectedMember);

            try {
                await processPayment();
            } catch (error) {
                console.error('Error in payment confirmation:', error);
                alert('An error occurred. Please check the console for details.');
            }
        }

        async function processPayment() {
            console.log('processPayment called');

            // First, check for active membership
            console.log('Checking active membership for member ID:', selectedMember.id);
            const membershipCheck = await PaymentValidation.checkActiveMembership(selectedMember.id);
            console.log('Membership check result:', membershipCheck);

            if (membershipCheck.has_active_plan) {
                console.log('Active plan found, showing modal');
                // Close the receipt preview modal first
                closeReceiptPreview();

                if (PaymentValidation.isAdmin) {
                    // Show admin warning modal
                    const message = `This member already has an active membership plan: ${membershipCheck.plan_name} (Expires: ${membershipCheck.expiration_date}). Proceeding will create a duplicate plan. Are you sure you want to continue?`;
                    console.log('Showing admin warning modal');
                    PaymentValidation.showAdminWarning(message);
                    return;
                } else {
                    // Show employee error modal
                    const message = `Cannot process payment. This member already has an active membership plan: ${membershipCheck.plan_name} (Expires: ${membershipCheck.expiration_date})`;
                    console.log('Showing employee error modal');
                    PaymentValidation.showEmployeeError(message);
                    return;
                }
            }

            console.log('No active plan found, proceeding with payment');
            // If no active membership, proceed with payment
            executePayment(false);
        }

        // Function to execute payment with optional admin override
        window.processPaymentWithOverride = function() {
            executePayment(true);
        };

        function executePayment(adminOverride = false) {
            // Validate required data
            if (!selectedMember || !selectedMember.id) {
                console.error('No member selected!');
                alert('Please select a member first.');
                return;
            }

            if (!selectedPlanType) {
                console.error('No plan type selected!');
                alert('Please select a plan type.');
                return;
            }

            if (!selectedDurationType) {
                console.error('No duration type selected!');
                alert('Please select a duration type.');
                return;
            }

            const paymentAmountEl = document.getElementById('paymentAmount');
            const startDateEl = document.getElementById('startDate');
            const notesEl = document.querySelector('textarea[name="notes"]');
            const isPwdEl = document.getElementById('isPwd');
            const isSeniorCitizenEl = document.getElementById('isSeniorCitizen');
            const discountAmountEl = document.getElementById('discountAmount');

            if (!paymentAmountEl || !startDateEl) {
                console.error('Required form elements not found!');
                alert('Form is not properly loaded. Please refresh the page.');
                return;
            }

            const formData = {
                member_id: selectedMember.id,
                plan_type: selectedPlanType,
                duration_type: selectedDurationType,
                amount: paymentAmountEl.value,
                start_date: startDateEl.value,
                notes: notesEl ? notesEl.value : '',
                is_pwd: isPwdEl ? (isPwdEl.checked ? 1 : 0) : 0,
                is_senior_citizen: isSeniorCitizenEl ? (isSeniorCitizenEl.checked ? 1 : 0) : 0,
                discount_amount: discountAmountEl ? discountAmountEl.value : 0,
                discount_percentage: (isPwdEl && isSeniorCitizenEl && isPwdEl.checked && isSeniorCitizenEl.checked) ? 40 :
                                   ((isPwdEl && isPwdEl.checked) || (isSeniorCitizenEl && isSeniorCitizenEl.checked) ? 20 : 0),
                admin_override: adminOverride,
                override_reason: adminOverride ? 'Admin override for duplicate membership plan' : null,
                _token: '{{ csrf_token() }}'
            };

            console.log('Form data to submit:', formData);

            // Show loading state
            const confirmBtn = document.querySelector('button[onclick="window.handlePaymentConfirmationClick()"]');
            if (!confirmBtn) {
                console.error('Confirm button not found!');
                alert('An error occurred. Please refresh the page and try again.');
                return;
            }
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
            confirmBtn.disabled = true;

            fetch('{{ route("membership.process-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok && response.status !== 422) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Close the receipt preview modal first
                    closeReceiptPreview();

                    // Show appropriate success message
                    if (adminOverride) {
                        // Show admin success modal for override
                        PaymentValidation.showAdminSuccess();
                    } else {
                        // Show regular success message
                        showPaymentSuccessMessage(data);
                    }

                    // Store payment ID for receipt generation
                    window.lastPaymentId = data.payment_id;

                    // Reset the form for next payment
                    resetPaymentForm();

                    // Refresh member data if needed
                    if (selectedMember) {
                        // Update member display to show new membership status
                        updateMemberDisplay(selectedMember.id);
                    }
                } else {
                    // Handle validation errors
                    if (data.error === 'ACTIVE_MEMBERSHIP_EXISTS') {
                        if (PaymentValidation.isAdmin) {
                            PaymentValidation.showAdminWarning(data.message);
                        } else {
                            PaymentValidation.showEmployeeError(data.message);
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                closeReceiptPreview();
                
                // Provide more specific error messages
                let errorMessage = 'An error occurred while processing the payment.';
                if (error.message.includes('HTTP error')) {
                    errorMessage = 'Server error occurred. Please try again.';
                } else if (error.message.includes('NetworkError')) {
                    errorMessage = 'Network error. Please check your connection.';
                } else if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Connection failed. Please refresh the page and try again.';
                }
                
                alert(errorMessage + ' Error details: ' + error.message);
                if (confirmBtn) {
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                }
            });
        }

        function showPaymentSuccessMessage(data) {
            // Create success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <div class="font-semibold">Payment Successful!</div>
                        <div class="text-sm">Membership activated for ${selectedMember.first_name} ${selectedMember.last_name}</div>
                        <div class="text-xs mt-1">Payment ID: ${data.payment_id}</div>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        function resetPaymentForm() {
            // Reset payment-specific fields only
            document.getElementById('paymentAmount').value = '';
            document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
            document.querySelector('textarea[name="notes"]').value = '';
            document.getElementById('isPwd').checked = false;
            document.getElementById('isSeniorCitizen').checked = false;

            // Reset plan and duration selections
            selectedPlanType = null;
            selectedDurationType = null;

            // Reset UI for plan selection
            document.querySelectorAll('.plan-type-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            });

            document.querySelectorAll('.duration-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50');
            });

            // Hide discount info
            document.getElementById('discountInfo').classList.add('hidden');

            // Reset price display
            document.getElementById('totalPrice').textContent = '₱0.00';
            document.getElementById('priceBreakdown').textContent = 'Select plan and duration';

            // Update confirm button
            updateConfirmButton();
        }

        function updateMemberDisplay(memberId) {
            // Refresh the member card to show updated membership status
            // This could be enhanced to make an AJAX call to get updated member data
            console.log('Member display updated for ID:', memberId);

            // For now, just show a visual indicator that the member was updated
            const memberCards = document.querySelectorAll('.member-card');
            memberCards.forEach(card => {
                const memberIdElement = card.querySelector('[data-member-id]');
                if (memberIdElement && memberIdElement.dataset.memberId == memberId) {
                    // Add a temporary success indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full';
                    indicator.textContent = 'Updated';
                    card.style.position = 'relative';
                    card.appendChild(indicator);

                    // Remove indicator after 3 seconds
                    setTimeout(() => {
                        if (indicator.parentNode) {
                            indicator.parentNode.removeChild(indicator);
                        }
                    }, 3000);
                }
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
            
            // Clear payment ID
            window.lastPaymentId = null;
        }

        function printReceipt() {
            if (window.lastPaymentId) {
                // Open receipt in new window for printing
                const receiptUrl = `/membership/payments/${window.lastPaymentId}/print`;
                window.open(receiptUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
            } else {
                alert('No payment ID available for receipt generation');
            }
        }
    </script>

    {{-- Include payment validation modals --}}
    @include('components.payment-validation-modals')
</x-layout>
