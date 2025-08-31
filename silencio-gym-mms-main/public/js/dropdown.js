document.addEventListener('DOMContentLoaded', function() {
    console.log('Dropdown script loaded');
    
    // Get all dropdown toggles (both old and new classes)
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle, .nav-dropdown-toggle');
    console.log('Found dropdown toggles:', dropdownToggles.length);
    
    dropdownToggles.forEach((toggle, index) => {
        console.log(`Setting up dropdown ${index + 1}`);
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Dropdown clicked');
            
            // Get the dropdown item container (support both old and new classes)
            const dropdownItem = this.closest('.dropdown-item, .nav-dropdown-item');
            const dropdownContent = dropdownItem.querySelector('.dropdown-content, .nav-dropdown-content');
            const dropdownArrow = dropdownItem.querySelector('.dropdown-arrow');
            
            console.log('Dropdown content found:', dropdownContent);
            console.log('Dropdown arrow found:', dropdownArrow);
            
            // Close other dropdowns
            dropdownToggles.forEach((otherToggle, otherIndex) => {
                if (otherIndex !== index) {
                    const otherDropdownItem = otherToggle.closest('.dropdown-item, .nav-dropdown-item');
                    const otherDropdownContent = otherDropdownItem.querySelector('.dropdown-content, .nav-dropdown-content');
                    const otherDropdownArrow = otherDropdownItem.querySelector('.dropdown-arrow');
                    
                    if (otherDropdownContent) {
                        otherDropdownContent.classList.add('hidden');
                        otherDropdownContent.style.display = 'none';
                        otherDropdownContent.style.opacity = '0';
                        otherDropdownContent.style.visibility = 'hidden';
                    }
                    if (otherDropdownArrow) {
                        otherDropdownArrow.style.transform = 'rotate(0deg)';
                    }
                }
            });
            
            // Toggle the dropdown content
            if (dropdownContent.classList.contains('hidden')) {
                console.log('Opening dropdown');
                // Show dropdown
                dropdownContent.classList.remove('hidden');
                dropdownContent.style.display = 'block';
                dropdownContent.style.opacity = '1';
                dropdownContent.style.visibility = 'visible';
                if (dropdownArrow) {
                    dropdownArrow.style.transform = 'rotate(180deg)';
                }
                
                // Add fade-in animation
                dropdownContent.style.opacity = '0';
                dropdownContent.style.transform = 'translateY(-8px)';
                
                setTimeout(() => {
                    dropdownContent.style.transition = 'opacity 0.2s ease-out, transform 0.2s ease-out';
                    dropdownContent.style.opacity = '1';
                    dropdownContent.style.transform = 'translateY(0)';
                }, 10);
            } else {
                console.log('Closing dropdown');
                // Hide dropdown
                dropdownContent.style.transition = 'opacity 0.2s ease-in, transform 0.2s ease-in';
                dropdownContent.style.opacity = '0';
                dropdownContent.style.transform = 'translateY(-8px)';
                if (dropdownArrow) {
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
                
                setTimeout(() => {
                    dropdownContent.classList.add('hidden');
                    dropdownContent.style.display = 'none';
                    dropdownContent.style.visibility = 'hidden';
                }, 200);
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown-item, .nav-dropdown-item')) {
            dropdownToggles.forEach((toggle) => {
                const dropdownItem = toggle.closest('.dropdown-item, .nav-dropdown-item');
                const dropdownContent = dropdownItem.querySelector('.dropdown-content, .nav-dropdown-content');
                const dropdownArrow = dropdownItem.querySelector('.dropdown-arrow');
                
                if (dropdownContent && !dropdownContent.classList.contains('hidden')) {
                    dropdownContent.style.transition = 'opacity 0.2s ease-in, transform 0.2s ease-in';
                    dropdownContent.style.opacity = '0';
                    dropdownContent.style.transform = 'translateY(-8px)';
                    if (dropdownArrow) {
                        dropdownArrow.style.transform = 'rotate(0deg)';
                    }
                    
                    setTimeout(() => {
                        dropdownContent.classList.add('hidden');
                        dropdownContent.style.display = 'none';
                        dropdownContent.style.visibility = 'hidden';
                    }, 200);
                }
            });
        }
    });
}); 