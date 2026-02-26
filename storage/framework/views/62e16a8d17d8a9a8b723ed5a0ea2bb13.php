<?php $__env->startPush('stylesDashboard'); ?>
<style>
    /* ========================================
       ELECTRIC MODERN - BASE THEME
       High-tech futuristic color scheme
       ======================================== */
    
    :root {
        /* Electric Modern Palette */
        --electric-blue: #0D47A1;
        --neon-blue: #2196F3;
        --cyan-bright: #00E5FF;
        --magenta: #E91E63;
        --violet: #9C27B0;
        --lime-electric: #C6FF00;
        --slate-bg: #ECEFF1;
        --ice-white: #FAFBFC;
        
        /* Dashboard Color Mapping */
        --app-bg: linear-gradient(135deg, #F5F7FA 0%, #E8EAF6 100%);
        --card-bg: #FFFFFF;
        --card-border: rgba(13, 71, 161, 0.12);
        --page-header: #1A237E;
        
        /* Enhanced Text Colors for Better Contrast */
        --text-primary: #1A1A1A;
        --text-secondary: #424242;
        --text-muted: #757575;
        --text-light: #9E9E9E;
        --text-on-dark: #FFFFFF;
        --text-on-primary: #FFFFFF;
        
        /* KPI Colors - Electric Theme */
        --success-teal: #00695C;
        --danger-red: #C62828;
        --warning-yellow: #F57F17;
        --attention-orange: #D84315;
        --info-blue: #1565C0;
        --hover-blue: #00ACC1;
        --inactive-text: #9E9E9E;
        
        /* Chart Colors - Electric */
        --chart-this-week: #1565C0;
        --chart-last-week: #0D47A1;
        --chart-target: #00ACC1;
        --chart-positive-area: rgba(0, 229, 255, 0.15);
        --chart-warning: #F57F17;
        
        /* State Colors */
        --profit-positive: #00695C;
        --profit-negative: #C62828;
        --sales-trend-up: #00695C;
        --sales-trend-down: #C62828;
        --expenses-warning: #F57F17;
        --low-stock-warning: #F57F17;
        --low-stock-critical: #D84315;
        --cash-normal: #1A1A1A;
        --cash-balanced: #00695C;
        --cash-mismatch: #D84315;
        
        /* Background Colors */
        --bg-primary: #FFFFFF;
        --bg-secondary: #F8F9FA;
        --bg-tertiary: #F5F5F5;
        --bg-dark: #1A1A1A;
        --bg-overlay: rgba(0, 0, 0, 0.5);
        
        /* Border Colors */
        --border-light: #E0E0E0;
        --border-medium: #BDBDBD;
        --border-dark: #757575;
        --border-primary: var(--electric-blue);
    }
    
    /* Base Body & Layout */
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        line-height: 1.6;
        font-weight: 400;
    }
    
    /* Enhanced Typography */
    h1, h2, h3, h4, h5, h6 {
        color: var(--text-primary);
        font-weight: 600;
        line-height: 1.3;
    }
    
    p {
        color: var(--text-secondary);
        margin-bottom: 1rem;
    }
    
    small {
        color: var(--text-muted);
    }
    
    /* Page Headers */
    .page-header h1 {
        font-size: 28px; 
        font-weight: 800; 
        background: linear-gradient(135deg, var(--electric-blue), var(--neon-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.02em;
        margin-bottom: 0.5rem;
        color: var(--text-primary); /* Fallback for browsers that don't support text-fill-color */
    }
    
    .page-header p {
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0;
    }
    
    /* Search Inputs */
    .search-input { 
        border-radius: 999px; 
        padding-left: 44px; 
        padding-right: 1rem;
        border: 2px solid var(--electric-blue);
        transition: all 0.3s;
    }
    
    .search-input:focus {
        border-color: var(--cyan-bright);
        box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
    }
    
    .search-icon { 
        position: absolute; 
        left: 1rem; 
        top: 50%; 
        transform: translateY(-50%); 
        color: var(--neon-blue);
    }
    
    /* Cards & Panels */
    .card-base { 
        background: var(--card-bg); 
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
    }
    
    .card-base:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border-color: var(--border-primary);
    }
    
    .panel { 
        background: var(--card-bg);
        border-radius: 14px; 
        padding: 18px; 
        border: 1px solid var(--border-light);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }
    
    /* Stats Cards */
    .stat-card { 
        border-radius: 16px; 
        padding: 20px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        background: var(--card-bg);
        border: 1px solid var(--border-light);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: radial-gradient(circle, rgba(0, 229, 255, 0.08), transparent 70%);
        pointer-events: none;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
        border-color: var(--border-primary);
    }
    
    .stat-icon { 
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
        color: var(--neon-blue);
        transition: all 0.3s;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.1);
    }
    
    .stats-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }
    
    .stats-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        font-family: 'Inter', monospace;
    }
    
    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
        border: none;
        color: var(--text-on-primary);
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: var(--text-on-primary);
    }
    
    .btn-primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.5);
        outline: none;
    }
    
    .btn-outline-primary {
        border: 2px solid var(--electric-blue);
        color: var(--electric-blue);
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        background: transparent;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-outline-primary:hover {
        background: var(--electric-blue);
        color: var(--text-on-primary);
        transform: translateY(-2px);
    }
    
    .btn-outline-primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.5);
        outline: none;
    }
    
    .btn-outline-danger {
        border: 2px solid var(--danger-red);
        color: var(--danger-red);
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        background: transparent;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-outline-danger:hover {
        background: var(--danger-red);
        color: var(--text-on-primary);
        transform: translateY(-2px);
    }
    
    .btn-outline-danger:focus {
        box-shadow: 0 0 0 0.2rem rgba(198, 40, 40, 0.5);
        outline: none;
    }
    
    .btn-soft { 
        background: var(--bg-primary); 
        border: 2px solid var(--border-medium);
        color: var(--text-secondary);
        transition: all 0.3s;
        font-weight: 600;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-soft:hover {
        background: var(--bg-secondary);
        border-color: var(--border-primary);
        color: var(--electric-blue);
        transform: translateY(-2px);
    }
    
    .btn-soft:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.3);
        outline: none;
    }
    
    /* Form Controls */
    .form-control {
        border: 2px solid var(--border-medium);
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.3s;
        background: var(--bg-primary);
        color: var(--text-primary);
        font-weight: 500;
    }
    
    .form-control:focus {
        border-color: var(--electric-blue);
        box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
        outline: none;
        background: var(--bg-primary);
    }
    
    .form-control::placeholder {
        color: var(--text-light);
    }
    
    .form-select {
        border: 2px solid var(--border-medium);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: var(--text-primary);
        transition: all 0.3s;
        background: var(--bg-primary);
    }
    
    .form-select:focus {
        border-color: var(--electric-blue);
        box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.25);
        outline: none;
        background: var(--bg-primary);
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Checkboxes */
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--border-medium);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--bg-primary);
    }
    
    .form-check-input:checked {
        background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
        border-color: var(--cyan-bright);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
        outline: none;
    }
    
    /* Tables */
    .table-base {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border-light);
    }
    
    .table-base table {
        color: var(--text-primary);
        margin-bottom: 0;
    }
    
    .table-base thead tr {
        border-bottom: 2px solid var(--border-medium);
    }
    
    .table-base thead th {
        background: linear-gradient(135deg, rgba(13, 71, 161, 0.05), rgba(33, 150, 243, 0.05));
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 0.75rem;
        color: var(--electric-blue);
        border: none;
    }
    
    .table-base thead th a {
        color: var(--electric-blue);
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .table-base thead th a:hover {
        color: var(--neon-blue);
    }
    
    .table-base tbody tr {
        border-bottom: 1px solid var(--border-light);
        transition: all 0.2s;
        background: var(--bg-primary);
    }
    
    .table-base tbody tr:hover {
        background: var(--bg-secondary);
        transform: scale(1.005);
    }
    
    .table-base tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border: none;
        color: var(--text-primary);
    }
    
    /* Badges */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    
    .badge-primary {
        background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
        color: var(--neon-blue);
        border: 1px solid rgba(33, 150, 243, 0.3);
    }
    
    .badge-success {
        background: linear-gradient(135deg, rgba(0, 105, 92, 0.15), rgba(0, 137, 123, 0.15));
        color: var(--success-teal);
        border: 1px solid rgba(0, 105, 92, 0.3);
    }
    
    .badge-secondary {
        background: linear-gradient(135deg, rgba(117, 117, 117, 0.15), rgba(158, 158, 158, 0.15));
        color: var(--text-muted);
        border: 1px solid rgba(117, 117, 117, 0.3);
    }
    
    .badge-status-active {
        background: linear-gradient(135deg, rgba(0, 229, 255, 0.2), rgba(33, 150, 243, 0.2));
        color: var(--electric-blue);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(0, 229, 255, 0.3);
    }
    
    .badge-status-inactive {
        background: rgba(158, 158, 158, 0.15);
        color: var(--text-muted);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(158, 158, 158, 0.3);
    }
    
    /* User Avatar */
    .user-avatar { 
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
        color: var(--text-on-primary);
        font-weight: 700;
        box-shadow: 0 6px 14px rgba(33, 150, 243, 0.2);
        font-size: 0.875rem;
    }
    
    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-muted);
    }
    
    .empty-state svg {
        opacity: 0.6;
        margin-bottom: 1rem;
    }
    
    .empty-state .fw-semibold {
        color: var(--text-primary);
        margin-top: 0.5rem;
    }
    
    /* Modal Styling */
    .modal-content {
        border-radius: 16px;
        border: 1px solid var(--border-light);
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        background: var(--bg-primary);
    }
    
    .modal-header {
        background: linear-gradient(90deg, rgba(33, 150, 243, 0.05), transparent);
        border-bottom: 1px solid var(--border-light);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--electric-blue);
    }
    
    .modal-body {
        padding: 1.5rem;
        color: var(--text-primary);
    }
    
    .modal-footer {
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-light);
        padding: 1rem 1.5rem;
    }
    
    /* Close Button */
    .btn-close {
        background-image: none;
        opacity: 1;
    }
    
    .btn-close::before {
        content: '×';
        font-size: 1.5rem;
        color: var(--text-muted);
    }
    
    /* Responsive */
    @media (max-width: 991.98px) {
        .stat-card {
            margin-bottom: 1rem;
        }
        
        .card-base {
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 767.98px) {
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .btn-group .btn {
            width: 100%;
        }
        
        .table-base {
            font-size: 0.875rem;
        }
        
        .stats-value {
            font-size: 1.5rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\POS\resources\views/layouts/theme-base.blade.php ENDPATH**/ ?>