/**
 * Cashier Sidebar Inventory JavaScript
 * Handles inventory-specific sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inventory sidebar specific functionality
    const inventorySection = document.querySelector('[data-section="inventory"]');
    if (!inventorySection) return;
    
    // Handle inventory navigation
    const inventoryLinks = inventorySection.querySelectorAll('a');
    inventoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all inventory links
            inventoryLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active state
            localStorage.setItem('activeInventoryLink', this.getAttribute('href'));
        });
    });
    
    // Restore active inventory link
    const activeInventoryLink = localStorage.getItem('activeInventoryLink');
    if (activeInventoryLink) {
        inventoryLinks.forEach(link => {
            if (link.getAttribute('href') === activeInventoryLink) {
                link.classList.add('active');
            }
        });
    }
    
    // Handle inventory submenu animations
    const inventorySubmenu = document.querySelector('.inventory-submenu');
    const inventoryToggle = document.querySelector('.inventory-toggle');
    
    if (inventoryToggle && inventorySubmenu) {
        inventoryToggle.addEventListener('click', function(e) {
            e.preventDefault();
            inventorySubmenu.classList.toggle('show');
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Auto-expand inventory section if on inventory page
    const currentPath = window.location.pathname;
    if (currentPath.includes('/cashier/inventory')) {
        if (inventorySubmenu) {
            inventorySubmenu.classList.add('show');
        }
        const icon = document.querySelector('.inventory-toggle i');
        if (icon) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
});
