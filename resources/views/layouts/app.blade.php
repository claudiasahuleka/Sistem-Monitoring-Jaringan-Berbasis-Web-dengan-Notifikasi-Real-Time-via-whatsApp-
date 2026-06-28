<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Monitoring') – DISNAKERTRANS Maluku</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        #sidebar {
            width: 260px;
            min-height: 100vh;
            background: #0f172a;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        /* ===== SIDEBAR BRAND ===== */
        .sb-brand {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .sb-brand-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sb-logo-img {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            object-fit: contain;
            background: #ffffff;
            padding: 3px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
        }

        .sb-logo-text-wrap {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sb-logo-title {
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.35;
            letter-spacing: 0.3px;
        }

        .sb-logo-sub {
            color: rgba(255, 255, 255, 0.35);
            font-size: 9.5px;
            letter-spacing: 0.5px;
        }

        .sb-section {
            padding: 14px 16px 4px;
            color: rgba(255, 255, 255, 0.25);
            font-size: 9.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .sb-nav {
            padding: 0 8px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sb-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 9px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .sb-item:hover {
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.9);
        }

        .sb-item.active {
            background: rgba(59, 130, 246, 0.18);
            color: #60a5fa;
        }

        .sb-item i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sb-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 999px;
        }

        .sb-badge-amber {
            background: #f59e0b !important;
        }

        .sb-footer {
            margin-top: auto;
            padding: 14px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sb-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sb-user-name {
            color: rgba(255, 255, 255, 0.85);
            font-size: 12px;
            font-weight: 600;
        }

        .sb-user-role {
            color: rgba(255, 255, 255, 0.3);
            font-size: 10px;
        }

        .sb-logout {
            margin-left: auto;
            color: rgba(255, 255, 255, 0.25);
            font-size: 16px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.2s;
        }

        .sb-logout:hover {
            color: #f87171;
        }

        /* ===== MAIN AREA ===== */
        #main-wrap {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            height: 56px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .topbar-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .topbar-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #dcfce7;
            color: #15803d;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            letter-spacing: 0.3px;
        }

        .pulse {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: pulse-anim 1.5s infinite;
        }

        @keyframes pulse-anim {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
        }

        .topbar-clock {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 7px;
            font-variant-numeric: tabular-nums;
        }

        .topbar-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #f1f5f9;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .topbar-icon-btn:hover {
            background: #e2e8f0;
        }

        /* ===== CONTENT ===== */
        .main-content {
            padding: 24px;
            flex: 1;
        }

        /* ===== ALERTS ===== */
        .alert-modern {
            border: none;
            border-radius: 10px;
            border-left: 4px solid;
            font-size: 13px;
        }

        .alert-modern.alert-success {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #15803d;
        }

        .alert-modern.alert-danger {
            background: #fef2f2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        /* ===== FOOTER ===== */
        footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 14px 24px;
        }

        /* Responsive sidebar toggle */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #main-wrap {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar">
        {{-- Brand --}}
        <div class="sb-brand">
            <div class="sb-brand-inner">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Maluku" class="sb-logo-img">
                <div class="sb-logo-text-block">
                    <div class="sb-logo-title">DISNAKERTRANS<br>PROVINSI MALUKU</div>
                    <div class="sb-logo-sub">Sistem Monitoring Jaringan</div>
                </div>
            </div>
        </div>

        {{-- Menu --}}
        <div class="mt-1">
            <div class="sb-section">Menu Utama</div>
            <div class="sb-nav">
                <a href="{{ route('dashboard') }}"
                    class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>

                <a href="{{ route('devices.index') }}"
                    class="sb-item {{ request()->routeIs('devices.*') ? 'active' : '' }}">
                    <i class="bi bi-hdd-network"></i>
                    Perangkat
                </a>

                <a href="{{ route('logs.index') }}" class="sb-item {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    Log Historis
                </a>

                <a href="{{ route('reports.index') }}"
                    class="sb-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    Laporan
                </a>
            </div>
        </div>

        {{-- User Footer --}}
        <div class="sb-footer">
            <div class="sb-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="sb-user-name">{{ Auth::user()->name }}</div>
                <div class="sb-user-role">Administrator</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="sb-logout" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div id="main-wrap">

        {{-- Topbar --}}
        <div class="topbar">
            {{-- Mobile toggle --}}
            <button class="topbar-icon-btn d-md-none me-1"
                onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>

            <div class="topbar-title">
                <i class="bi bi-activity"></i>
                @yield('title', 'Dashboard')
                <span class="topbar-pill d-none d-sm-flex">
                    <span class="pulse"></span> Monitoring Aktif
                </span>
            </div>

            <div class="topbar-clock d-none d-lg-block">
                <i class="bi bi-clock me-1"></i>
                <span id="nav-last-check">{{ now()->format('H:i:s') }}</span>
            </div>

            <button class="topbar-icon-btn">
                <i class="bi bi-bell"></i>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-modern alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-modern alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <footer class="text-center text-muted" style="font-size:11px">
            &copy; {{ date('Y') }} DISNAKERTRANS Provinsi Maluku &mdash; Sistem Monitoring Jaringan Terintegrasi
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.csrfPost = async function (url) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            }).then(r => r.json());
        };
        setInterval(() => {
            const el = document.getElementById('nav-last-check');
            if (!el) return;
            const n = new Date();
            el.textContent =
                String(n.getHours()).padStart(2, '0') + ':' +
                String(n.getMinutes()).padStart(2, '0') + ':' +
                String(n.getSeconds()).padStart(2, '0');
        }, 1000);
    </script>
    @stack('scripts')
</body>

</html>