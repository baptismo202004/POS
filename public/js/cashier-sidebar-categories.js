/**
 * Cashier Sidebar Categories JavaScript
 * Handles categories-specific sidebar interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Categories sidebar specific functionality
    const categoriesSection = document.querySelector('[data-section="categories"]');
    if (!categoriesSection) return;
    
    // Handle categories navigation
    const categoryLinks = categoriesSection.querySelectorAll('a');
    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all category links
            categoryLinks.forEach(l => l.classList.remove('active'));
            // Add active class to clicked link
            this.classList.add('active');
            
            // Store active state
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
    
    // Handle category submenu animations
    const categoriesSubmenu = document.querySelector('.categories-submenu');
    const categoriesToggle = document.querySelector('.categories-toggle');
    
    if (categoriesToggle && categoriesSubmenu) {
        categoriesToggle.addEventListener('click', function(e) {
            e.preventDefault();
            categoriesSubmenu.classList.toggle('show');
            
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        });
    }
    
    // Auto-expand categories section if on categories page
    const currentPath = window.location.pathname;
    if (currentPath.includes('/cashier/categories')) {
        if (categoriesSubmenu) {
            categoriesSubmenu.classList.add('show');
        }
        const icon = document.querySelector('.categories-toggle i');
        if (icon) {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        }
    }
    
    // Handle category management functions
    function showCategoryNotification(message, type = 'success') {
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
    
    // Handle category form validation
    function validateCategoryForm(formData) {
        const errors = [];
        
        if (!formData.get('category_name') || formData.get('category_name').trim() === '') {
            errors.push('Category name is required');
        }
        
        if (formData.get('category_name') && formData.get('category_name').length > 255) {
            errors.push('Category name must not exceed 255 characters');
        }
        
        if (formData.get('description') && formData.get('description').length > 1000) {
            errors.push('Description must not exceed 1000 characters');
        }
        
        return errors;
    }
    
    // Handle category deletion confirmation
    function confirmCategoryDelete(categoryName, categoryId) {
        if (confirm(`Are you sure you want to delete the category "${categoryName}"? This action cannot be undone.`)) {
            // Send delete request
            fetch(`/cashier/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCategoryNotification('Category deleted successfully', 'success');
                    // Remove category row from table
                    const categoryRow = document.querySelector(`tr[data-category-id="${categoryId}"]`);
                    if (categoryRow) {
                        categoryRow.remove();
                    }
                } else {
                    showCategoryNotification(data.message || 'Error deleting category', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting category:', error);
                showCategoryNotification('Error deleting category', 'error');
            });
        }
    }
    
    // Make functions globally available
    window.showCategoryNotification = showCategoryNotification;
    window.validateCategoryForm = validateCategoryForm;
    window.confirmCategoryDelete = confirmCategoryDelete;
    
    // Initialize tooltips for category actions
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
