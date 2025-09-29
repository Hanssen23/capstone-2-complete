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
    
    // Swipe gesture detection
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let currentY = 0;
    let isDragging = false;
    let swipeThreshold = 50;
    
    function handleTouchStart(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        isDragging = true;
        
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
        
        // Only handle horizontal swipes (prevent vertical scrolling interference)
        if (Math.abs(deltaX) > deltaY && Math.abs(deltaX) > 10) {
            e.preventDefault();
            
            // Calculate sidebar position based on swipe
            const sidebarWidth = sidebar.offsetWidth;
            let translateX = deltaX - sidebarWidth;
            
            // Clamp translation to prevent over-extending
            if (translateX > 0) translateX = 0;
            if (translateX < -sidebarWidth) translateX = -sidebarWidth;
            
            sidebar.style.transform = `translateX(${translateX}px)`;
        }
    }
    
    function handleTouchEnd(e) {
        if (!isDragging) return;
        
        isDragging = false;
        sidebar.classList.remove('swiping');
        
        const deltaX = currentX - startX;
        
        // Determine if swipe was strong enough to toggle sidebar
        if (Math.abs(deltaX) > swipeThreshold) {
            if (deltaX > 0 && !sidebar.classList.contains('open')) {
                // Swipe right (opening)
                sidebar.classList.add('open');
                if (overlay) overlay.classList.add('active');
            } else if (deltaX < 0 && sidebar.classList.contains('open')) {
                // Swipe left (closing)
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
            }
        }
        
        // Reset transform
        const sidebarWidth = sidebar.offsetWidth;
        sidebar.style.transform = sidebar.classList.contains('open') ? 
            'translateX(0)' : 'translateX(-100%)';
    }
    
    // Add touch listeners to entire viewport for swipe detection
    document.addEventListener('touchstart', handleTouchStart, { passive: false });
    document.addEventListener('touchmove', handleTouchMove, { passive: false });
    document.addEventListener('touchend', handleTouchEnd, { passive: true });
    
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