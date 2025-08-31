document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    
    if (!sidebar) {
        console.log('Sidebar not found');
        return;
    }
    
    console.log('Sidebar script loaded');
    
    // Expand sidebar on hover
    sidebar.addEventListener('mouseenter', function() {
        console.log('Sidebar hover - expanding');
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('expanded');
    });
    
    // Collapse sidebar when mouse leaves
    sidebar.addEventListener('mouseleave', function() {
        console.log('Sidebar leave - collapsing');
        sidebar.classList.remove('expanded');
        sidebar.classList.add('collapsed');
        
        // Close any open dropdowns when collapsing
        const dropdownContents = sidebar.querySelectorAll('.nav-dropdown-content, .dropdown-content');
        const dropdownArrows = sidebar.querySelectorAll('.dropdown-arrow');
        
        dropdownContents.forEach(content => {
            if (!content.classList.contains('hidden')) {
                content.style.transition = 'opacity 0.2s ease-in, transform 0.2s ease-in';
                content.style.opacity = '0';
                content.style.transform = 'translateY(-8px)';
                
                setTimeout(() => {
                    content.classList.add('hidden');
                    content.style.display = 'none';
                    content.style.visibility = 'hidden';
                }, 200);
            }
        });
        
        dropdownArrows.forEach(arrow => {
            arrow.style.transform = 'rotate(0deg)';
        });
    });
    
    // Handle mobile responsiveness
    function handleMobileSidebar() {
        if (window.innerWidth <= 768) {
            // On mobile, start collapsed and allow manual expansion
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('expanded');
            
            // Remove hover events on mobile
            sidebar.removeEventListener('mouseenter', sidebar._mouseenterHandler);
            sidebar.removeEventListener('mouseleave', sidebar._mouseleaveHandler);
        } else {
            // On desktop, restore hover functionality
            sidebar._mouseenterHandler = function() {
                sidebar.classList.remove('collapsed');
                sidebar.classList.add('expanded');
            };
            
            sidebar._mouseleaveHandler = function() {
                sidebar.classList.remove('expanded');
                sidebar.classList.add('collapsed');
                
                // Close dropdowns when collapsing
                const dropdownContents = sidebar.querySelectorAll('.nav-dropdown-content, .dropdown-content');
                const dropdownArrows = sidebar.querySelectorAll('.dropdown-arrow');
                
                dropdownContents.forEach(content => {
                    if (!content.classList.contains('hidden')) {
                        content.style.transition = 'opacity 0.2s ease-in, transform 0.2s ease-in';
                        content.style.opacity = '0';
                        content.style.transform = 'translateY(-8px)';
                        
                        setTimeout(() => {
                            content.classList.add('hidden');
                            content.style.display = 'none';
                            content.style.visibility = 'hidden';
                        }, 200);
                    }
                });
                
                dropdownArrows.forEach(arrow => {
                    arrow.style.transform = 'rotate(0deg)';
                });
            };
            
            sidebar.addEventListener('mouseenter', sidebar._mouseenterHandler);
            sidebar.addEventListener('mouseleave', sidebar._mouseleaveHandler);
        }
    }
    
    // Initialize mobile handling
    handleMobileSidebar();
    
    // Handle window resize
    window.addEventListener('resize', handleMobileSidebar);
}); 