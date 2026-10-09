<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Stok Opname BPJS')</title>

    {{-- BOOTSTRAP --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- BOOTSTRAP ICONS --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <style>
        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: #f5f7fb;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #212529;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            height: 100dvh;

            display: flex;
            flex-direction: column;

            background: #151b2d;
            color: #ffffff;

            overflow-y: auto;
            z-index: 1030;

            transition: transform .25s ease;
        }

        /* =====================================================
           SIDEBAR BRAND
        ===================================================== */

        .sidebar-brand {
            position: relative;

            min-height: 75px;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 15px 55px 15px 20px;

            border-bottom: 1px solid rgba(255, 255, 255, .08);

            flex-shrink: 0;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
            color: #ffffff;
        }

        .brand-subtitle {
            margin-top: 4px;
            font-size: 11px;
            color: rgba(255, 255, 255, .55);
            line-height: 1.3;
        }

        /* =====================================================
           TOMBOL TUTUP SIDEBAR
        ===================================================== */

        .sidebar-close-btn {
            display: none;

            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);

            width: 34px;
            height: 34px;

            align-items: center;
            justify-content: center;

            padding: 0;

            border: 0;
            border-radius: 7px;

            background: rgba(255, 255, 255, .1);
            color: #ffffff;

            font-size: 27px;
            line-height: 1;

            cursor: pointer;

            transition: background-color .2s ease;
        }

        .sidebar-close-btn:hover {
            background: rgba(255, 255, 255, .2);
        }

        /* =====================================================
           SIDEBAR USER
        ===================================================== */

        .sidebar-user {
            padding: 15px 20px;

            border-bottom: 1px solid rgba(255, 255, 255, .08);

            flex-shrink: 0;
        }

        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #ffffff;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            margin-top: 3px;
            font-size: 11px;
            color: rgba(255, 255, 255, .5);
        }

        /* =====================================================
           SIDEBAR NAVIGATION
        ===================================================== */

        .sidebar-nav {
            flex: 1;
            padding: 15px 12px;
            overflow-y: auto;
        }

        /* =====================================================
           SIDEBAR DIRECT LINK
        ===================================================== */

        .sidebar-link {
            width: 100%;

            display: flex;
            align-items: center;

            padding: 10px 12px;
            margin-bottom: 3px;

            border-radius: 7px;

            color: rgba(255, 255, 255, .72);
            text-decoration: none;

            font-size: 14px;
            font-weight: 400;

            transition:
                background-color .2s ease,
                color .2s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .08);
        }

        .sidebar-link.active {
            color: #ffffff;
            background: #0d6efd;
            font-weight: 500;
        }

        /* =====================================================
           SIDEBAR DROPDOWN
        ===================================================== */

        .sidebar-dropdown {
            margin-bottom: 3px;
        }

        .sidebar-dropdown-toggle {
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 10px 12px;

            border: 0;
            border-radius: 7px;

            background: transparent;
            color: rgba(255, 255, 255, .72);

            font-family: inherit;
            font-size: 14px;
            font-weight: 400;
            text-align: left;

            cursor: pointer;

            transition:
                background-color .2s ease,
                color .2s ease;
        }

        .sidebar-dropdown-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .08);
        }

        .sidebar-dropdown-toggle.active {
            color: #ffffff;
            background: rgba(255, 255, 255, .08);
            font-weight: 500;
        }

        /* =====================================================
           DROPDOWN CHEVRON
        ===================================================== */

        .sidebar-chevron {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            width: 18px;
            height: 18px;

            color: rgba(255, 255, 255, .5);
            font-size: 20px;
            line-height: 1;

            transform: rotate(0deg);

            transition:
                transform .2s ease,
                color .2s ease;
        }

        .sidebar-dropdown-toggle:hover .sidebar-chevron {
            color: #ffffff;
        }

        .sidebar-dropdown-toggle[aria-expanded="true"]
        .sidebar-chevron {
            transform: rotate(90deg);
            color: #ffffff;
        }

        /* =====================================================
           SUBMENU
        ===================================================== */

        .sidebar-submenu {
            padding: 4px 0 4px 12px;
            margin-left: 8px;

            border-left: 1px solid rgba(255, 255, 255, .1);
        }

        /* =====================================================
           SUBMENU LINK
        ===================================================== */

        .sidebar-sublink {
            display: flex;
            align-items: center;

            min-height: 38px;

            padding: 8px 12px;
            margin-bottom: 2px;

            border-radius: 6px;

            color: rgba(255, 255, 255, .6);
            text-decoration: none;

            font-size: 13px;

            transition:
                background-color .2s ease,
                color .2s ease;
        }

        .sidebar-sublink:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .06);
        }

        .sidebar-sublink.active {
            color: #ffffff;
            background: #0d6efd;
            font-weight: 500;
        }

        /* =====================================================
           SIDEBAR LOGOUT
        ===================================================== */

        .sidebar-logout {
            padding: 15px;

            border-top: 1px solid rgba(255, 255, 255, .08);

            flex-shrink: 0;
        }

        .sidebar-logout form {
            margin: 0;
        }

        .sidebar-logout-button {
            width: 100%;

            padding: 9px 12px;

            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 7px;

            background: transparent;
            color: rgba(255, 255, 255, .7);

            font-family: inherit;
            font-size: 13px;
            text-align: left;

            cursor: pointer;

            transition:
                background-color .2s ease,
                color .2s ease,
                border-color .2s ease;
        }

        .sidebar-logout-button:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, .08);
            border-color: rgba(255, 255, 255, .2);
        }

        /* =====================================================
           MAIN AREA
        ===================================================== */

        .main-wrapper {
            min-height: 100vh;
            margin-left: 250px;
        }

        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {
            height: 70px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 25px;

            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
        }

        /* =====================================================
           PAGE TITLE
        ===================================================== */

        .page-title {
            font-size: 18px;
            font-weight: 600;
            line-height: 1.2;
            color: #212529;
        }

        .page-breadcrumb {
            margin-top: 3px;
            font-size: 12px;
            color: #6c757d;
        }

        /* =====================================================
           TOPBAR USER
        ===================================================== */

        .user-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #e7f1ff;
            color: #0d6efd;

            font-size: 14px;
            font-weight: 700;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.2;
            color: #212529;
        }

        .user-role {
            margin-top: 2px;
            font-size: 11px;
            color: #6c757d;
        }

        /* =====================================================
           CONTENT
        ===================================================== */

        .content-wrapper {
            padding: 25px;
        }

        .content-card {
            background: #ffffff;
            border: 0;
            border-radius: 10px;
            padding: 20px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        /* =====================================================
           MOBILE MENU BUTTON
        ===================================================== */

        .mobile-menu-btn {
            display: none;

            width: 38px;
            height: 38px;

            align-items: center;
            justify-content: center;

            padding: 0;

            border: 1px solid #dee2e6;
            border-radius: 7px;

            background: #ffffff;
            color: #495057;
        }

        .mobile-menu-btn:hover {
            background: #f8f9fa;
            border-color: #ced4da;
        }

        /* =====================================================
           SIDEBAR SCROLLBAR
        ===================================================== */

        .sidebar::-webkit-scrollbar,
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track,
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .15);
            border-radius: 10px;
        }

        /* =====================================================
           OVERLAY
        ===================================================== */

        .sidebar-overlay {
            display: none;
        }

        /* =====================================================
           MOBILE & TABLET
        ===================================================== */

        @media (max-width: 991.98px) {
            .sidebar {
                z-index: 1050;

                transform: translateX(-100%);

                box-shadow: 5px 0 20px rgba(0, 0, 0, .15);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-close-btn {
                display: flex;
            }

            .main-wrapper {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: inline-flex !important;
            }

            .content-wrapper {
                padding: 15px;
            }

            .topbar {
                height: 65px;
                padding: 0 15px;
            }

            .page-title {
                font-size: 16px;
            }

            .sidebar-overlay.active {
                display: block;

                position: fixed;
                inset: 0;

                background: rgba(0, 0, 0, .45);

                z-index: 1040;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }

        /* =====================================================
           SMALL MOBILE
        ===================================================== */

        @media (max-width: 575.98px) {
            .topbar {
                padding: 0 12px;
            }

            .content-wrapper {
                padding: 12px;
            }

            .user-avatar {
                width: 34px;
                height: 34px;
                font-size: 13px;
            }
        }

        /* Kurangi animasi jika pengguna memilih reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .sidebar {
                transition: none;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- SIDEBAR --}}
    @include('layouts.sidebar')

    {{-- OVERLAY MOBILE --}}
    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
        aria-hidden="true"
    ></div>

    {{-- MAIN WRAPPER --}}
    <div class="main-wrapper">

        {{-- TOPBAR --}}
        <header class="topbar">

            <div class="d-flex align-items-center gap-3">

                {{-- MOBILE MENU --}}
                <button
                    type="button"
                    class="btn mobile-menu-btn"
                    id="mobileMenuButton"
                    aria-label="Buka menu"
                    aria-controls="sidebar"
                    aria-expanded="false"
                >
                    <i class="bi bi-list fs-5"></i>
                </button>

                {{-- PAGE TITLE --}}
                <div>
                    <div class="page-title">
                        @yield('page-title', 'Dashboard')
                    </div>

                    <div class="page-breadcrumb">
                        Sistem Stok Opname
                    </div>
                </div>

            </div>

            {{-- USER TOPBAR --}}
            @auth
                <div class="user-area">

                    <div class="text-end d-none d-sm-block">
                        <div class="user-name">
                            {{ auth()->user()->name }}
                        </div>

                        <div class="user-role">
                            {{ auth()->user()->role?->name ?? 'User' }}
                        </div>
                    </div>

                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                </div>
            @endauth

        </header>

        {{-- CONTENT --}}
        <main class="content-wrapper">
            @yield('content')
        </main>

    </div>

    {{-- BOOTSTRAP JS --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    {{-- MOBILE SIDEBAR --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('mobileMenuButton');
            const closeButton = document.getElementById('sidebarCloseBtn');
            const overlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !menuButton || !overlay) {
                return;
            }

            function isMobile() {
                return window.innerWidth <= 991.98;
            }

            function openSidebar() {
                if (!isMobile()) {
                    return;
                }

                sidebar.classList.add('show');
                overlay.classList.add('active');
                document.body.classList.add('sidebar-open');

                menuButton.setAttribute('aria-expanded', 'true');
                menuButton.setAttribute('aria-label', 'Tutup menu');
                overlay.setAttribute('aria-hidden', 'false');
            }

            function closeSidebar() {
                sidebar.classList.remove('show');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');

                menuButton.setAttribute('aria-expanded', 'false');
                menuButton.setAttribute('aria-label', 'Buka menu');
                overlay.setAttribute('aria-hidden', 'true');
            }

            menuButton.addEventListener('click', function () {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            if (closeButton) {
                closeButton.addEventListener('click', closeSidebar);
            }

            // Tutup saat area di luar sidebar disentuh.
            overlay.addEventListener('click', closeSidebar);

            // Tutup setelah memilih menu atau submenu.
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (isMobile()) {
                        closeSidebar();
                    }
                });
            });

            // Tutup dengan tombol Escape.
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && isMobile()) {
                    closeSidebar();
                }
            });

            // Reset status sidebar ketika kembali ke desktop.
            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')

</body>
</html>
