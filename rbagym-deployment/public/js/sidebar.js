document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!sidebar) {
        console.log('Sidebar not found');
        return;
    }
    
    console.log('Sidebar script loaded');
    
    // Desktop behavior - ensure sidebar is expanded
    if (window.innerWidth > 1024) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('expanded');
    }
    
    // Custom toggle function for sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('open');
        if (overlay) {
            overlay.classList.toggle('active');
        }
    }
    
    // Handle sidebar toggle button clicks
    const toggleButtons = document.querySelectorAll('.sidebar-toggle');
    toggleButtons.forEach(button => {
        button.addEventListener('click', toggleSidebar);
    });
    
    // Close sidebar when overlay is clicked
    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }
    
    // Swipe gesture detection with improved sensitivity
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let currentY = 0;
    let isDragging = false;
    let swipeThreshold = 100; // Increased from 50 to reduce sensitivity
    let minSwipeDistance = 30; // Minimum distance to start recognizing swipe
    let maxVerticalDeviation = 50; // Maximum vertical movement allowed for horizontal swipe
    let swipeStartTime = 0;
    let maxSwipeTime = 500; // Maximum time for a valid swipe (ms)
    
    function handleTouchStart(e) {
        // Only handle swipes from the left edge or when sidebar is open
        const touchX = e.touches[0].clientX;
        const isFromLeftEdge = touchX < 30; // Only start swipe from left 30px of screen
        const isSidebarOpen = sidebar.classList.contains('open');

        // Only proceed if swipe starts from left edge or sidebar is open
        if (!isFromLeftEdge && !isSidebarOpen) {
            return;
        }

        startX = touchX;
        startY = e.touches[0].clientY;
        currentX = startX;
        currentY = startY;
        isDragging = true;
        swipeStartTime = Date.now();

        // Add swiping class for smooth transitions
        sidebar.classList.add('swiping');
    }
    
    function handleTouchMove(e) {
        if (!isDragging) return;

        currentX = e.touches[0].clientX;
        currentY = e.touches[0].clientY;

        // Calculate swipe distance
        const deltaX = currentX - startX;
        const deltaY = Math.abs(currentY - startY);

        // Check if this is a valid horizontal swipe
        const isHorizontalSwipe = Math.abs(deltaX) > deltaY;
        const hasMinimumDistance = Math.abs(deltaX) > minSwipeDistance;
        const isWithinVerticalLimit = deltaY < maxVerticalDeviation;

        // Only handle horizontal swipes that meet our criteria
        if (isHorizontalSwipe && hasMinimumDistance && isWithinVerticalLimit) {
            e.preventDefault();

            // Calculate sidebar position based on swipe
            const sidebarWidth = sidebar.offsetWidth;
            const isSidebarOpen = sidebar.classList.contains('open');

            let translateX;
            if (isSidebarOpen) {
                // Sidebar is open, allow closing swipe
                translateX = Math.max(deltaX, -sidebarWidth);
            } else {
                // Sidebar is closed, allow opening swipe
                translateX = Math.min(deltaX - sidebarWidth, 0);
            }

            sidebar.style.transform = `translateX(${translateX}px)`;
        } else if (deltaY > maxVerticalDeviation) {
            // If vertical movement is too much, cancel the swipe
            isDragging = false;
            sidebar.classList.remove('swiping');
            sidebar.style.transform = sidebar.classList.contains('open') ?
                'translateX(0)' : 'translateX(-100%)';
        }
    }
    
    function handleTouchEnd(e) {
        if (!isDragging) return;
        
        isDragging = false;
        sidebar.classList.remove('swiping');
        
        const deltaX = currentX - startX;
        const deltaY = Math.abs(currentY - startY);
        const swipeTime = Date.now() - swipeStartTime;
        const swipeVelocity = Math.abs(deltaX) / swipeTime; // pixels per millisecond

        // Enhanced swipe detection criteria
        const isValidSwipe = (
            Math.abs(deltaX) > swipeThreshold || // Distance threshold
            (Math.abs(deltaX) > minSwipeDistance && swipeVelocity > 0.3) // Fast swipe
        ) && deltaY < maxVerticalDeviation && swipeTime < maxSwipeTime;

        // Determine if swipe was strong enough to toggle sidebar
        if (isValidSwipe) {
            if (deltaX > 0 && !sidebar.classList.contains('open')) {
                // Swipe right (opening)
                sidebar.classList.add('open');
                if (overlay) overlay.classList.add('active');
                console.log('Sidebar opened by swipe');
            } else if (deltaX < 0 && sidebar.classList.contains('open')) {
                // Swipe left (closing)
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
                console.log('Sidebar closed by swipe');
            }
        }
        
        // Reset transform
        const sidebarWidth = sidebar.offsetWidth;
        sidebar.style.transform = sidebar.classList.contains('open') ? 
            'translateX(0)' : 'translateX(-100%)';
    }
    
    // Add touch listeners for swipe detection (only on mobile)
    if (window.innerWidth <= 1024) {
        document.addEventListener('touchstart', handleTouchStart, { passive: false });
        document.addEventListener('touchmove', handleTouchMove, { passive: false });
        document.addEventListener('touchend', handleTouchEnd, { passive: true });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            // Desktop: remove open/closed states, ensure expanded
            sidebar.classList.remove('open');
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('expanded');
            sidebar.style.transform = '';
            if (overlay) overlay.classList.remove('active');
        } else {
            // Mobile: remove expanded state, handle mobile behavior
            sidebar.classList.remove('expanded');
            if (!sidebar.classList.contains('open')) {
                sidebar.style.transform = 'translateX(-100%)';
            }
        }
    });
});