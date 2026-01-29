@push('stylesDashboard')
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
        --app-bg: linear-gradient(135deg, #ECEFF1 0%, #E8EAF6 100%);
        --card-bg: #FAFBFC;
        --card-border: rgba(13, 71, 161, 0.15);
        --page-header: #263238;
        
        /* KPI Colors - Electric Theme */
        --text-primary: #263238;
        --text-secondary: #546E7A;
        --text-muted: #78909C;
        --success-teal: #43A047;
        --danger-red: #E53935;
        --warning-yellow: #C6FF00;
        --attention-orange: #E91E63;
        --info-blue: #2196F3;
        --hover-blue: #00E5FF;
        --inactive-text: #78909C;
        
        /* Chart Colors - Electric */
        --chart-this-week: #2196F3;
        --chart-last-week: #0D47A1;
        --chart-target: #00E5FF;
        --chart-positive-area: rgba(0, 229, 255, 0.15);
        --chart-warning: #C6FF00;
        
        /* State Colors */
        --profit-positive: #43A047;
        --profit-negative: #E53935;
        --sales-trend-up: #43A047;
        --sales-trend-down: #E53935;
        --expenses-warning: #C6FF00;
        --low-stock-warning: #C6FF00;
        --low-stock-critical: #E91E63;
        --cash-normal: #263238;
        --cash-balanced: #43A047;
        --cash-mismatch: #E91E63;
    }
    
    /* Base Body & Layout */
    body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, var(--slate-bg) 0%, #E8EAF6 100%);
        color: var(--text-primary);
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
        border: 2px solid var(--card-border);
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
        transition: all 0.3s;
    }
    
    .card-base:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(13, 71, 161, 0.15);
        border-color: var(--cyan-bright);
    }
    
    .panel { 
        background: var(--card-bg);
        border-radius: 14px; 
        padding: 18px; 
        border: 2px solid var(--card-border);
        box-shadow: 0 6px 18px rgba(13, 71, 161, 0.08);
    }
    
    /* Stats Cards */
    .stat-card { 
        border-radius: 16px; 
        padding: 20px; 
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        background: var(--card-bg);
        border: 2px solid var(--card-border);
        box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
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
        box-shadow: 0 12px 32px rgba(13, 71, 161, 0.15);
        border-color: var(--cyan-bright);
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
        color: var(--text-secondary);
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
        color: var(--electric-blue);
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        color: var(--electric-blue);
    }
    
    .btn-outline-primary {
        border: 2px solid var(--electric-blue);
        color: var(--electric-blue);
        font-weight: 700;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        background: transparent;
        transition: all 0.3s;
    }
    
    .btn-outline-primary:hover {
        background: var(--electric-blue);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-soft { 
        background: #FFFFFF; 
        border: 2px solid var(--electric-blue);
        color: var(--electric-blue);
        transition: all 0.3s;
    }
    
    .btn-soft:hover {
        background: var(--electric-blue);
        color: #FFFFFF;
        transform: translateY(-2px);
    }
    
    /* Form Controls */
    .form-control {
        border: 2px solid var(--electric-blue);
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--cyan-bright);
        box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
    }
    
    .form-select {
        border: 2px solid var(--electric-blue);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: var(--text-primary);
        transition: all 0.3s;
    }
    
    .form-select:focus {
        border-color: var(--cyan-bright);
        box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
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
        border: 2px solid var(--electric-blue);
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .form-check-input:checked {
        background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
        border-color: var(--cyan-bright);
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
    }
    
    /* Tables */
    .table-base {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 16px rgba(13, 71, 161, 0.1);
    }
    
    .table-base table {
        color: var(--text-primary);
        margin-bottom: 0;
    }
    
    .table-base thead tr {
        border-bottom: 2px solid rgba(13, 71, 161, 0.15);
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
        color: var(--cyan-bright);
    }
    
    .table-base tbody tr {
        border-bottom: 1px solid rgba(13, 71, 161, 0.08);
        transition: all 0.2s;
        background: rgba(255, 255, 255, 0.8);
    }
    
    .table-base tbody tr:hover {
        background: rgba(0, 229, 255, 0.08);
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
        background: linear-gradient(135deg, rgba(67, 160, 71, 0.15), rgba(102, 187, 106, 0.15));
        color: var(--success-teal);
        border: 1px solid rgba(67, 160, 71, 0.3);
    }
    
    .badge-secondary {
        background: linear-gradient(135deg, rgba(120, 144, 156, 0.15), rgba(144, 164, 174, 0.15));
        color: var(--text-muted);
        border: 1px solid rgba(120, 144, 156, 0.3);
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
        background: rgba(149, 165, 166, 0.15);
        color: var(--inactive-text);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(149, 165, 166, 0.3);
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
        color: var(--electric-blue);
        font-weight: 700;
        box-shadow: 0 6px 14px rgba(33, 150, 243, 0.2);
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
        border: 2px solid var(--card-border);
        box-shadow: 0 12px 48px rgba(13, 71, 161, 0.15);
    }
    
    .modal-header {
        background: linear-gradient(90deg, rgba(33, 150, 243, 0.05), transparent);
        border-bottom: 2px solid var(--card-border);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--electric-blue);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        background: rgba(236, 239, 241, 0.3);
        border-top: 2px solid var(--card-border);
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
@endpush
