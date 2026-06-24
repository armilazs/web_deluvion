<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DL-VN | Flood Monitoring System</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .mobile-menu-btn {
            display: none;
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 12px;
            background: #eff6ff;
            color: var(--primary-blue);
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 998;
        }

        .sidebar-backdrop.active {
            display: block;
        }

        .desktop-only {
            display: inline-flex;
        }

        @media (max-width: 1024px) {
            body {
                overflow-x: hidden;
            }

            .mobile-menu-btn {
                display: inline-flex;
            }

            .desktop-only {
                display: none !important;
            }

            .sidebar {
                position: fixed !important;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 12px 0 30px rgba(15, 23, 42, 0.18);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                min-height: 100vh;
            }

            .topbar {
                height: auto !important;
                min-height: 78px;
                padding: 18px 18px 12px !important;
                gap: 12px;
                align-items: flex-start;
            }

            .greeting {
                flex: 1;
                min-width: 0;
            }

            .greeting p {
                font-size: 12px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .greeting h1 {
                font-size: 22px;
                line-height: 1.2;
            }

            .topbar-right {
                gap: 8px;
            }

            .notification-btn {
                width: 42px;
                height: 42px;
                flex-shrink: 0;
            }

            .user-profile {
                padding: 6px 8px;
                min-width: auto;
            }

            .user-profile .user-info {
                display: none;
            }

            .content-area {
                padding: 16px !important;
            }

            .dashboard-grid {
                grid-template-columns: 1fr !important;
            }

            .widget-card,
            .chart-container,
            .logs-card {
                border-radius: 14px !important;
            }

            table {
                min-width: 720px;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 14px 14px 10px !important;
            }

            .greeting h1 {
                font-size: 20px;
            }

            .content-area {
                padding: 12px !important;
            }

            .avatar {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .dropdown-menu {
                right: 0;
                min-width: 190px;
            }
        }
    </style>
</head>

<body>
    @php
    $adminEmail = session('firebase_email', 'Admin');
    $adminName = explode('@', $adminEmail)[0] ?? 'Admin';

    $words = preg_split('/[\s._-]+/', $adminName);
    $initials = '';

    foreach ($words as $word) {
    if (!empty($word)) {
    $initials .= strtoupper(substr($word, 0, 1));
    }

    if (strlen($initials) >= 2) {
    break;
    }
    }

    if ($initials === '') {
    $initials = 'AD';
    }
    @endphp

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            DL&middot;VN
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('monitoring') }}" class="nav-item {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Monitoring
            </a>

            <a href="{{ route('maintenance') }}" class="nav-item {{ request()->routeIs('maintenance') ? 'active' : '' }}">
                <i class="far fa-calendar-alt"></i> Jadwal Pemeliharaan
            </a>

            <a href="{{ route('devices') }}" class="nav-item {{ request()->routeIs('devices') ? 'active' : '' }}">
                <i class="fas fa-microchip"></i> Perangkat
            </a>

            <a href="{{ route('qos') }}" class="nav-item {{ request()->routeIs('qos') ? 'active' : '' }}">
                <i class="fas fa-network-wired"></i> Kualitas Jaringan
            </a>

            <a href="{{ route('logs') }}" class="nav-item {{ request()->routeIs('logs') ? 'active' : '' }}">
                <i class="far fa-comment-dots"></i> Aktivitas Log
            </a>

            <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
        </nav>

        <div class="sidebar-footer">
            <div style="font-size: 10px; font-weight: bold; color: var(--primary-blue); margin-bottom: 8px;">
                SISTEM LOG
            </div>

            <button class="export-btn" type="button">
                Ekspor Data
                <i class="fas fa-arrow-right" style="color: var(--primary-blue); background: #e0e7ff; padding: 4px; border-radius: 4px;"></i>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar" style="padding-top: 24px; height: 96px;">
            <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Buka menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="greeting">
                <p>Hello {{ e($adminName) }}, welcome back!</p>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>

            <div class="topbar-right">
                <div class="notification-btn">
                    <i class="far fa-bell"></i>
                    <div class="notification-dot"></div>
                </div>

                <div class="user-profile" onclick="toggleDropdown(event)">
                    <div class="avatar">{{ e($initials) }}</div>

                    <div class="user-info">
                        <div class="name">{{ e($adminName) }}</div>
                        <div class="role">Pengelola</div>
                    </div>

                    <i class="fas fa-chevron-down desktop-only" style="color: var(--text-secondary); font-size: 12px;"></i>

                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu" id="profileDropdown">
                        <a href="{{ route('settings') }}" class="dropdown-item">
                            <i class="fas fa-user"></i> Profil Admin
                        </a>

                        <a href="{{ route('settings') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i> Pengaturan
                        </a>

                        <hr style="margin: 0; border: none; border-top: 1px solid var(--border-color);">

                        <form method="POST" action="{{ route('logout') }}" id="logoutForm" style="margin: 0;">
                            @csrf

                            <button type="submit" class="dropdown-item text-danger" style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <!-- Global Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script>
        const sidebar = document.getElementById("sidebar");
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const sidebarBackdrop = document.getElementById("sidebarBackdrop");

        function openSidebar() {
            if (sidebar) {
                sidebar.classList.add("mobile-open");
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add("active");
            }

            document.body.style.overflow = "hidden";
        }

        function closeSidebar() {
            if (sidebar) {
                sidebar.classList.remove("mobile-open");
            }

            if (sidebarBackdrop) {
                sidebarBackdrop.classList.remove("active");
            }

            document.body.style.overflow = "";
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (sidebar && sidebar.classList.contains("mobile-open")) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener("click", closeSidebar);
        }

        document.querySelectorAll(".sidebar .nav-item").forEach(function(item) {
            item.addEventListener("click", function() {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener("resize", function() {
            if (window.innerWidth > 1024) {
                closeSidebar();
            }
        });

        // Global Toast Function
        function showToast(message, type = "success") {
            const container = document.getElementById("toastContainer");

            if (!container) {
                return;
            }

            const toast = document.createElement("div");
            toast.className = "toast";

            const iconClass = type === "success" ? "check-circle" : "info-circle";
            const iconColor = type === "success" ? "var(--success-color)" : "var(--primary-blue)";

            toast.innerHTML = `
                <i class="fas fa-${iconClass}" style="color: ${iconColor}"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => toast.classList.add("show"), 10);

            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        const exportButton = document.querySelector(".export-btn");

        if (exportButton) {
            exportButton.addEventListener("click", function(e) {
                e.preventDefault();
                showToast("Sistem sedang mengekspor data, mohon tunggu...");
            });
        }

        const notificationButton = document.querySelector(".notification-btn");

        if (notificationButton) {
            notificationButton.addEventListener("click", function(e) {
                e.preventDefault();
                showToast("Tidak ada notifikasi baru saat ini.", "info");
            });
        }

        function toggleDropdown(event) {
            if (event) {
                event.stopPropagation();
            }

            const dropdown = document.getElementById("profileDropdown");

            if (dropdown) {
                dropdown.classList.toggle("active");
            }
        }

        window.onclick = function(event) {
            if (!event.target.closest(".user-profile")) {
                const dropdowns = document.getElementsByClassName("dropdown-menu");

                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];

                    if (openDropdown.classList.contains("active")) {
                        openDropdown.classList.remove("active");
                    }
                }
            }
        };

        document.addEventListener("keydown", function(event) {
            if (event.key === "Escape") {
                closeSidebar();

                const dropdown = document.getElementById("profileDropdown");
                if (dropdown) {
                    dropdown.classList.remove("active");
                }
            }
        });
    </script>
</body>

</html>