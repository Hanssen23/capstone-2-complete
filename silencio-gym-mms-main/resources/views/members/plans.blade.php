<x-layout>
    <x-nav-member></x-nav-member>
    <div class="flex-1 bg-white">
        <x-topbar>Membership Plans</x-topbar>

        <div class="bg-white min-h-screen p-6">
            <!-- Plan Types Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold" style="color: #1E40AF;">Plan Types</h2>
                        <div class="flex items-center space-x-2">
                            <div id="realtime-indicator" class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></div>
                            <span class="text-sm text-gray-600">Real-time</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 lg:gap-10">
                        @foreach($plans as $plan)
                        <!-- Flip Card Container -->
                        <div class="flip-card" style="perspective: 1000px; height: 550px;">
                            <div class="flip-card-inner" id="flip-card-{{ $plan->id }}" style="position: relative; width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d;">

                                <!-- Front Side - Plan Details -->
                                <div class="flip-card-front" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden;">
                                    <div class="bg-white border rounded-xl p-6 sm:p-8 hover:shadow-xl transition-shadow h-full flex flex-col" style="border-color: #D1D5DB; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
                                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                            @if($plan->name === 'VIP' || $plan->name === 'Premium')
                                            <span class="px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-full bg-amber-500 text-white whitespace-nowrap">
                                                {{ $plan->currency ?? '₱' }}{{ number_format($plan->price, 2) }}/month
                                            </span>
                                            @else
                                            <span class="px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-full bg-green-600 text-white whitespace-nowrap">
                                                {{ $plan->currency ?? '₱' }}{{ number_format($plan->price, 2) }}/month
                                            </span>
                                            @endif
                                        </div>
                                        <p class="mb-6 leading-relaxed text-sm sm:text-base text-gray-600">{{ $plan->description }}</p>
                                        <div class="space-y-3 mb-6 flex-grow">
                                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <span class="text-xs sm:text-sm text-gray-700 font-medium">Monthly:</span>
                                                <span class="font-bold text-xs sm:text-sm text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price, 2) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <span class="text-xs sm:text-sm text-gray-700 font-medium">Quarterly:</span>
                                                <span class="font-bold text-xs sm:text-sm text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 3, 2) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <span class="text-xs sm:text-sm text-gray-700 font-medium">Biannually:</span>
                                                <span class="font-bold text-xs sm:text-sm text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 6, 2) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-lg border border-gray-200">
                                                <span class="text-xs sm:text-sm text-gray-700 font-medium">Annually:</span>
                                                <span class="font-bold text-xs sm:text-sm text-gray-900">{{ $plan->currency ?? '₱' }}{{ number_format($plan->price * 12, 2) }}</span>
                                            </div>
                                        </div>
                                        <button onclick="flipCard('{{ $plan->id }}')" class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                            View Benefits
                                        </button>
                                    </div>
                                </div>

                                <!-- Back Side - Benefits -->
                                <div class="flip-card-back" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; transform: rotateY(180deg);">
                                    <div class="bg-white border rounded-xl p-6 sm:p-8 h-full flex flex-col" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-color: #D1D5DB;">
                                        <div class="flex items-center justify-between mb-6">
                                            <h3 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $plan->name }} Benefits</h3>
                                            <button onclick="flipCard('{{ $plan->id }}')" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex-grow overflow-y-auto pr-3">
                                            @if($plan->features && count($plan->features) > 0)
                                                <div class="space-y-3">
                                                    @foreach($plan->features as $benefit)
                                                    <div class="flex items-start space-x-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                        <div class="flex-shrink-0 mt-0.5">
                                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <circle cx="10" cy="10" r="10"/>
                                                                <path d="M14.5 6.5L8.5 12.5L5.5 9.5" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                        </div>
                                                        <p class="text-gray-700 text-sm font-medium leading-relaxed">{{ $benefit }}</p>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500 text-sm italic">No benefits added yet. Contact admin for more information.</p>
                                            @endif
                                        </div>
                                        <button onclick="flipCard('{{ $plan->id }}')" class="w-full px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors mt-6">
                                            Back to Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Duration Types Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-3xl font-bold" style="color: #1E40AF;">Duration Types</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        @foreach(config('membership.duration_types') as $key => $duration)
                        <div class="bg-white border rounded-lg p-6" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div class="text-center">
                                <h3 class="text-xl font-bold mb-4" style="color: #000000;">{{ $duration['name'] }}</h3>
                                <div class="text-4xl font-bold mb-3" style="color: #1E40AF;">{{ $duration['multiplier'] }}x</div>
                                <p class="text-sm mb-6" style="color: #6B7280;">{{ $duration['days'] }} days</p>
                                <div class="space-y-3 mb-6">
                                    @foreach(config('membership.plan_types') as $planKey => $plan)
                                    <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded border" style="border-color: #E5E7EB;">
                                        <span class="text-sm" style="color: #374151;">{{ $plan['name'] }}:</span>
                                        <span class="font-semibold" style="color: #000000;">₱{{ number_format($plan['base_price'] * $duration['multiplier'], 2) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <p class="text-xs" style="color: #6B7280;">Read-only view</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pricing Calculator Preview -->
            <div class="mb-8">
                <div class="bg-white rounded-lg border p-8" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <h2 class="text-3xl font-bold mb-8" style="color: #1E40AF;">Pricing Calculator</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <div>
                            <h3 class="text-xl font-bold mb-6" style="color: #000000;">Plan Type + Duration = Total Price</h3>
                            <div class="space-y-4">
                                @foreach(config('membership.plan_types') as $planKey => $plan)
                                    @foreach(config('membership.duration_types') as $durationKey => $duration)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border hover:bg-gray-100 transition-colors" style="border-color: #E5E7EB;">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium" style="color: #000000;">{{ $plan['name'] }}</span>
                                            <span style="color: #6B7280;">+</span>
                                            <span class="font-medium" style="color: #000000;">{{ $duration['name'] }}</span>
                                        </div>
                                        <span class="font-bold text-lg" style="color: #000000;">₱{{ number_format($plan['base_price'] * $duration['multiplier'], 2) }}</span>
                                    </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-6" style="color: #000000;">Current Membership</h3>
                            <div class="bg-gray-50 border rounded-lg p-6" style="border-color: #E5E7EB;">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background-color: #1E40AF;">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-bold mb-1" style="color: #1E40AF;">Your Membership</h4>
                                        <p style="color: #6B7280;">View your current plan details</p>
                                    </div>
                                </div>
                                <a href="/member" class="block w-full px-4 py-3 text-white text-center rounded-lg transition-colors" 
                                   style="background-color: #2563EB;" 
                                   onmouseover="this.style.backgroundColor='#1D4ED8'" 
                                   onmouseout="this.style.backgroundColor='#2563EB'">
                                    Go to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let plans = [];
        let pricing = [];
        let lastPlansHash = '';
        let lastPricingHash = '';

        // Real-time updates every 15 seconds for better responsiveness (fallback only)
        // setInterval(function() {
        //     loadPlansData();
        // }, 15000);

        function loadPlansData() {
            // Load active plans
            fetch('{{ route("member.membership-plans") }}')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        const newHash = JSON.stringify(data);
                        if (newHash !== lastPlansHash) {
                            plans = data;
                            updatePlansDisplay();
                            lastPlansHash = newHash;
                        }
                    }
                })
                .catch(error => console.error('Error loading plans:', error));

            // Load pricing information
            fetch('{{ route("member.membership-pricing") }}')
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        const newHash = JSON.stringify(data);
                        if (newHash !== lastPricingHash) {
                            pricing = data;
                            updatePricingDisplay();
                            lastPricingHash = newHash;
                        }
                    }
                })
                .catch(error => console.error('Error loading pricing:', error));
        }

        function updatePlansDisplay() {
            // Update the plans display with real-time data
            const plansContainer = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3');
            if (plansContainer) {
                // Fetch fresh data and update DOM without page reload
                fetch('{{ route("member.membership-pricing") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.plans) {
                            plans = data.plans;
                            renderPlans(plans);
                            showNotification('Plans updated in real-time', 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating plans:', error);
                        // No fallback reload - just log the error
                    });
            }
        }

        function renderPlans(plansData) {
            const plansContainer = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2.lg\\:grid-cols-3');
            if (!plansContainer) return;

            // Store current flip states before updating
            const flipStates = {};
            document.querySelectorAll('[id^="flip-card-"]').forEach(card => {
                flipStates[card.id] = card.style.transform;
            });

            const plansHTML = plansData.map(plan => `
                <!-- Flip Card Container -->
                <div class="flip-card" style="perspective: 1000px; height: 500px;">
                    <div class="flip-card-inner" id="flip-card-${plan.id}" style="position: relative; width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d;">

                        <!-- Front Side - Plan Details -->
                        <div class="flip-card-front" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden;">
                            <div class="bg-white border rounded-lg p-4 sm:p-6 hover:shadow-lg transition-shadow h-full flex flex-col" style="border-color: #E5E7EB; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">${plan.name}</h3>
                                    <span class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium rounded-full text-white" style="background-color: ${plan.name === 'VIP' || plan.name === 'Premium' ? '#F59E0B' : '#059669'};">
                                        ₱${parseFloat(plan.price).toFixed(2)}/month
                                    </span>
                                </div>
                                <p class="mb-4 leading-relaxed text-sm sm:text-base text-gray-600">${plan.description}</p>
                                <div class="space-y-2 mb-4 flex-grow">
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-700">Monthly:</span>
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900">₱${parseFloat(plan.price).toFixed(2)}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-700">Quarterly:</span>
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900">₱${(parseFloat(plan.price) * 3).toFixed(2)}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-700">Biannually:</span>
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900">₱${(parseFloat(plan.price) * 6).toFixed(2)}</span>
                                    </div>
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded border border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-700">Annually:</span>
                                        <span class="font-semibold text-xs sm:text-sm text-gray-900">₱${(parseFloat(plan.price) * 12).toFixed(2)}</span>
                                    </div>
                                </div>
                                <button onclick="flipCard('${plan.id}')" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors mb-3">
                                    View Benefits
                                </button>
                            </div>
                        </div>

                        <!-- Back Side - Benefits -->
                        <div class="flip-card-back" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; -webkit-backface-visibility: hidden; transform: rotateY(180deg);">
                            <div class="bg-white border rounded-lg p-4 sm:p-6 h-full flex flex-col" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">${plan.name} Benefits</h3>
                                    <button onclick="flipCard('${plan.id}')" class="text-gray-600 hover:text-gray-800">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-grow overflow-y-auto pr-3">
                                    ${plan.features && plan.features.length > 0 ?
                                        `<div class="space-y-3">
                                            ${plan.features.map(benefit => `
                                                <div class="flex items-start space-x-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <circle cx="10" cy="10" r="10"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-gray-700 text-sm font-medium leading-relaxed">${benefit}</p>
                                                </div>
                                            `).join('')}
                                        </div>` :
                                        `<p class="text-gray-600 text-sm italic">No benefits added yet. Contact admin for more information.</p>`
                                    }
                                </div>
                                <button onclick="flipCard('${plan.id}')" class="w-full px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors mt-4">
                                    Back to Details
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            `).join('');

            plansContainer.innerHTML = plansHTML;

            // Restore flip states after updating
            setTimeout(() => {
                Object.keys(flipStates).forEach(cardId => {
                    const card = document.getElementById(cardId);
                    if (card && flipStates[cardId]) {
                        card.style.transform = flipStates[cardId];
                    }
                });
            }, 0);
        }

        function updatePricingDisplay() {
            // Update pricing display with real-time data
            if (pricing.length > 0) {
                // Update the pricing calculator section
                const pricingContainer = document.querySelector('.space-y-4');
                if (pricingContainer) {
                    const newPricingHTML = pricing.map(planPricing => {
                        return Object.values(planPricing.durations || {}).map(duration => `
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border hover:bg-gray-100 transition-colors" style="border-color: #E5E7EB;">
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium" style="color: #000000;">${planPricing.plan_name}</span>
                                    <span style="color: #6B7280;">+</span>
                                    <span class="font-medium" style="color: #000000;">${duration.name}</span>
                                </div>
                                <span class="font-bold text-lg" style="color: #000000;">₱${parseFloat(duration.price).toFixed(2)}</span>
                            </div>
                        `).join('');
                    }).join('');

                    // Update pricing section if it exists
                    const pricingSection = document.querySelector('.space-y-4');
                    if (pricingSection && newPricingHTML) {
                        pricingSection.innerHTML = newPricingHTML;
                        
                        // Add visual feedback
                        pricingSection.style.opacity = '0.7';
                        setTimeout(() => {
                            pricingSection.style.opacity = '1';
                        }, 200);
                    }
                }
                
                console.log('Pricing updated:', pricing);
            }
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300';

            if (type === 'success') {
                notification.style.backgroundColor = '#059669';
                notification.style.color = '#FFFFFF';
            } else if (type === 'error') {
                notification.style.backgroundColor = '#DC2626';
                notification.style.color = '#FFFFFF';
            } else {
                notification.style.backgroundColor = '#2563EB';
                notification.style.color = '#FFFFFF';
            }

            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="text-xl">${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}</span>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Flip card function
        function flipCard(planId) {
            const card = document.getElementById('flip-card-' + planId);
            if (card.style.transform === 'rotateY(180deg)') {
                card.style.transform = 'rotateY(0deg)';
            } else {
                card.style.transform = 'rotateY(180deg)';
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Disabled to prevent auto-refresh
            // loadPlansData();
            // initializeSSE();
        });

        // Update real-time indicator status
        function updateRealtimeIndicator(isActive) {
            const indicator = document.getElementById('realtime-indicator');
            if (indicator) {
                if (isActive) {
                    indicator.className = 'w-3 h-3 rounded-full bg-green-500 animate-pulse';
                } else {
                    indicator.className = 'w-3 h-3 rounded-full bg-red-500';
                }
            }
        }

        // Initialize Server-Sent Events for real-time updates
        function initializeSSE() {
            if (typeof(EventSource) !== "undefined") {
                const eventSource = new EventSource('{{ route("member.membership-plans.stream") }}');
                
                eventSource.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        // Update real-time indicator
                        updateRealtimeIndicator(true);
                        
                        if (data.plans_changed) {
                            plans = data.plans;
                            updatePlansDisplay();
                        }
                        
                        if (data.pricing_changed) {
                            pricing = data.pricing;
                            updatePricingDisplay();
                        }
                        
                        console.log('Real-time update received:', data.timestamp);
                    } catch (error) {
                        console.error('Error parsing SSE data:', error);
                        updateRealtimeIndicator(false);
                    }
                };
                
                eventSource.onerror = function(event) {
                    console.error('SSE connection error:', event);
                    updateRealtimeIndicator(false);
                    // Fallback to polling if SSE fails
                    setTimeout(() => {
                        loadPlansData();
                    }, 5000);
                };
                
                // Clean up on page unload
                window.addEventListener('beforeunload', function() {
                    eventSource.close();
                });
            } else {
                console.log('SSE not supported');
                // Polling disabled to prevent auto-refresh
            }
        }
    </script>
</x-layout>


