<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SPK Decision Suite - AHP & TOPSIS')</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(109, 122, 114, 0.2); border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(109, 122, 114, 0.4); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-sans text-on-surface antialiased min-h-screen">
    <!-- Sidebar Navigation -->
    @include('layouts.sidebar')

    <!-- Main Content Shell -->
    <div class="pl-24 flex flex-col min-h-screen">
        <!-- Minimalist Navbar -->
        @include('layouts.navbar')

        <!-- Page Content -->
        <main class="pt-16 w-full flex-1">
            @yield('content')
        </main>

        <!-- Minimal Footer -->
        <footer class="py-4 px-gutter text-center text-on-surface-variant font-body-sm text-[11px] border-t border-outline-variant/10">
            Sistem Pendukung Keputusan (SPK) Multi-Kriteria &bull; Metode AHP & TOPSIS &copy; {{ date('Y') }}
        </footer>
    </div>
</body>
</html>

