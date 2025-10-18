{{-- Employee Error Modal --}}
<div id="employeeErrorModal" class="fixed inset-0 flex items-center justify-center z-[10000] p-4 hidden" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="relative p-5 border w-96 shadow-2xl rounded-lg bg-white" style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cannot Process Payment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-700" id="employeeErrorMessage">
                    This member already has an active membership plan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button" id="employeeErrorClose" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Admin Warning Modal (First Confirmation) --}}
<div id="adminWarningModal" class="fixed inset-0 flex items-center justify-center z-[10001] p-4 hidden" style="background-color: rgba(0, 0, 0, 0.6);">
    <div class="relative p-6 border w-[500px] max-w-[90vw] shadow-2xl rounded-lg bg-white" style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <div class="text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <!-- Title -->
            <h3 class="text-xl leading-6 font-bold text-gray-900 mb-4">⚠️ Member Has Active Plan</h3>

            <!-- Message -->
            <div class="mb-8 px-4">
                <p class="text-base text-gray-700 leading-relaxed" id="adminWarningMessage">
                    This member already has an active membership plan. The new membership will override their current membership. Do you want to continue?
                </p>
            </div>

            <!-- Buttons -->
            <div class="space-y-3">
                <button type="button" id="adminWarningContinue"
                        class="w-full px-6 py-4 bg-green-600 text-white text-base font-semibold rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 transition-colors cursor-pointer">
                    Yes, Override Current Membership
                </button>
                <button type="button" id="adminWarningCancel"
                        class="w-full px-6 py-4 bg-gray-300 text-gray-700 text-base font-semibold rounded-lg shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Admin Final Confirmation Modal (With Countdown) --}}
<div id="adminFinalModal" class="fixed inset-0 flex items-center justify-center z-[10002] p-4 hidden" style="background-color: rgba(0, 0, 0, 0.6);">
    <div class="relative p-6 border w-[450px] max-w-[90vw] shadow-2xl rounded-lg bg-white" style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">⚠️ FINAL CONFIRMATION REQUIRED</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-700 mb-3">
                    You are about to create a duplicate membership plan for this member. This action should only be done in exceptional circumstances.
                </p>
                <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-3">
                    <div class="text-lg font-bold text-red-600" id="countdownDisplay">5</div>
                    <div class="text-xs text-red-500">seconds remaining</div>
                </div>
            </div>
            <div class="flex flex-col gap-3 px-4 py-3">
                <button type="button" id="adminFinalConfirm" class="w-full px-4 py-3 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50 disabled:cursor-not-allowed transition-colors cursor-pointer" disabled>
                    <span id="confirmButtonText">Please wait... (<span id="buttonCountdown">5</span> seconds)</span>
                </button>
                <button type="button" id="adminFinalCancel" class="w-full px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors cursor-pointer">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal for Admin Override --}}
<div id="adminSuccessModal" class="fixed inset-0 flex items-center justify-center z-[10003] p-4 hidden" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="relative p-5 border w-96 shadow-2xl rounded-lg bg-white" style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Payment Processed Successfully</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-700">
                    <strong>Note:</strong> A duplicate membership plan has been created for this member as requested.
                </p>
                <div class="mt-2 text-xs text-gray-500">
                    This action has been logged for audit purposes.
                </div>
            </div>
            <div class="items-center px-4 py-3">
                <button type="button" id="adminSuccessClose" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Payment validation system
window.PaymentValidation = {
    currentMemberData: null,
    isAdmin: {{ auth()->check() && auth()->user()->role === 'admin' ? 'true' : 'false' }},
    
    // Check for active membership before payment
    async checkActiveMembership(memberId) {
        console.log('PaymentValidation.checkActiveMembership called with member ID:', memberId);
        try {
            const url = '{{ route("membership.check-active-membership") }}';
            console.log('Fetching from URL:', url);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ member_id: memberId })
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            return data;
        } catch (error) {
            console.error('Error checking active membership:', error);
            return { has_active_plan: false };
        }
    },
    
    // Show employee error modal
    showEmployeeError(message) {
        console.log('Showing employee error modal with message:', message);
        const messageEl = document.getElementById('employeeErrorMessage');
        const modalEl = document.getElementById('employeeErrorModal');
        console.log('Message element:', messageEl);
        console.log('Modal element:', modalEl);

        if (messageEl && modalEl) {
            messageEl.textContent = message;
            modalEl.classList.remove('hidden');
            console.log('Employee error modal should now be visible');
        } else {
            console.error('Modal elements not found!');
        }
    },

    // Show admin warning modal
    showAdminWarning(message) {
        console.log('Showing admin warning modal with message:', message);
        const messageEl = document.getElementById('adminWarningMessage');
        const modalEl = document.getElementById('adminWarningModal');
        console.log('Message element:', messageEl);
        console.log('Modal element:', modalEl);

        if (messageEl && modalEl) {
            messageEl.textContent = message;
            modalEl.classList.remove('hidden');
            console.log('Admin warning modal should now be visible');
        } else {
            console.error('Modal elements not found!');
        }
    },
    
    // Show admin final confirmation with countdown
    showAdminFinalConfirmation() {
        document.getElementById('adminFinalModal').classList.remove('hidden');
        this.startCountdown();
    },
    
    // Start countdown timer
    startCountdown() {
        let seconds = 5;
        const countdownDisplay = document.getElementById('countdownDisplay');
        const buttonCountdown = document.getElementById('buttonCountdown');
        const confirmButton = document.getElementById('adminFinalConfirm');
        const confirmButtonText = document.getElementById('confirmButtonText');
        
        const timer = setInterval(() => {
            countdownDisplay.textContent = seconds;
            buttonCountdown.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                confirmButton.disabled = false;
                confirmButtonText.textContent = 'Confirm Payment';
                confirmButton.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
            }
            
            seconds--;
        }, 1000);
    },
    
    // Show success modal for admin override
    showAdminSuccess() {
        document.getElementById('adminSuccessModal').classList.remove('hidden');
    },
    
    // Hide all modals
    hideAllModals() {
        document.getElementById('employeeErrorModal').classList.add('hidden');
        document.getElementById('adminWarningModal').classList.add('hidden');
        document.getElementById('adminFinalModal').classList.add('hidden');
        document.getElementById('adminSuccessModal').classList.add('hidden');
    }
};

// Global functions for button clicks
function handleOverrideContinue() {
    console.log('Override Continue button clicked');
    document.getElementById('adminWarningModal').classList.add('hidden');
    if (window.processPaymentWithOverride) {
        window.processPaymentWithOverride();
    } else {
        console.error('processPaymentWithOverride function not found');
        alert('Error: Payment processing function not available');
    }
}

function handleOverrideCancel() {
    console.log('Override Cancel button clicked');
    document.getElementById('adminWarningModal').classList.add('hidden');
}

// Event listeners for modal buttons
document.addEventListener('DOMContentLoaded', function() {
    console.log('PaymentValidation: Setting up event listeners');

    // Employee error modal close
    const employeeErrorClose = document.getElementById('employeeErrorClose');
    if (employeeErrorClose) {
        employeeErrorClose.addEventListener('click', function() {
            console.log('Employee error close clicked');
            PaymentValidation.hideAllModals();
        });
    }

    // Admin warning modal buttons
    const adminWarningCancel = document.getElementById('adminWarningCancel');
    if (adminWarningCancel) {
        adminWarningCancel.addEventListener('click', function() {
            console.log('Admin warning cancel clicked');
            PaymentValidation.hideAllModals();
        });
    }

    const adminWarningContinue = document.getElementById('adminWarningContinue');
    if (adminWarningContinue) {
        adminWarningContinue.addEventListener('click', function() {
            console.log('Admin warning continue clicked');
            document.getElementById('adminWarningModal').classList.add('hidden');
            PaymentValidation.showAdminFinalConfirmation();
        });
    }

    // Admin final modal buttons
    const adminFinalCancel = document.getElementById('adminFinalCancel');
    if (adminFinalCancel) {
        adminFinalCancel.addEventListener('click', function() {
            console.log('Admin final cancel clicked');
            PaymentValidation.hideAllModals();
        });
    }

    const adminFinalConfirm = document.getElementById('adminFinalConfirm');
    if (adminFinalConfirm) {
        adminFinalConfirm.addEventListener('click', function() {
            console.log('Admin final confirm clicked');
            PaymentValidation.hideAllModals();
            // Trigger the actual payment processing with admin override
            if (window.processPaymentWithOverride) {
                window.processPaymentWithOverride();
            }
        });
    }

    // Admin success modal close
    const adminSuccessClose = document.getElementById('adminSuccessClose');
    if (adminSuccessClose) {
        adminSuccessClose.addEventListener('click', function() {
            console.log('Admin success close clicked');
            PaymentValidation.hideAllModals();
            // Refresh the page or update the UI as needed
            if (window.location.reload) {
                window.location.reload();
            }
        });
    }
    
    // Allow modal dismissal by clicking outside (since there's no background overlay)
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                PaymentValidation.hideAllModals();
            }
        });
    });
});
</script>
