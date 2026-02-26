<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <!-- Google Fonts - Inter for electronic palette -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <title><?php echo e(config('app.name', 'POS System')); ?></title>
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Animate.css for SweetAlert animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldContent('head'); ?>

    <style>
        :root { 
            /* ========================================
               NEW COLOR PALETTE
               ======================================== */
            
            /* Core Palette */
            --deep-blue: #05668D;           /* Trust, authority, structure */
            --blue-teal: #028090;           /* Navigation, stability */
            --teal: #00A896;                /* Actions, primary interaction */
            --mint-green: #02C39A;          /* Success, positive states */
            --soft-yellow: #F0F3BD;         /* Background, highlights, calm contrast */
            
            /* Status Colors */
            --success: #02C39A;             /* Mint Green - Success states */
            --info: #028090;                /* Blue-Teal - Info states */
            --warning: #F0F3BD;             /* Soft Yellow - Warning background */
            --error: #E63946;               /* Red - Error states */
            
            /* Opacity Variants */
            --blue-teal-hover: rgba(2, 128, 144, 0.2);
            --teal-hover: rgba(0, 168, 150, 0.08);
            --deep-blue-border: rgba(5, 102, 141, 0.15);
            
            /* Layout Colors */
            --color-app-bg: #F0F3BD;        /* Soft Yellow - Main background */
            --color-card-bg: #FFFFFF;       /* White - Card backgrounds */
            --color-divider: rgba(5, 102, 141, 0.15); /* Deep Blue - Dividers */
            --color-text: #05668D;          /* Deep Blue - Primary text */
            --color-text-muted: #6B7280;    /* Gray - Muted text */
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            
            /* Sidebar Variables */
            --sidebar-bg: #05668D;          /* Deep Blue - Sidebar background */
            --sidebar-icon-color: #FFFFFF;  /* White - Icons */
            --sidebar-icon-stroke: 1.6;
            --sidebar-active-bg: #028090;   /* Blue-Teal - Active states */
            --sidebar-hover-bg: #00A896;    /* Teal - Hover states */
            --sidebar-text: #FFFFFF;        /* White - Sidebar text */
            
            /* Legacy mappings for backward compatibility */
            --theme-color: #00A896;         /* Teal - Primary theme color */
            --color-soft-orange: #00A896;   /* Mapped to Teal */
            --color-deep-navy: #05668D;     /* Mapped to Deep Blue */
            --color-blue: #028090;          /* Mapped to Blue-Teal */
            --color-blue-hover: #05668D;    /* Mapped to Deep Blue */
            --color-teal: #00A896;          /* Teal */
            --color-yellow: #F0F3BD;        /* Soft Yellow */
            --color-red: #E63946;           /* Error Red */
        }
        
        /* Theme utility classes */
        .theme-bg { background-color: var(--teal) !important; }
        .theme-border { border-color: var(--teal) !important; }
        .theme-text { color: var(--teal) !important; }
        
        /* Helper classes */
        .card-rounded { border-radius: 12px; }
        
        /* Global page background */
        body {
            background-color: var(--color-app-bg); /* Soft Yellow */
            color: var(--color-text); /* Deep Blue */
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        
        .main-content { 
            background-color: transparent; 
            color: var(--color-text); 
            overflow-y: auto;
            margin-left: 260px;
            padding: 1.5rem;
            min-height: 100vh;
        }
        
        .sidebar-fixed {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            /* Hide scrollbar visually but keep functionality */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        
        .sidebar-fixed::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        
        /* Add padding to main content to account for fixed sidebar */
        /* Note: margin-left is already set in .main-content */
        
        /* ========================================
           BUTTON STYLING (Bootstrap Override)
           ======================================== */
        
        /* Primary Button - Teal for primary interactions */
        .btn-primary {
            --bs-btn-bg: var(--teal);                    /* #00A896 */
            --bs-btn-border-color: var(--teal);
            --bs-btn-hover-bg: var(--mint-green);        /* #02C39A */
            --bs-btn-hover-border-color: var(--mint-green);
            --bs-btn-active-bg: var(--mint-green);
            --bs-btn-active-border-color: var(--mint-green);
            --bs-btn-color: #FFFFFF;
        }
        
        /* Success Button - Mint Green */
        .btn-success {
            --bs-btn-bg: var(--mint-green);              /* #02C39A */
            --bs-btn-border-color: var(--mint-green);
            --bs-btn-hover-bg: #029C7B;
            --bs-btn-hover-border-color: #029C7B;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Warning Button - Deep Blue (authority) */
        .btn-warning {
            --bs-btn-bg: var(--deep-blue);               /* #05668D */
            --bs-btn-border-color: var(--deep-blue);
            --bs-btn-hover-bg: #045271;
            --bs-btn-hover-border-color: #045271;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Info Button - Blue-Teal */
        .btn-info {
            --bs-btn-bg: var(--blue-teal);               /* #028090 */
            --bs-btn-border-color: var(--blue-teal);
            --bs-btn-hover-bg: #026673;
            --bs-btn-hover-border-color: #026673;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Danger Button - Error Red */
        .btn-danger {
            --bs-btn-bg: var(--error);                   /* #E63946 */
            --bs-btn-border-color: var(--error);
            --bs-btn-hover-bg: #DC2626;
            --bs-btn-hover-border-color: #DC2626;
            --bs-btn-color: #FFFFFF;
        }
        
        /* Secondary Button - Light gray */
        .btn-secondary {
            --bs-btn-bg: #E5E7EB;
            --bs-btn-border-color: #E5E7EB;
            --bs-btn-color: var(--deep-blue);
            --bs-btn-hover-bg: #D1D5DB;
            --bs-btn-hover-border-color: #D1D5DB;
        }
        
        /* ========================================
           CARD STYLING
           ======================================== */
        
        .card {
            background-color: var(--color-card-bg); /* White */
            box-shadow: var(--card-shadow);
            border: 1px solid var(--color-divider);
            border-radius: 12px;
        }
        
        .card-header {
            background-color: var(--blue-teal);     /* Blue-Teal for headers */
            color: #FFFFFF;
            border-bottom: 1px solid var(--color-divider);
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* ========================================
           FORM CONTROLS
           ======================================== */
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--teal);
            box-shadow: 0 0 0 0.2rem rgba(0, 168, 150, 0.25);
        }
        
        .form-control,
        .form-select {
            border-color: var(--blue-teal);
        }
        
        /* ========================================
           TABLES
           ======================================== */
        
        .table thead {
            background-color: var(--deep-blue);
            color: #FFFFFF;
        }
        
        .table tbody tr:hover {
            background-color: var(--teal-hover);
        }
        
        .table-bordered {
            border-color: var(--deep-blue-border);
        }
        
        /* ========================================
           BADGES
           ======================================== */
        
        .badge.bg-success {
            background-color: var(--mint-green) !important;
        }
        
        .badge.bg-info {
            background-color: var(--blue-teal) !important;
        }
        
        .badge.bg-warning {
            background-color: #FACC15 !important;
            color: #1F2937 !important;
        }
        
        .badge.bg-danger {
            background-color: var(--error) !important;
        }
        
        .badge.bg-primary {
            background-color: var(--teal) !important;
        }
        
        /* ========================================
           ALERTS
           ======================================== */
        
        .alert-success {
            background-color: #E6FBF6;
            border-color: var(--mint-green);
            color: #01755C;
        }
        
        .alert-info {
            background-color: #E6F5F7;
            border-color: var(--blue-teal);
            color: #014D56;
        }
        
        .alert-warning {
            background-color: #FEFEF9;
            border-color: #E0E761;
            color: #B8C81A;
        }
        
        .alert-danger {
            background-color: #FEE2E2;
            border-color: var(--error);
            color: #991B1B;
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
            
            .sidebar-fixed {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar-fixed.show {
                transform: translateX(0);
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
            
            .sidebar-fixed {
                width: 260px;
            }
            
            .btn {
                min-height: 40px;
                font-size: 14px;
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
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .icon-badge {
                border-width: 0.5px;
            }
        }
        
        /* Reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
        
        /* Dark mode support preparation */
        @media (prefers-color-scheme: dark) {
            /* Future dark mode variables can be added here */
        }
        
        /* ========================================
           SELECT2 STYLING
           ======================================== */
        
        .select2-container--default .select2-selection--single {
            border-color: var(--blue-teal);
        }
        
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--teal);
            box-shadow: 0 0 0 0.2rem rgba(0, 168, 150, 0.25);
        }
        
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: var(--teal);
        }
    </style>
    <?php echo $__env->yieldPushContent('stylesDashboard'); ?>

</head>
<body class="min-h-screen font-sans" style="margin-top: 0; padding-top: 0;">

    <?php
        $user = auth()->user();
        $isCashier = $user && $user->userType && $user->userType->name === 'Cashier';
    ?>

    
    <?php if($isCashier): ?>
        <aside class="sidebar-fixed">
            <?php echo $__env->make('layouts.CashierSidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>
    <?php else: ?>
        <aside class="sidebar-fixed">
            <?php echo $__env->make('layouts.AdminSidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </aside>
    <?php endif; ?>

    <main class="main-content">
        <!-- Mobile menu toggle -->
        <button class="d-md-none btn btn-primary position-fixed" style="top: 1rem; left: 1rem; z-index: 1001;" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.querySelector('.sidebar-fixed');
            
            if (mobileMenuToggle && sidebar) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth < 768) {
                        if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

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
</html><?php /**PATH C:\xampp\htdocs\POS\resources\views/layouts/app.blade.php ENDPATH**/ ?>