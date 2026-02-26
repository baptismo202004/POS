/**
 * Cashier Sidebar Products JavaScript
 * Handles products-specific sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Products sidebar specific functionality
    const productsSection = document.querySelector('[data-section="products"]');
    if (!productsSection) return;
    
    // Handle products navigation
    const productsLinks = productsSection.querySelectorAll('a');
    productsLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all products links
            productsLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active state
            localStorage.setItem('activeProductsLink', this.getAttribute('href'));
        });
    });
    
    // Restore active products link
    const activeProductsLink = localStorage.getItem('activeProductsLink');
    if (activeProductsLink) {
        productsLinks.forEach(link => {
            if (link.getAttribute('href') === activeProductsLink) {
                link.classList.add('active');
            }
        });
    }
    
    // Handle products submenu animations
    const productsSubmenu = document.querySelector('.products-submenu');
    const productsToggle = document.querySelector('.products-toggle');
    
    if (productsToggle && productsSubmenu) {
        productsToggle.addEventListener('click', function(e) {
            e.preventDefault();
            productsSubmenu.classList.toggle('show');
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Auto-expand products section if on products page
    const currentPath = window.location.pathname;
    if (currentPath.includes('/cashier/products') || currentPath.includes('/cashier/categories')) {
        if (productsSubmenu) {
            productsSubmenu.classList.add('show');
        }
        const icon = document.querySelector('.products-toggle i');
        if (icon) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
    
    // Handle product category navigation
    const categoryLinks = document.querySelectorAll('.category-link');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all category links
            categoryLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active category
            localStorage.setItem('activeCategoryLink', this.getAttribute('href'));
        });
    });
    
    // Restore active category link
    const activeCategoryLink = localStorage.getItem('activeCategoryLink');
    if (activeCategoryLink) {
        categoryLinks.forEach(link => {
            if (link.getAttribute('href') === activeCategoryLink) {
                link.classList.add('active');
            }
        });
    }
});
