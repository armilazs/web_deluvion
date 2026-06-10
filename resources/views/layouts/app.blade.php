<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DL-VN | Flood Monitoring System</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
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
            <div style="font-size: 10px; font-weight: bold; color: var(--primary-blue); margin-bottom: 8px;">SISTEM LOG</div>
            <button class="export-btn">
                Ekspor Data
                <i class="fas fa-arrow-right" style="color: var(--primary-blue); background: #e0e7ff; padding: 4px; border-radius: 4px;"></i>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar" style="padding-top: 24px; height: 96px;">
            <div class="greeting">
                <p>Hello rrrr, welcome back!</p>
                <h1>@yield('title', 'Dashboard')</h1>
            </div>
            <div class="topbar-right">
                <div class="notification-btn">
                    <i class="far fa-bell"></i>
                    <div class="notification-dot"></div>
                </div>
                <div class="user-profile" onclick="toggleDropdown()">
                    <div class="avatar">rr</div>
                    <div class="user-info">
                        <div class="name">rrrr</div>
                        <div class="role">Pengelola</div>
                    </div>
                    <i class="fas fa-chevron-down" style="color: var(--text-secondary); font-size: 12px;"></i>
                    
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu" id="profileDropdown">
                        <a href="{{ route('settings') }}" class="dropdown-item">
                            <i class="fas fa-user"></i> Profil Admin
                        </a>
                        <a href="{{ route('settings') }}" class="dropdown-item">
                            <i class="fas fa-cog"></i> Pengaturan
                        </a>
                        <hr style="margin: 0; border: none; border-top: 1px solid var(--border-color);">
                        <a href="{{ route('login') }}" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </a>
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
        // Global Toast Function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}" style="color: ${type === 'success' ? 'var(--success-color)' : 'var(--primary-blue)'}"></i> <span>${message}</span>`;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            // Remove after 3s
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Make Export button interactive
        document.querySelector('.export-btn').addEventListener('click', function(e) {
            e.preventDefault();
            showToast('Sistem sedang mengekspor data, mohon tunggu...');
        });
        
        // Make Notifications interactive
        document.querySelector('.notification-btn').addEventListener('click', function(e) {
            e.preventDefault();
            showToast('Tidak ada notifikasi baru saat ini.', 'info');
        });

        // Dropdown Toggle
        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('active');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.closest('.user-profile')) {
                const dropdowns = document.getElementsByClassName("dropdown-menu");
                for (let i = 0; i < dropdowns.length; i++) {
                    const openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('active')) {
                        openDropdown.classList.remove('active');
                    }
                }
            }
        }
    </script>
</body>
</html>
