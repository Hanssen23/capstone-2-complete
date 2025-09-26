document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    
    if (!sidebar) {
        console.log('Sidebar not found');
        return;
    }
    
    console.log('Sidebar script loaded - permanently expanded');
    
    // Ensure sidebar is always expanded and remove collapsed class
    sidebar.classList.remove('collapsed');
    sidebar.classList.add('expanded');
    
    // Disable all hover-to-expand functionality
    // Sidebar is now permanently expanded
    console.log('Sidebar is permanently expanded - no hover functionality');
    
    // Ensure sidebar stays expanded on window resize
    window.addEventListener('resize', function() {
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('expanded');
    });
});