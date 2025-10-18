#!/bin/bash

# Deployment script for email verification modal and member list fixes
# Run this script on your VPS server at 156.67.221.184

echo "🚀 Starting deployment of email verification modal and member list fixes..."

# Find Laravel application directory
if [ -d "/var/www/html" ]; then
    APP_DIR="/var/www/html"
elif [ -d "/var/www/silencio-gym" ]; then
    APP_DIR="/var/www/silencio-gym"
elif [ -d "/home/silencio-gym" ]; then
    APP_DIR="/home/silencio-gym"
else
    echo "❌ Laravel application directory not found. Please specify the correct path."
    echo "Common locations: /var/www/html, /var/www/silencio-gym, /home/silencio-gym"
    exit 1
fi

echo "📁 Using application directory: $APP_DIR"
cd "$APP_DIR"

# Backup existing files
echo "💾 Creating backups..."
mkdir -p backups/$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="backups/$(date +%Y%m%d_%H%M%S)"

cp resources/views/members/register.blade.php "$BACKUP_DIR/" 2>/dev/null || echo "register.blade.php not found for backup"
cp app/Http/Controllers/MemberAuthController.php "$BACKUP_DIR/" 2>/dev/null || echo "MemberAuthController.php not found for backup"
cp app/Http/Controllers/EmployeeController.php "$BACKUP_DIR/" 2>/dev/null || echo "EmployeeController.php not found for backup"

echo "✅ Backups created in $BACKUP_DIR"

# Create the email verification modal component
echo "📝 Creating email verification modal component..."
mkdir -p resources/views/components

cat > resources/views/components/email-verification-modal.blade.php << 'EOF'
<!-- Email Verification Information Modal -->
<div id="emailVerificationModal" class="fixed inset-0 flex items-center justify-center p-2 sm:p-4 hidden z-50" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full transform transition-all duration-300 scale-95 opacity-0 border border-gray-200" 
         id="emailVerificationModalContent"
         style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); z-index: 9999;">
        <div class="p-4 sm:p-6 text-center">
            <!-- Header with Icon -->
            <div class="mb-4 sm:mb-6">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Please Read</h3>
            </div>
            
            <!-- Message Content -->
            <div class="mb-6 text-left">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm sm:text-base text-gray-700 mb-3 font-medium">
                        Please make sure to input a valid email address.
                    </p>
                    <p class="text-sm sm:text-base text-gray-700">
                        Once done creating the account please verify it by clicking/tapping on 
                        <span class="font-semibold text-blue-600">"Verify Email Address"</span> 
                        sent to you by mail from 
                        <span class="font-semibold">Silencio Gym Management System</span>
                    </p>
                </div>
                
                <!-- Additional Info -->
                <div class="text-xs sm:text-sm text-gray-500 text-center">
                    <p>Check your spam folder if you don't see the email within a few minutes.</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <button type="button" 
                        id="emailVerificationOk"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 sm:py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 cursor-pointer">
                    I Understand
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Email Verification Modal Management
const EmailVerificationModal = {
    // Show the email verification modal
    show() {
        console.log('Showing email verification modal');
        const modal = document.getElementById('emailVerificationModal');
        const content = document.getElementById('emailVerificationModalContent');
        
        if (modal && content) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            
            console.log('Email verification modal should now be visible');
        } else {
            console.error('Email verification modal elements not found!');
        }
    },
    
    // Hide the email verification modal
    hide() {
        console.log('Hiding email verification modal');
        const modal = document.getElementById('emailVerificationModal');
        const content = document.getElementById('emailVerificationModalContent');
        
        if (modal && content) {
            // Trigger close animation
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scrolling
            }, 300);
        }
    }
};

// Event listeners for modal buttons
document.addEventListener('DOMContentLoaded', function() {
    console.log('EmailVerificationModal: Setting up event listeners');

    // OK button click
    const okButton = document.getElementById('emailVerificationOk');
    if (okButton) {
        okButton.addEventListener('click', function() {
            console.log('Email verification OK button clicked');
            EmailVerificationModal.hide();
        });
    }

    // Allow modal dismissal by clicking outside
    const modal = document.getElementById('emailVerificationModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                EmailVerificationModal.hide();
            }
        });
    }
    
    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('emailVerificationModal');
            if (modal && !modal.classList.contains('hidden')) {
                EmailVerificationModal.hide();
            }
        }
    });
});

// Make EmailVerificationModal globally available
window.EmailVerificationModal = EmailVerificationModal;
</script>
EOF

echo "✅ Email verification modal component created"

echo "🔧 Deployment script created. Please run the remaining updates manually or continue with the next script..."
echo ""
echo "Next steps:"
echo "1. Update resources/views/members/register.blade.php"
echo "2. Update app/Http/Controllers/MemberAuthController.php" 
echo "3. Update app/Http/Controllers/EmployeeController.php"
echo "4. Clear Laravel caches"
