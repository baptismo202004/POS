<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'POS System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind Play CDN (for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-color) !important; }
        /* small helper to mix Bootstrap and tailwind spacing */
        .card-rounded{ border-radius: 12px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans">

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-64">
            @include('layouts.AdminSidebar')
        </aside>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>