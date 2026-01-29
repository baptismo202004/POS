<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier POS System')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Animate.css for SweetAlert animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { 
            /* ========================================
               ELECTRIC MODERN PALETTE - CASHIER
               ======================================== */
            
            /* Core Palette */
            --electric-blue: #0D47A1;           /* Trust, authority, structure */
            --neon-blue: #2196F3;               /* Navigation, stability */
            --cyan-bright: #00E5FF;            /* Actions, primary interaction */
            --magenta: #E91E63;                /* Accent, attention */
            --violet: #9C27B0;                 /* Secondary accent */
            --lime-electric: #C6FF00;          /* Highlights */
            --slate-bg: #ECEFF1;               /* Background */
            --ice-white: #FAFBFC;              /* Card backgrounds */
            
            /* Status Colors */
            --success: #43A047;               /* Success states */
            --info: #2196F3;                  /* Info states */
            --warning: #C6FF00;               /* Warning background */
            --error: #E53935;                 /* Error states */
            
            /* Opacity Variants */
            --neon-blue-hover: rgba(33, 150, 243, 0.2);
            --cyan-hover: rgba(0, 229, 255, 0.08);
            --electric-blue-border: rgba(13, 71, 161, 0.15);
            
            /* Layout Colors */
            --color-app-bg: #ECEFF1;          /* Main background */
            --color-card-bg: #FAFBFC;         /* Card backgrounds */
            --color-divider: rgba(13, 71, 161, 0.15); /* Dividers */
            --color-text: #263238;            /* Primary text */
            --color-text-muted: #546E7A;      /* Muted text */
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            
            /* Sidebar Variables */
            --sidebar-bg: #0D47A1;            /* Sidebar background */
            --sidebar-icon-color: #FFFFFF;   /* Icons */
            --sidebar-icon-stroke: 1.6;
            --sidebar-active-bg: #2196F3;     /* Active states */
            --sidebar-hover-bg: #00E5FF;     /* Hover states */
            --sidebar-text: #FFFFFF;          /* Sidebar text */
        }
        
        /* Theme utility classes */
        .theme-bg { background-color: var(--neon-blue) !important; }
        .theme-border { border-color: var(--neon-blue) !important; }
        .theme-text { color: var(--neon-blue) !important; }
        
        /* Helper classes */
        .card-rounded { border-radius: 12px; }
        
        /* Global page background */
        body {
            background-color: var(--color-app-bg);
            color: var(--color-text);
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        .main-content { 
            background-color: transparent; 
            color: var(--color-text); 
        }
        
        /* ========================================
           BUTTON STYLING (Bootstrap Override)
           ======================================== */
        
        /* Primary Button - Neon Blue */
        .btn-primary {
            --bs-btn-bg: var(--neon-blue);
            --bs-btn-border-color: var(--neon-blue);
            --bs-btn-hover-bg: var(--cyan-bright);
            --bs-btn-hover-border-color: var(--cyan-bright);
            --bs-btn-active-bg: var(--cyan-bright);
            --bs-btn-active-border-color: var(--cyan-bright);
            --bs-btn-color: #FFFFFF;
        }
        
        /* Success Button */
        .btn-success {
            --bs-btn-bg: var(--success);
            --bs-btn-border-color: var(--success);
            --bs-btn-hover-bg: #388E3C;
            --bs-btn-hover-border-color: #388E3C;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Warning Button - Electric Blue */
        .btn-warning {
            --bs-btn-bg: var(--electric-blue);
            --bs-btn-border-color: var(--electric-blue);
            --bs-btn-hover-bg: #1565C0;
            --bs-btn-hover-border-color: #1565C0;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Info Button - Cyan Bright */
        .btn-info {
            --bs-btn-bg: var(--cyan-bright);
            --bs-btn-border-color: var(--cyan-bright);
            --bs-btn-hover-bg: #00ACC1;
            --bs-btn-hover-border-color: #00ACC1;
            --bs-btn-color: var(--electric-blue);
        }
        
        /* Danger Button */
        .btn-danger {
            --bs-btn-bg: var(--error);
            --bs-btn-border-color: var(--error);
            --bs-btn-hover-bg: #D32F2F;
            --bs-btn-hover-border-color: #D32F2F;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Secondary Button */
        .btn-secondary {
            --bs-btn-bg: #E5E7EB;
            --bs-btn-border-color: #E5E7EB;
            --bs-btn-color: var(--electric-blue);
            --bs-btn-hover-bg: #D1D5DB;
            --bs-btn-hover-border-color: #D1D5DB;
        }
        
        /* ========================================
           CARD STYLING
           ======================================== */
        
        .card {
            background-color: var(--color-card-bg);
            box-shadow: var(--card-shadow);
            border: 1px solid var(--color-divider);
            border-radius: 12px;
        }
        
        .card-header {
            background-color: var(--neon-blue);
            color: #FFFFFF;
            border-bottom: 1px solid var(--color-divider);
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* ========================================
           FORM CONTROLS
           ======================================== */
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--neon-blue);
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        
        .form-control,
        .form-select {
            border-color: var(--neon-blue);
        }
        
        /* ========================================
           TABLES
           ======================================== */
        
        .table thead {
            background-color: var(--electric-blue);
            color: #FFFFFF;
        }
        
        .table tbody tr:hover {
            background-color: var(--cyan-hover);
        }
        
        .table-bordered {
            border-color: var(--electric-blue-border);
        }
        
        /* ========================================
           BADGES
           ======================================== */
        
        .badge.bg-success {
            background-color: var(--success) !important;
        }
        
        .badge.bg-info {
            background-color: var(--neon-blue) !important;
        }
        
        .badge.bg-warning {
            background-color: var(--warning) !important;
            color: #1F2937 !important;
        }
        
        .badge.bg-danger {
            background-color: var(--error) !important;
        }
        
        .badge.bg-primary {
            background-color: var(--neon-blue) !important;
        }
        
        /* ========================================
           ALERTS
           ======================================== */
        
        .alert-success {
            background-color: #E8F5E8;
            border-color: var(--success);
            color: #2E7D32;
        }
        
        .alert-info {
            background-color: #E3F2FD;
            border-color: var(--neon-blue);
            color: #1565C0;
        }
        
        .alert-warning {
            background-color: #FFF9C4;
            border-color: var(--warning);
            color: #F57C00;
        }
        
        .alert-danger {
            background-color: #FFEBEE;
            border-color: var(--error);
            color: #C62828;
        }
        
        /* ========================================
           RESPONSIVE DESIGN
           ======================================== */
        
        /* Mobile (< 768px) */
        @media (max-width: 767.98px) {
            .main-content {
                padding: 1rem;
                margin-left: 0;
            }
            
            .btn {
                min-height: 44px;
                min-width: 44px;
                font-size: 14px;
            }
            
            .form-control, .form-select {
                min-height: 44px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            .modal-dialog {
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
        }
        
        /* Tablet (768px - 991px) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .btn {
                min-height: 40px;
                font-size: 14px;
            }
            
            .sidebar {
                width: 200px;
            }
        }
        
        /* Desktop (> 992px) */
        @media (min-width: 992px) {
            .main-content {
                padding: 2rem;
            }
        }
        
        /* Large desktop (> 1200px) */
        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1400px;
                margin: 0 auto;
            }
        }
        
        /* ========================================
           SELECT2 STYLING
           ======================================== */
        
        .select2-container--default .select2-selection--single {
            border-color: var(--neon-blue);
        }
        
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--neon-blue);
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--neon-blue);
        }
    </style>
    @stack('stylesDashboard')

</head>
<body class="min-h-screen font-sans">

    <div class="d-flex min-vh-100">
        {{-- Admin Sidebar with Cashier Context --}}
        <aside>
            @include('layouts.AdminSidebar')
        </aside>

        <main class="main-content flex-1">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize all Bootstrap collapse elements
            const collapseElements = document.querySelectorAll('.collapse');
            collapseElements.forEach(function (collapseElement) {
                new bootstrap.Collapse(collapseElement, {
                    toggle: false
                });
            });
        });
    </script>
</body>
</html>
