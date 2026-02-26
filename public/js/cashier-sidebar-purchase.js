/**
 * Cashier Sidebar Purchase JavaScript
 * Handles purchase-specific sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Purchase sidebar specific functionality
    const purchaseSection = document.querySelector('[data-section="purchase"]');
    if (!purchaseSection) return;
    
    // Handle purchase navigation
    const purchaseLinks = purchaseSection.querySelectorAll('a');
    purchaseLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all purchase links
            purchaseLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active state
            localStorage.setItem('activePurchaseLink', this.getAttribute('href'));
        });
    });
    
    // Restore active purchase link
    const activePurchaseLink = localStorage.getItem('activePurchaseLink');
    if (activePurchaseLink) {
        purchaseLinks.forEach(link => {
            if (link.getAttribute('href') === activePurchaseLink) {
                link.classList.add('active');
            }
        });
    }
    
    // Handle purchase submenu animations
    const purchaseSubmenu = document.querySelector('.purchase-submenu');
    const purchaseToggle = document.querySelector('.purchase-toggle');
    
    if (purchaseToggle && purchaseSubmenu) {
        purchaseToggle.addEventListener('click', function(e) {
            e.preventDefault();
            purchaseSubmenu.classList.toggle('show');
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Auto-expand purchase section if on purchase page
    const currentPath = window.location.pathname;
    if (currentPath.includes('/cashier/purchases')) {
        if (purchaseSubmenu) {
            purchaseSubmenu.classList.add('show');
        }
        const icon = document.querySelector('.purchase-toggle i');
        if (icon) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
    
    // Handle purchase-related notifications
    function showPurchaseNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    
    // Make notification function globally available
    window.showPurchaseNotification = showPurchaseNotification;
});
