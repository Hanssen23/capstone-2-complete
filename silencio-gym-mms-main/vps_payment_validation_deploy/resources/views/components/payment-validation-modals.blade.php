{{-- Employee Error Modal --}}
<div id="employeeErrorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cannot Process Payment</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="employeeErrorMessage">
                    This member already has an active membership plan.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="employeeErrorClose" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Admin Warning Modal (First Confirmation) --}}
<div id="adminWarningModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">⚠️ WARNING: Member Has Active Plan</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-700" id="adminWarningMessage">
                    This member already has an active membership plan. Proceeding will create a duplicate plan. Are you sure you want to continue?
                </p>
            </div>
            <div class="flex gap-3 px-4 py-3">
                <button id="adminWarningCancel" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button id="adminWarningContinue" class="flex-1 px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Yes, I understand the risks
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Admin Final Confirmation Modal (With Countdown) --}}
<div id="adminFinalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
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
            <div class="flex gap-3 px-4 py-3">
                <button id="adminFinalCancel" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button id="adminFinalConfirm" class="flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <span id="confirmButtonText">Please wait... (<span id="buttonCountdown">5</span> seconds)</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Success Modal for Admin Override --}}
<div id="adminSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
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
                <button id="adminSuccessClose" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300">
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
        try {
            const response = await fetch('{{ route("membership.check-active-membership") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ member_id: memberId })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error checking active membership:', error);
            return { has_active_plan: false };
        }
    },
    
    // Show employee error modal
    showEmployeeError(message) {
        document.getElementById('employeeErrorMessage').textContent = message;
        document.getElementById('employeeErrorModal').classList.remove('hidden');
    },
    
    // Show admin warning modal
    showAdminWarning(message) {
        document.getElementById('adminWarningMessage').textContent = message;
        document.getElementById('adminWarningModal').classList.remove('hidden');
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

// Event listeners for modal buttons
document.addEventListener('DOMContentLoaded', function() {
    // Employee error modal close
    document.getElementById('employeeErrorClose').addEventListener('click', function() {
        PaymentValidation.hideAllModals();
    });
    
    // Admin warning modal buttons
    document.getElementById('adminWarningCancel').addEventListener('click', function() {
        PaymentValidation.hideAllModals();
    });
    
    document.getElementById('adminWarningContinue').addEventListener('click', function() {
        document.getElementById('adminWarningModal').classList.add('hidden');
        PaymentValidation.showAdminFinalConfirmation();
    });
    
    // Admin final modal buttons
    document.getElementById('adminFinalCancel').addEventListener('click', function() {
        PaymentValidation.hideAllModals();
    });
    
    document.getElementById('adminFinalConfirm').addEventListener('click', function() {
        PaymentValidation.hideAllModals();
        // Trigger the actual payment processing with admin override
        if (window.processPaymentWithOverride) {
            window.processPaymentWithOverride();
        }
    });
    
    // Admin success modal close
    document.getElementById('adminSuccessClose').addEventListener('click', function() {
        PaymentValidation.hideAllModals();
        // Refresh the page or update the UI as needed
        if (window.location.reload) {
            window.location.reload();
        }
    });
    
    // Prevent modal dismissal by clicking outside
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});
</script>
