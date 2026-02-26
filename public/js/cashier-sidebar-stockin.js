/**
 * Cashier Sidebar Stock In JavaScript
 * Handles stock-in specific sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Stock In sidebar specific functionality
    const stockInSection = document.querySelector('[data-section="stockin"]');
    if (!stockInSection) return;
    
    // Handle stock-in navigation
    const stockInLinks = stockInSection.querySelectorAll('a');
    stockInLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all stock-in links
            stockInLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active state
            localStorage.setItem('activeStockInLink', this.getAttribute('href'));
        });
    });
    
    // Restore active stock-in link
    const activeStockInLink = localStorage.getItem('activeStockInLink');
    if (activeStockInLink) {
        stockInLinks.forEach(link => {
            if (link.getAttribute('href') === activeStockInLink) {
                link.classList.add('active');
            }
        });
    }
    
    // Handle stock-in submenu animations
    const stockInSubmenu = document.querySelector('.stockin-submenu');
    const stockInToggle = document.querySelector('.stockin-toggle');
    
    if (stockInToggle && stockInSubmenu) {
        stockInToggle.addEventListener('click', function(e) {
            e.preventDefault();
            stockInSubmenu.classList.toggle('show');
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Auto-expand stock-in section if on stock-in page
    const currentPath = window.location.pathname;
    if (currentPath.includes('/cashier/stockin')) {
        if (stockInSubmenu) {
            stockInSubmenu.classList.add('show');
        }
        const icon = document.querySelector('.stockin-toggle i');
        if (icon) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
    
    // Handle stock level indicators
    function updateStockIndicators() {
        const stockIndicators = document.querySelectorAll('.stock-indicator');
        stockIndicators.forEach(indicator => {
            const stockLevel = indicator.dataset.stockLevel;
            const lowStockThreshold = indicator.dataset.lowStockThreshold || 10;
            
            indicator.classList.remove('stock-high', 'stock-medium', 'stock-low', 'stock-critical');
            
            if (stockLevel <= 0) {
                indicator.classList.add('stock-critical');
                indicator.title = 'Out of Stock';
            } else if (stockLevel <= lowStockThreshold) {
                indicator.classList.add('stock-low');
                indicator.title = 'Low Stock';
            } else if (stockLevel <= lowStockThreshold * 2) {
                indicator.classList.add('stock-medium');
                indicator.title = 'Medium Stock';
            } else {
                indicator.classList.add('stock-high');
                indicator.title = 'Good Stock';
            }
        });
    }
    
    // Update stock indicators on page load
    updateStockIndicators();
    
    // Make update function globally available for AJAX calls
    window.updateStockIndicators = updateStockIndicators;
});
