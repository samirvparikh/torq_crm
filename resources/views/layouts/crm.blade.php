<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.jpg') }}" type="image/jpeg">
    <link rel="apple-touch-icon" href="{{ asset('images/torq.jpg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="crm-app-body">
    <script>
        (function () {
            try {
                if (localStorage.getItem('crm-sidebar-collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    @include('partials.crm.header')

    <div class="crm-app-shell">
        @include('partials.crm.sidebar')

        <main class="crm-main">
            @hasSection('breadcrumb')
                <nav class="crm-breadcrumb">@yield('breadcrumb')</nav>
            @endif

            @hasSection('toolbar')
                <div class="crm-page-toolbar">@yield('toolbar')</div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
    function toggleFullscreen() {
        if (!document.fullscreenElement) document.documentElement.requestFullscreen();
        else document.exitFullscreen();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('crm-sidebar-toggle');
        toggle?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem(
                    'crm-sidebar-collapsed',
                    document.body.classList.contains('sidebar-collapsed') ? '1' : '0'
                );
            } catch (e) {}
        });
    });

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            document.getElementById('crm-quick-find')?.focus();
        }
    });
    </script>
</body>
</html>
