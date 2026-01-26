<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS System')</title>
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
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-color) !important; }
        /* small helper to mix Bootstrap and tailwind spacing */
        .card-rounded{ border-radius: 12px; }
        /* Global page background */
        body {
            background:
                
                url('/images/BGH_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .main-content { background-color: transparent; color: #dde4ed; }
        

    </style>
    @stack('stylesDashboard')

</head>
<body class="min-h-screen font-sans">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        <aside>
            @include('layouts.AdminSidebar')
        </aside>

        <main class="main-content flex-1 p-6">
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