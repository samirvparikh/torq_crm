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
        <button type="button"
                class="crm-sidebar-backdrop"
                id="crm-sidebar-backdrop"
                aria-label="Close navigation menu"></button>

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
        const sidebar = document.getElementById('crm-sidebar');
        const backdrop = document.getElementById('crm-sidebar-backdrop');
        const mobileSidebar = window.matchMedia('(max-width: 768px)');

        const closeMobileSidebar = () => {
            document.body.classList.remove('sidebar-mobile-open');
            toggle?.setAttribute('aria-expanded', 'false');
        };

        toggle?.addEventListener('click', () => {
            if (mobileSidebar.matches) {
                const isOpen = document.body.classList.toggle('sidebar-mobile-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                return;
            }

            document.body.classList.toggle('sidebar-collapsed');
            toggle.setAttribute(
                'aria-expanded',
                document.body.classList.contains('sidebar-collapsed') ? 'false' : 'true'
            );
            try {
                localStorage.setItem(
                    'crm-sidebar-collapsed',
                    document.body.classList.contains('sidebar-collapsed') ? '1' : '0'
                );
            } catch (e) {}
        });

        if (!mobileSidebar.matches) {
            toggle?.setAttribute(
                'aria-expanded',
                document.body.classList.contains('sidebar-collapsed') ? 'false' : 'true'
            );
        }

        backdrop?.addEventListener('click', closeMobileSidebar);

        sidebar?.addEventListener('click', (event) => {
            if (mobileSidebar.matches && event.target.closest('a')) {
                closeMobileSidebar();
            }
        });

        document.querySelectorAll('.crm-nav-parent').forEach((parent) => {
            parent.addEventListener('click', () => {
                const group = parent.closest('.crm-nav-group');
                if (!group) return;

                if (!mobileSidebar.matches && document.body.classList.contains('sidebar-collapsed')) {
                    document.body.classList.remove('sidebar-collapsed');
                    try {
                        localStorage.setItem('crm-sidebar-collapsed', '0');
                    } catch (e) {}
                }

                const isOpen = group.classList.toggle('is-open');
                parent.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        mobileSidebar.addEventListener('change', closeMobileSidebar);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && document.body.classList.contains('sidebar-mobile-open')) {
                closeMobileSidebar();
                toggle?.focus();
            }
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
